<?php

namespace App\Modules\Admin\Dashboard\Controllers;

use App\Support\Api\BaseApiController;
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
     * Get dashboard overview
     */
    public function overview()
    {
        $data = $this->service->getOverview();
        return $this->successResponse($data);
    }

    /**
     * Get reviews chart data
     */
    public function reviewsChart(DashboardChartRequest $request)
    {
        $filters = $request->validated();
        $data = $this->service->getReviewsChart($filters);
        return $this->successResponse($data);
    }

    /**
     * Get ratings chart data
     */
    public function ratingsChart(DashboardChartRequest $request)
    {
        $filters = $request->validated();
        $data = $this->service->getRatingsChart($filters);
        return $this->successResponse($data);
    }
}
