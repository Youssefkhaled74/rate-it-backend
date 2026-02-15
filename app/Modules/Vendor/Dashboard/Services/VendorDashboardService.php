<?php

namespace App\Modules\Vendor\Dashboard\Services;

use App\Models\Review;
use App\Models\Voucher;
use App\Models\VendorUser;
use App\Models\Branch;
use App\Support\Traits\Vendor\VendorScoping;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendorDashboardService
{
    use VendorScoping;

    /**
     * Get vendor dashboard KPIs
     * 
     * Returns:
     * - total_branches
     * - reviews_count (last 7 days, last 30 days)
     * - average_rating_brand
     * - top_branches_by_rating (limit 5)
     * - vouchers_used (last 7 days, last 30 days)
     */
    public function getSummary(VendorUser $vendor): array
    {
        $brandId = $this->getVendorBrandId($vendor);
        
        // Get time references
        $now = Carbon::now();
        $sevenDaysAgo = $now->copy()->subDays(7);
        $thirtyDaysAgo = $now->copy()->subDays(30);

        // Parallel queries for efficiency
        return [
            'total_branches' => $this->getTotalBranches($brandId),
            'reviews_count' => [
                'last_7_days' => $this->getReviewsCount($brandId, $sevenDaysAgo),
                'last_30_days' => $this->getReviewsCount($brandId, $thirtyDaysAgo),
            ],
            'average_rating_brand' => $this->getAverageRating($brandId),
            'top_branches_by_rating' => $this->getTopBranchesByRating($brandId, 5),
            'vouchers_used' => [
                'last_7_days' => $this->getVouchersUsed($brandId, $sevenDaysAgo),
                'last_30_days' => $this->getVouchersUsed($brandId, $thirtyDaysAgo),
            ],
        ];
    }

    /**
     * Get total branches in vendor's brand
     * 
     * Uses indexed query on place.brand_id
     */
    protected function getTotalBranches(int $brandId): int
    {
        return Branch::query()
            ->where('brand_id', $brandId)
            ->count();
    }

    /**
     * Get reviews count for brand since date
     * 
     * Uses indexed queries: place.brand_id, review.created_at
     */
    protected function getReviewsCount(int $brandId, Carbon $since): int
    {
        return Review::query()
            ->whereHas('branch', fn($q) => $q->where('brand_id', $brandId))
            ->where('created_at', '>=', $since)
            ->whereNull('deleted_at')
            ->count();
    }

    /**
     * Get average rating for brand
     * 
     * Uses indexed queries with aggregation
     */
    protected function getAverageRating(int $brandId): float
    {
        $avg = Review::query()
            ->whereHas('branch', fn($q) => $q->where('brand_id', $brandId))
            ->whereNull('deleted_at')
            ->avg('overall_rating');
        
        return $avg ? round((float) $avg, 2) : 0.0;
    }

    /**
     * Get top 5 branches ranked by average rating
     * 
     * Uses indexed queries with aggregation
     */
    protected function getTopBranchesByRating(int $brandId, int $limit): array
    {
        return Branch::query()
            ->with(['brand:id,name_en,name_ar'])
            ->where('brand_id', $brandId)
            ->whereHas('reviews', fn($q) => $q->whereNull('deleted_at'))
            ->withCount(['reviews' => function ($q) {
                $q->whereNull('deleted_at');
            }])
            ->withAvg(['reviews' => function ($q) {
                $q->whereNull('deleted_at');
            }], 'overall_rating')
            ->orderByDesc('reviews_avg_overall_rating')
            ->limit($limit)
            ->get()
            ->map(function($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'place_id' => null,
                    'place_name' => $branch->brand?->display_name ?? null,
                    'reviews_count' => $branch->reviews_count ?? 0,
                    'average_rating' => $branch->reviews_avg_overall_rating 
                        ? round((float) $branch->reviews_avg_overall_rating, 2) 
                        : 0.0,
                ];
            })
            ->toArray();
    }

    /**
     * Get count of vouchers redeemed (status=USED) since date
     * 
     * Uses indexed queries: voucher.brand_id, voucher.status, voucher.used_at
     */
    protected function getVouchersUsed(int $brandId, Carbon $since): int
    {
        return Voucher::query()
            ->where('brand_id', $brandId)
            ->where('status', 'USED')
            ->where('used_at', '>=', $since)
            ->count();
    }
}
