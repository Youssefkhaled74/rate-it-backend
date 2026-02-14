<?php

namespace App\Modules\User\Home\Services;

use App\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class HomeTopBrandService
{
    public function paginateTopBrands(int $perPage = 10): LengthAwarePaginator
    {
        $perPage = max(1, min($perPage, 30));

        $reviewStats = DB::table('reviews')
            ->leftJoin('places', 'places.id', '=', 'reviews.place_id')
            ->leftJoin('branches', 'branches.id', '=', 'reviews.branch_id')
            ->where('reviews.status', 'ACTIVE')
            ->where(function ($query) {
                $query->whereNull('reviews.is_hidden')
                    ->orWhere('reviews.is_hidden', false);
            })
            ->whereRaw('COALESCE(places.brand_id, branches.brand_id) IS NOT NULL')
            ->groupBy(DB::raw('COALESCE(places.brand_id, branches.brand_id)'))
            ->selectRaw('
                COALESCE(places.brand_id, branches.brand_id) as brand_id,
                AVG(reviews.overall_rating) as overall_avg_rating,
                AVG(reviews.review_score) as avg_review_score,
                COUNT(reviews.id) as reviews_count
            ');

        return Brand::query()
            ->where('brands.is_active', true)
            ->where(function ($query) {
                $query->whereNull('brands.start_date')
                    ->orWhereDate('brands.start_date', '<=', now()->toDateString());
            })
            ->where(function ($query) {
                $query->whereNull('brands.end_date')
                    ->orWhereDate('brands.end_date', '>=', now()->toDateString());
            })
            ->joinSub($reviewStats, 'review_stats', function ($join) {
                $join->on('review_stats.brand_id', '=', 'brands.id');
            })
            ->leftJoin('subcategories', 'subcategories.id', '=', 'brands.subcategory_id')
            ->select([
                'brands.*',
                'review_stats.overall_avg_rating',
                'review_stats.avg_review_score',
                'review_stats.reviews_count',
                'subcategories.name_en as subcategory_name_en',
                'subcategories.name_ar as subcategory_name_ar',
            ])
            ->orderByDesc('review_stats.avg_review_score')
            ->orderByDesc('review_stats.overall_avg_rating')
            ->orderByDesc('review_stats.reviews_count')
            ->paginate($perPage);
    }
}
