<?php

namespace App\Modules\User\Points\Services;

use App\Models\PointsSetting;
use App\Models\PointsTransaction;
use App\Support\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PointsService
{
    public function getCurrentSetting(): ?PointsSetting
    {
        return PointsSetting::where('is_active', true)->orderBy('created_at', 'desc')->first();
    }

    public function getBalance($user): int
    {
        // Use scopeUnexpired if available
        $query = PointsTransaction::where('user_id', $user->id);
        if (method_exists(PointsTransaction::class, 'scopeUnexpired')) {
            $query = $query->unexpired();
        } else {
            $query = $query->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); });
        }
        return (int) $query->sum('points');
    }

    public function getHistory($user, int $perPage = 20)
    {
        return PointsTransaction::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function awardPointsForReview($user, $review): int
    {
        $setting = $this->getCurrentSetting();
        if (! $setting) {
            throw new ApiException('points.settings_not_found', 500);
        }

        $points = (int) $setting->points_per_review;
        if ($points <= 0) {
            return 0;
        }

        // Prevent duplicates (check existing reference record or legacy source keys)
        $exists = PointsTransaction::where('user_id', $user->id)
            ->where(function($q) use ($review) {
                $q->where(function($q2) use ($review) {
                    $q2->where('reference_type', \\App\\Models\\Review::class)->where('reference_id', $review->id);
                })->orWhere(function($q3) use ($review) {
                    $q3->where('source_type', 'review')->where('source_id', $review->id);
                });
            })->exists();
        if ($exists) {
            return 0;
        }

        $meta = [];
        if (isset($review->branch_id)) $meta['branch_id'] = $review->branch_id;
        if (isset($review->place_id)) $meta['place_id'] = $review->place_id;
        if (isset($review->brand_id)) $meta['brand_id'] = $review->brand_id;

        PointsTransaction::create([
            'user_id' => $user->id,
            'brand_id' => $review->brand_id ?? null,
            'type' => 'EARN_REVIEW',
            'points' => $points,
            'reference_type' => \\App\\Models\\Review::class,
            'reference_id' => $review->id,
            'meta' => $meta ? array_merge($meta, ['settings_version' => $setting->version ?? null]) : ['settings_version' => $setting->version ?? null],
            'expires_at' => null,
        ]);

        return $points;
    }

    public function redeemPoints($user, int $points): int
    {
        if ($points <= 0) {
            throw new ApiException('points.invalid_amount', 422);
        }

        $balance = $this->getBalance($user);
        if ($points > $balance) {
            throw new ApiException('points.insufficient_balance', 422, ['balance' => $balance]);
        }

        PointsTransaction::create([
            'user_id' => $user->id,
            'brand_id' => null,
            'type' => 'REDEEM_VOUCHER',
            'points' => -abs($points),
            'source_type' => 'redeem',
            'source_id' => null,
            'meta' => null,
            'expires_at' => null,
        ]);

        return $this->getBalance($user);
    }
}
