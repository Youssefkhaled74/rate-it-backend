<?php

namespace App\Modules\Vendor\Dashboard\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\Vendor\Dashboard\Services\VendorDashboardService;
use App\Modules\Vendor\Dashboard\Resources\DashboardResource;
use App\Support\Exceptions\ApiException;
use Illuminate\Support\Facades\Auth;

class DashboardController extends BaseApiController
{
    protected VendorDashboardService $service;

    public function __construct(VendorDashboardService $service)
    {
        $this->service = $service;
    }

    /**
     * Get vendor dashboard KPIs
     * VENDOR_ADMIN only
     * 
     * Returns: total_branches, reviews_count (7d/30d), average_rating, top_branches, vouchers_used (7d/30d)
     */
    public function summary()
    {
        $vendor = Auth::guard('vendor')->user();

        // Verify vendor is admin
        if ($vendor->role !== 'VENDOR_ADMIN') {
            return $this->error('auth.forbidden', null, 403);
        }

        try {
            $data = $this->service->getSummary($vendor);
            return $this->success(new DashboardResource($data), 'vendor.dashboard.summary');
        } catch (ApiException $e) {
            return $this->error($e->getMessage(), null, $e->getStatusCode());
        }
    }
}
