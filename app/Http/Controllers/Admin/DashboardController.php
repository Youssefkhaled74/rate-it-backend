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
        $selectedChartPeriod = $request->query('chart_period', 'week');
        $stats = $this->service->getStats();
        $recent = $this->service->getRecentReviews($selectedStatus, 10);
        $branches = $this->service->getRecentBranches(6);
        $reviewsChart = $this->service->getReviewsChart($selectedChartPeriod, $request->query('lang', app()->getLocale()));
        $userGrowth = $this->service->getUserGrowthMonthly(12);
        $subscriptionOverview = $this->service->getSubscriptionOverview(6);

        $welcomeName = auth()->guard('admin_web')->user()?->name ?? 'Admin';
        $freeTrialDays = SubscriptionSetting::getFreeTrialDays();

        return view('admin.pages.dashboard', [
            'stats' => $stats,
            'reviews' => $recent['items'],
            'counts' => $recent['counts'],
            'branches' => $branches,
            'welcomeName' => $welcomeName,
            'selectedStatus' => $selectedStatus,
            'selectedChartPeriod' => $selectedChartPeriod,
            'reviewsChart' => $reviewsChart,
            'userGrowth' => $userGrowth,
            'subscriptionOverview' => $subscriptionOverview,
            'freeTrialDays' => $freeTrialDays,
        ]);
    }
}
