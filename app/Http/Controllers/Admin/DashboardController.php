<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminDashboardService;
use App\Models\SubscriptionSetting;

class DashboardController extends Controller
{
    protected AdminDashboardService $service;

    public function __construct(AdminDashboardService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $selectedStatus = $request->query('status', 'all');
        $stats = $this->service->getStats();
        $recent = $this->service->getRecentReviews($selectedStatus, 10);
        $branches = $this->service->getRecentBranches(6);
        $reviewsChart = $this->service->getReviewsChart(7);
        $userGrowth = $this->service->getUserGrowthMonthly(12);

        $welcomeName = auth()->guard('admin_web')->user()?->name ?? 'Admin';
        $freeTrialDays = SubscriptionSetting::getFreeTrialDays();

        return view('admin.pages.dashboard', [
            'stats' => $stats,
            'reviews' => $recent['items'],
            'counts' => $recent['counts'],
            'branches' => $branches,
            'welcomeName' => $welcomeName,
            'selectedStatus' => $selectedStatus,
            'reviewsChart' => $reviewsChart,
            'userGrowth' => $userGrowth,
            'freeTrialDays' => $freeTrialDays,
        ]);
    }
}
