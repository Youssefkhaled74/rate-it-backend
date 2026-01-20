<?php

namespace App\Modules\Admin\Dashboard\Services;

use App\Models\Review;
use App\Models\User;
use App\Models\Place;
use App\Models\PointsTransaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardService
{
    /**
     * Get dashboard summary (KPIs)
     * 
     * @param array $filters { 'from': 'YYYY-MM-DD', 'to': 'YYYY-MM-DD' }
     * @return array
     */
    public function getSummary(array $filters = []): array
    {
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;

        // Build date range queries
        $reviewQuery = Review::whereNull('deleted_at');
        $userQuery = User::whereNull('deleted_at');
        $pointsQuery = PointsTransaction::whereIn('type', ['EARN_REVIEW', 'EARN_INVITE', 'EARN']);

        if ($from) {
            $reviewQuery->where('created_at', '>=', Carbon::parse($from)->startOfDay());
            $pointsQuery->where('created_at', '>=', Carbon::parse($from)->startOfDay());
        }
        if ($to) {
            $reviewQuery->where('created_at', '<=', Carbon::parse($to)->endOfDay());
            $pointsQuery->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        }

        // Get metrics
        $reviewsCount = $reviewQuery->count();
        $avgRating = $reviewQuery->count() > 0 
            ? (float) $reviewQuery->avg('overall_rating') 
            : 0.0;
        
        $usersCount = $userQuery->count();
        $pointsIssuedTotal = (int) $pointsQuery->where('points', '>', 0)->sum('points');

        // Points redeemed (negative points)
        $pointsRedeemedTotal = (int) abs(PointsTransaction::where('points', '<', 0)
            ->when($from, fn($q) => $q->where('created_at', '>=', Carbon::parse($from)->startOfDay()))
            ->when($to, fn($q) => $q->where('created_at', '<=', Carbon::parse($to)->endOfDay()))
            ->sum('points')
        );

        return [
            'users_count' => $usersCount,
            'reviews_count' => $reviewsCount,
            'avg_rating' => round($avgRating, 2),
            'points_issued_total' => $pointsIssuedTotal,
            'points_redeemed_total' => $pointsRedeemedTotal,
        ];
    }

    /**
     * Get top places ranked by metric
     * 
     * @param array $filters
     * @return array
     */
    public function getTopPlaces(array $filters = []): array
    {
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;
        $limit = min($filters['limit'] ?? 10, 50);
        $metric = $filters['metric'] ?? 'reviews_count';
        $minReviews = $filters['min_reviews'] ?? 1;
        $categoryId = $filters['category_id'] ?? null;

        // Base query
        $query = Place::withCount(['reviews' => function($q) {
            $q->whereNull('deleted_at');
        }])
        ->with('reviews')
        ->whereNull('deleted_at')
        ->having('reviews_count', '>=', $minReviews);

        // Apply date range to review counts
        if ($from || $to) {
            $query->whereHas('reviews', function($q) use ($from, $to) {
                $q->whereNull('deleted_at');
                if ($from) $q->where('created_at', '>=', Carbon::parse($from)->startOfDay());
                if ($to) $q->where('created_at', '<=', Carbon::parse($to)->endOfDay());
            }, '>=', $minReviews);
        }

        // Filter by category if provided
        if ($categoryId) {
            $query->whereHas('subcategory', fn($q) => $q->where('category_id', $categoryId));
        }

        // Apply sorting based on metric
        if ($metric === 'avg_rating') {
            $query->selectRaw('places.*, AVG(reviews.overall_rating) as avg_rating')
                ->join('reviews', 'places.id', '=', 'reviews.place_id')
                ->whereNull('reviews.deleted_at')
                ->when($from, fn($q) => $q->where('reviews.created_at', '>=', Carbon::parse($from)->startOfDay()))
                ->when($to, fn($q) => $q->where('reviews.created_at', '<=', Carbon::parse($to)->endOfDay()))
                ->groupBy('places.id')
                ->orderByDesc('avg_rating')
                ->limit($limit);
        } else {
            // Default: reviews_count
            $query->orderByDesc('reviews_count')->limit($limit);
        }

        $places = $query->get();

        // Build response
        return $places->map(function($place) use ($from, $to) {
            $reviewsInRange = $place->reviews->filter(function($r) use ($from, $to) {
                $createdAt = $r->created_at;
                if ($from && $createdAt < Carbon::parse($from)->startOfDay()) return false;
                if ($to && $createdAt > Carbon::parse($to)->endOfDay()) return false;
                return true;
            });

            $avgRating = $reviewsInRange->isNotEmpty() 
                ? round($reviewsInRange->avg('overall_rating'), 2)
                : 0.0;

            // Points issued for this place (via reviews)
            $pointsIssued = PointsTransaction::where('reference_type', Review::class)
                ->whereIn('reference_id', $reviewsInRange->pluck('id'))
                ->where('points', '>', 0)
                ->sum('points');

            return [
                'place' => [
                    'id' => $place->id,
                    'name' => $place->name ?? '',
                    'logo_url' => $place->meta['logo_url'] ?? null,
                ],
                'reviews_count' => $reviewsInRange->count(),
                'avg_rating' => $avgRating,
                'points_issued' => (int) $pointsIssued,
            ];
        })->sortByDesc('reviews_count')->values()->all();
    }

    /**
     * Get reviews chart (timeseries)
     * 
     * @param array $filters
     * @return array
     */
    public function getReviewsChart(array $filters = []): array
    {
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;
        $interval = $filters['interval'] ?? null;
        $placeId = $filters['place_id'] ?? null;
        $branchId = $filters['branch_id'] ?? null;
        $categoryId = $filters['category_id'] ?? null;

        if (!$from || !$to) {
            throw new \InvalidArgumentException('from and to dates are required');
        }

        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        // Auto-select interval based on range
        if (!$interval) {
            $days = $fromDate->diffInDays($toDate);
            if ($days <= 45) {
                $interval = 'day';
            } elseif ($days <= 180) {
                $interval = 'week';
            } else {
                $interval = 'month';
            }
        }

        // Build query
        $query = Review::whereNull('deleted_at')
            ->where('created_at', '>=', $fromDate)
            ->where('created_at', '<=', $toDate);

        if ($placeId) {
            $query->where('place_id', (int) $placeId);
        }
        if ($branchId) {
            $query->where('branch_id', (int) $branchId);
        }
        if ($categoryId) {
            $query->whereHas('place', fn($q) => 
                $q->whereHas('subcategory', fn($sq) => $sq->where('category_id', $categoryId))
            );
        }

        // Group by interval
        $selectFormat = match($interval) {
            'day' => "DATE(reviews.created_at) as period",
            'week' => "DATE_TRUNC('week', reviews.created_at) as period",
            'month' => "DATE_TRUNC('month', reviews.created_at) as period",
            default => "DATE(reviews.created_at) as period",
        };

        // PostgreSQL-friendly aggregation
        $results = DB::table('reviews')
            ->whereNull('deleted_at')
            ->where('created_at', '>=', $fromDate)
            ->where('created_at', '<=', $toDate)
            ->when($placeId, fn($q) => $q->where('place_id', $placeId))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->selectRaw("DATE(reviews.created_at) as date, COUNT(*) as reviews_count, AVG(overall_rating) as avg_rating")
            ->groupBy(DB::raw("DATE(reviews.created_at)"))
            ->orderBy('date')
            ->get();

        // Build result with period labels
        $data = [];
        $resultMap = $results->keyBy('date');

        $period = CarbonPeriod::create($fromDate, $toDate);
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $result = $resultMap[$dateStr] ?? null;

            $data[] = [
                'date' => $dateStr,
                'reviews_count' => (int) ($result?->reviews_count ?? 0),
                'avg_rating' => $result && $result->reviews_count > 0 
                    ? round((float) $result->avg_rating, 2)
                    : null,
            ];
        }

        return [
            'interval' => $interval,
            'from' => $from,
            'to' => $to,
            'series' => $data,
        ];
    }
}


