<?php

namespace App\Modules\Admin\Dashboard\Services;

use Illuminate\Database\Eloquent\Collection;

class DashboardService
{
    /**
     * Get dashboard overview data
     */
    public function getOverview(): array
    {
        return [
            'total_reviews' => 0,
            'total_ratings' => 0,
            'average_rating' => 0,
            'total_users' => 0,
        ];
    }

    /**
     * Get chart data for reviews
     */
    public function getReviewsChart(array $filters): array
    {
        return [
            'labels' => [],
            'data' => [],
        ];
    }

    /**
     * Get chart data for ratings
     */
    public function getRatingsChart(array $filters): array
    {
        return [
            'labels' => [],
            'data' => [],
        ];
    }
}
