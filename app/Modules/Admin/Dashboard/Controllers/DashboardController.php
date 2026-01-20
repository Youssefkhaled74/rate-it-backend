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
     * Get dashboard summary
     */
    public function summary()
    {
        $from = request()->query('from');
        $to = request()->query('to');
        $data = $this->service->summary($from, $to);
        return $this->success($data);
    }

    /**
     * Get top places
     */
    public function topPlaces(DashboardChartRequest $request)
    {
        $filters = $request->validated();
        $data = $this->service->topPlaces($filters);
        return $this->success($data);
    }

    /**
     * Get reviews chart data
     */
    public function reviewsChart(DashboardChartRequest $request)
    {
        $filters = $request->validated();
        $result = $this->service->reviewsChart($filters);
        return $this->success($result);
    }
}
