<?php

namespace App\Modules\Admin\Dashboard\Controllers;

use App\Support\Api\BaseApiController;
use Illuminate\Support\Facades\Auth;
use App\Modules\Admin\Dashboard\Services\DashboardService;
use App\Modules\Admin\Dashboard\Requests\DashboardChartRequest;

class DashboardController extends BaseApiController
{
    protected DashboardService $service;

    public function __construct(DashboardService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /admin/dashboard/summary
     * 
     * Returns KPIs: users count, reviews count, avg rating, points issued/redeemed.
     * Optional: from, to (date range)
     */
    public function summary(DashboardChartRequest $request)
    {
        $filters = $request->validated();
        $data = $this->service->getSummary($filters);
        
        $meta = [];
        if ($filters['from'] ?? null) $meta['from'] = $filters['from'];
        if ($filters['to'] ?? null) $meta['to'] = $filters['to'];

        return $this->success($data, 'admin.dashboard.summary', $meta ?: null);
    }

    /**
     * GET /admin/dashboard/top-places
     * 
     * Returns top places ranked by metric (reviews_count, avg_rating, points_issued).
     * Optional: from, to, limit (default 10, max 50), metric, min_reviews, category_id
     */
    public function topPlaces(DashboardChartRequest $request)
    {
        $filters = $request->validated();
        $data = $this->service->getTopPlaces($filters);

        return $this->success($data, 'admin.dashboard.top_places');
    }

    /**
     * GET /admin/dashboard/reviews-chart
     * 
     * Returns timeseries (day/week/month) of reviews count + avg rating.
     * Required: from, to (YYYY-MM-DD)
     * Optional: interval (auto-selected if not provided), place_id, branch_id, category_id
     */
    public function reviewsChart(DashboardChartRequest $request)
    {
        $filters = $request->validated();
        
        try {
            $data = $this->service->getReviewsChart($filters);
            return $this->success($data, 'admin.dashboard.reviews_chart');
        } catch (\InvalidArgumentException $e) {
            return $this->error('Validation error: ' . $e->getMessage(), null, 422);
        }
    }
}

