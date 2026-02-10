<?php

namespace App\Modules\User\Reviews\Services;

use App\Models\BranchQrSession;
use App\Models\Review;
use App\Models\ReviewAnswer;
use App\Models\ReviewAnswerPhoto;
use App\Models\ReviewPhoto;
use App\Models\RatingCriteria;
use App\Models\RatingCriteriaChoice;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Support\Traits\Media\PublicUploadTrait;
use App\Support\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;
use App\Modules\User\Reviews\Support\CriteriaResolver;
use App\Modules\User\Points\Services\PointsService;
use App\Models\Subscription;
use App\Models\SubscriptionSetting;

class ReviewService
{
    use PublicUploadTrait;

    public function createReview($user, string $sessionToken, $overallRating, $comment, array $answers, array $photos = [], array $answerPhotos = [])
    {
        // Load session scoped to user, not consumed
        $session = BranchQrSession::query()
            ->where('session_token', $sessionToken)
            ->where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->first();

        if (! $session) {
            throw new ApiException('reviews.invalid_qr', 422);
        }

        // Debug log (temporary)
        Log::debug('qr.create_attempt', ['token' => $sessionToken, 'found' => (bool) $session, 'expires_at' => optional($session->expires_at)->toDateTimeString(), 'now' => Carbon::now()->toDateTimeString(), 'consumed_at' => optional($session->consumed_at)->toDateTimeString()]);

        // Ensure expires_at is a proper Carbon instance and check expiry
        if (! $session->expires_at || $session->expires_at->isPast()) {
            throw new ApiException('reviews.qr_expired', 422, [
                'server_time' => Carbon::now()->toDateTimeString(),
                'expires_at' => optional($session->expires_at)->toDateTimeString(),
                'action' => 'RESCAN_REQUIRED',
            ]);
        }

        $branch = $session->branch;
        if (! $branch) {
            throw new ApiException(trans('reviews.invalid_qr'), 422);
        }

        $subscription = $this->ensureUserSubscription($user);
        if (! $this->hasSubscriptionAccess($subscription)) {
            $freeUntil = $this->resolveFreeUntil($subscription);
            $dateText = $freeUntil ? $freeUntil->format('Y-m-d') : null;
            $message = $dateText
                ? __('reviews.subscription_required_with_date', ['date' => $dateText])
                : __('reviews.subscription_required');
            throw new ApiException($message, 402, [
                'free_until' => $freeUntil?->toDateTimeString(),
                'days_left' => $freeUntil ? max(0, Carbon::now()->diffInDays($freeUntil, false)) : 0,
                'action' => 'SUBSCRIBE_REQUIRED',
            ]);
        }

        // Instrumentation: log branch and subcategory
        $subcat = optional(optional($branch)->brand)->subcategory_id;
        Log::debug('review.create.branch_info', ['branch_id' => $branch->id, 'place_id' => $branch->place_id, 'brand_id' => $branch->brand_id, 'subcategory_id' => $subcat]);

        // Cooldown check
        $cooldownDays = (int) $branch->review_cooldown_days;
        if ($cooldownDays > 0) {
            $last = Review::where('branch_id', $branch->id)->where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
            if ($last && Carbon::now()->diffInDays($last->created_at) < $cooldownDays) {
                $cooldownEndsAt = $last->created_at->copy()->addDays($cooldownDays);
                $remainingSeconds = max(0, Carbon::now()->diffInSeconds($cooldownEndsAt));
                $human = Carbon::now()->diffForHumans($cooldownEndsAt);
                throw new ApiException('reviews.cooldown_active', 429, [
                    'retry_after_seconds' => $remainingSeconds,
                    'retry_after_human' => $human,
                    'cooldown_ends_at' => $cooldownEndsAt->toDateTimeString(),
                ]);
            }
        }

        // Validate answers against criteria using CriteriaResolver (single source of truth)
        $resolver = app(CriteriaResolver::class);
        $criteriaCollection = $resolver->getForBranch($branch);
        $criteria = $criteriaCollection->keyBy('id');

        // Log allowed criteria ids (count + first 20)
        $allowedIds = $criteriaCollection->pluck('id')->toArray();
        Log::debug('review.create.allowed_criteria', ['count' => count($allowedIds), 'sample_ids' => array_slice($allowedIds, 0, 20)]);

        // Log incoming answers for debugging
        Log::debug('review.create.incoming_answers', ['answers' => $answers]);

        $required = $criteria->where('is_required', true)->pluck('id')->toArray();
        $providedIds = array_map('intval', array_column($answers, 'criteria_id'));
        foreach ($required as $rid) {
            if (! in_array((int) $rid, $providedIds, true)) {
                throw new ApiException(trans('reviews.invalid_answer'), 422);
            }
        }

        DB::beginTransaction();
        try {
            $review = Review::create([
                'user_id' => $user->id,
                'place_id' => $branch->place_id,
                'branch_id' => $branch->id,
                'overall_rating' => $overallRating,
                'comment' => $comment,
                'status' => 'ACTIVE',
            ]);

            $pointsFromAnswers = 0;
            foreach ($answers as $ans) {
                // Normalize incoming criteria id to int
                $criteriaId = (int) Arr::get($ans, 'criteria_id');
                $crit = $criteria->get($criteriaId);

                // Per-answer debug context
                $answerDebug = [
                    'criteria_id' => $criteriaId,
                    'incoming_keys' => array_keys((array) $ans),
                    'incoming_values' => $ans,
                    'criteria_resolved' => $crit ? $crit->type : null,
                    'subcategory_id' => $subcat,
                ];

                if (! $crit) {
                    Log::debug('review.create.invalid_criteria', $answerDebug + ['reason' => 'not_in_allowed_set']);
                    throw new ApiException('reviews.invalid_answer_for_criteria', 422, ['criteria_id' => $criteriaId]);
                }

                $data = ['review_id' => $review->id, 'criteria_id' => $criteriaId];
                switch ($crit->type) {
                    case 'RATING':
                        $raw = Arr::get($ans, 'rating_value');
                        $val = is_numeric($raw) ? (int) $raw : null;
                        Log::debug('review.create.answer_validation', $answerDebug + ['normalized_rating' => $val]);
                        if ($val === null || $val < 1 || $val > 5) {
                            Log::debug('review.create.invalid_answer', $answerDebug + ['reason' => 'rating_out_of_range', 'normalized' => $val]);
                            throw new ApiException(trans('reviews.invalid_answer'), 422, ['criteria_id' => $criteriaId, 'type' => 'RATING']);
                        }
                        $data['rating_value'] = $val;
                        break;
                    case 'YES_NO':
                        $raw = Arr::get($ans, 'yes_no_value');
                        // Accept "1"/"0", "true"/"false", booleans, ints
                        if (is_string($raw)) {
                            $low = strtolower($raw);
                            if ($low === '1' || $low === 'true') {
                                $boolVal = true;
                            } elseif ($low === '0' || $low === 'false') {
                                $boolVal = false;
                            } else {
                                $boolVal = null;
                            }
                        } elseif (is_int($raw) || is_bool($raw)) {
                            $boolVal = (bool) $raw;
                        } else {
                            $boolVal = null;
                        }

                        Log::debug('review.create.answer_validation', $answerDebug + ['normalized_yes_no' => $boolVal]);
                        if ($boolVal === null) {
                            Log::debug('review.create.invalid_answer', $answerDebug + ['reason' => 'invalid_yes_no_value', 'raw' => $raw]);
                            throw new ApiException(trans('reviews.invalid_answer'), 422, ['criteria_id' => $criteriaId, 'type' => 'YES_NO']);
                        }
                        $data['yes_no_value'] = (int) $boolVal;
                        break;
                    case 'MULTIPLE_CHOICE':
                        $choiceId = (int) Arr::get($ans, 'choice_id');
                        // Allowed choice ids from resolved criteria (if loaded) or DB fallback
                        $allowed = $crit && $crit->relationLoaded('choices') ? $crit->choices->pluck('id')->map(fn($i) => (int) $i)->toArray() : RatingCriteriaChoice::where('criteria_id', $criteriaId)->pluck('id')->map(fn($i) => (int) $i)->toArray();

                        Log::debug('review.create.answer_validation', $answerDebug + ['allowed_choice_ids' => $allowed, 'incoming_choice_id' => $choiceId]);

                        if (! in_array($choiceId, $allowed, true)) {
                            Log::debug('review.create.invalid_answer', $answerDebug + ['reason' => 'choice_not_belong_to_criteria', 'incoming_choice_id' => $choiceId, 'allowed' => $allowed]);
                            throw new ApiException(trans('reviews.invalid_answer'), 422, ['criteria_id' => $criteriaId, 'type' => 'MULTIPLE_CHOICE', 'allowed_choice_ids' => $allowed]);
                        }

                        $data['choice_id'] = $choiceId;
                        break;
                    case 'TEXT':
                        $text = trim((string) Arr::get($ans, 'text_value', ''));
                        if ($text === '') {
                            throw new ApiException(trans('reviews.invalid_answer'), 422, ['criteria_id' => $criteriaId, 'type' => 'TEXT']);
                        }
                        $data['text_value'] = $text;
                        break;
                    case 'PHOTO':
                        $files = $answerPhotos[$criteriaId] ?? [];
                        if (! is_array($files) || count($files) === 0) {
                            throw new ApiException(trans('reviews.invalid_answer'), 422, ['criteria_id' => $criteriaId, 'type' => 'PHOTO']);
                        }
                        break;
                }

                $answer = ReviewAnswer::create($data);

                if ($crit->type === 'PHOTO') {
                    $files = $answerPhotos[$criteriaId] ?? [];
                    $uploaded = $this->uploadMany($files, 'review-answers/' . $review->id . '/' . $criteriaId, ['max_files' => 3]);
                    foreach ($uploaded as $u) {
                        ReviewAnswerPhoto::create([
                            'review_answer_id' => $answer->id,
                            'storage_path' => $u['path'],
                            'encrypted' => false,
                        ]);
                    }
                }

                $pointsFromAnswers += (int) ($crit->points ?? 0);
            }

            // photos upload
            if (! empty($photos)) {
                $uploaded = $this->uploadMany($photos, 'reviews/' . $review->id, ['max_files' => 3]);
                foreach ($uploaded as $u) {
                    ReviewPhoto::create([
                        'review_id' => $review->id,
                        'storage_path' => $u['path'],
                        'encrypted' => false,
                    ]);
                }
            }

            // consume session
            $session->consumed_at = Carbon::now();
            $session->save();

            // compute weighted review_score using criteria + answer weights (normalized to total 5)
            $answerRows = ReviewAnswer::query()
                ->with(['criteria', 'choice'])
                ->where('review_id', $review->id)
                ->get();

            $sumWeights = 0.0;
            $sumWeighted = 0.0;
            $scores = [];

            foreach ($answerRows as $row) {
                $crit = $row->criteria;
                if (! $crit) continue;

                $score = null;
                $answerWeight = 1.0;

                switch ($crit->type) {
                    case 'RATING':
                        $score = $row->rating_value;
                        $answerWeight = 1.0;
                        break;
                    case 'YES_NO':
                        if ($row->yes_no_value === null) break;
                        $isYes = (bool) $row->yes_no_value;
                        $score = $isYes ? (int) ($crit->yes_value ?? 5) : (int) ($crit->no_value ?? 1);
                        $answerWeight = $isYes ? (float) ($crit->yes_weight ?? 1) : (float) ($crit->no_weight ?? 1);
                        break;
                    case 'MULTIPLE_CHOICE':
                        if (! $row->choice || $row->choice->value === null) break;
                        $score = (int) $row->choice->value;
                        $answerWeight = (float) ($row->choice->weight ?? 1);
                        break;
                    default:
                        break;
                }

                if ($score === null) continue;
                $scores[] = (float) $score;

                $critWeight = (float) ($crit->weight ?? 0);
                $weight = $critWeight * $answerWeight;
                $sumWeights += $weight;
                $sumWeighted += ((float) $score) * $weight;
            }

            if (count($scores) > 0) {
                if ($sumWeights > 0) {
                    $review->review_score = round($sumWeighted / $sumWeights, 2);
                } else {
                    $avg = array_sum($scores) / count($scores);
                    $review->review_score = round((float) $avg, 2);
                }
                $review->save();
            }

            // Award points for review (idempotent)
            $pointsSvc = app(PointsService::class);
            $pointsAwarded = 0;
            $answersPointsAwarded = 0;
            try {
                $pointsAwarded = $pointsSvc->awardPointsForReview($user, $review);
            } catch (\Throwable $ex) {
                // Log and continue; do not fail the review creation for points errors
                Log::error('points.award_failed: '.$ex->getMessage(), ['exception' => $ex, 'review_id' => $review->id]);
                $pointsAwarded = 0;
            }
            if ($pointsFromAnswers > 0) {
                try {
                    $answersPointsAwarded = $pointsSvc->awardPointsForReviewAnswers($user, $review, $pointsFromAnswers);
                } catch (\Throwable $ex) {
                    Log::error('points.award_answers_failed: '.$ex->getMessage(), ['exception' => $ex, 'review_id' => $review->id]);
                    $answersPointsAwarded = 0;
                }
            }

            $pointsBalance = 0;
            try {
                $pointsBalance = $pointsSvc->getBalance($user);
            } catch (\Throwable $_) {
                $pointsBalance = 0;
            }

            DB::commit();

            $review->load(['answers.choice','answers.photos','photos']);
            return [
                'review' => $review,
                'points_awarded' => $pointsAwarded,
                'points_awarded_answers' => $answersPointsAwarded,
                'points_balance' => $pointsBalance,
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function ensureUserSubscription($user): Subscription
    {
        $sub = Subscription::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
        if ($sub) return $sub;

        $started = $user->created_at ? Carbon::parse($user->created_at) : Carbon::now();
        $freeTrialDays = SubscriptionSetting::getFreeTrialDays();
        return Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => null,
            'status' => 'FREE',
            'subscription_status' => 'trialing',
            'started_at' => $started,
            'free_until' => $started->copy()->addDays($freeTrialDays),
            'paid_until' => null,
            'auto_renew' => false,
            'provider' => null,
            'provider_subscription_id' => null,
            'provider_transaction_id' => null,
            'meta' => null,
        ]);
    }

    private function hasSubscriptionAccess(Subscription $sub): bool
    {
        $now = Carbon::now();
        $freeUntil = $this->resolveFreeUntil($sub);
        $paidUntil = $sub->paid_until;

        if ($freeUntil && $freeUntil->isFuture()) return true;
        if ($paidUntil && $paidUntil->isFuture()) return true;

        return false;
    }

    private function resolveFreeUntil(Subscription $sub): ?Carbon
    {
        if ($sub->free_until) return $sub->free_until;
        if (! $sub->started_at) return null;

        $freeTrialDays = SubscriptionSetting::getFreeTrialDays();
        return $sub->started_at->copy()->addDays($freeTrialDays);
    }
}
