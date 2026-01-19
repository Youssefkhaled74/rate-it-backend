<?php

namespace App\Modules\User\Reviews\Services;

use App\Models\BranchQrSession;
use App\Models\Review;
use App\Models\ReviewAnswer;
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

class ReviewService
{
    use PublicUploadTrait;

    public function createReview($user, string $sessionToken, $overallRating, $comment, array $answers, array $photos = [])
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

        // Instrumentation: log branch and subcategory
        $subcat = optional(optional($branch)->place)->subcategory_id;
        Log::debug('review.create.branch_info', ['branch_id' => $branch->id, 'place_id' => $branch->place_id, 'subcategory_id' => $subcat]);

        // Cooldown check
        $cooldownDays = (int) $branch->review_cooldown_days;
        if ($cooldownDays > 0) {
            $last = Review::where('branch_id', $branch->id)->where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
            if ($last && Carbon::now()->diffInDays($last->created_at) < $cooldownDays) {
                throw new ApiException(trans('reviews.cooldown_active', ['days' => $cooldownDays]), 422);
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
        $providedIds = array_column($answers, 'criteria_id');
        foreach ($required as $rid) {
            if (! in_array($rid, $providedIds, true)) {
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

            foreach ($answers as $ans) {
                $criteriaId = Arr::get($ans, 'criteria_id');
                $crit = $criteria->get($criteriaId);
                if (! $crit) {
                    Log::debug('review.create.invalid_criteria', ['criteria_id' => $criteriaId, 'reason' => 'not_in_allowed_set']);
                    throw new ApiException('reviews.invalid_answer_for_criteria', 422, ['criteria_id' => $criteriaId]);
                }

                $data = ['review_id' => $review->id, 'criteria_id' => $criteriaId];
                switch ($crit->type) {
                    case 'RATING':
                        $val = Arr::get($ans, 'rating_value');
                        if (! is_numeric($val) || $val < 1 || $val > 5) {
                            throw new ApiException(trans('reviews.invalid_answer'), 422);
                        }
                        $data['rating_value'] = (int) $val;
                        break;
                    case 'YES_NO':
                        if (! isset($ans['yes_no_value'])) {
                            throw new ApiException(trans('reviews.invalid_answer'), 422);
                        }
                        $data['yes_no_value'] = (bool) $ans['yes_no_value'];
                        break;
                    case 'MULTIPLE_CHOICE':
                        $choiceId = Arr::get($ans, 'choice_id');
                        $choice = RatingCriteriaChoice::where('id', $choiceId)->where('criteria_id', $criteriaId)->first();
                        if (! $choice) {
                            throw new ApiException(trans('reviews.invalid_answer'), 422);
                        }
                        $data['choice_id'] = $choiceId;
                        break;
                }

                ReviewAnswer::create($data);
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

            // compute simple review_score (average of rating_value answers if any)
            $ratingAnswers = ReviewAnswer::where('review_id', $review->id)->whereNotNull('rating_value')->pluck('rating_value')->toArray();
            if (! empty($ratingAnswers)) {
                $review->review_score = round(array_sum($ratingAnswers) / count($ratingAnswers), 2);
                $review->save();
            }

            DB::commit();

            return $review->load(['answers.choice','photos']);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
