<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminDashboardService;

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

        $welcomeName = auth()->guard('admin_web')->user()?->name ?? 'Admin';

        return view('admin.pages.dashboard', [
            'stats' => $stats,
            'reviews' => $recent['items'],
            'counts' => $recent['counts'],
            'welcomeName' => $welcomeName,
            'selectedStatus' => $selectedStatus,
        ]);
    }
}
