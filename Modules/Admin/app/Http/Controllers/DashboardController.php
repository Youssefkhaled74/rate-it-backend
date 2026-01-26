<?php

namespace Modules\Admin\app\Http\Controllers;

use Modules\Admin\app\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Dashboard Controller (Blade Views)
 * 
 * Renders HTML pages for the admin dashboard
 */
class DashboardController extends Controller
{
    /**
     * Display the dashboard index page.
     * 
     * The dashboard page will load KPI data via AJAX from the API endpoints
     * 
     * @return View
     */
    public function index(): View
    {
        $admin = auth('admin')->user();

        return view('admin::dashboard.index', [
            'admin' => $admin,
            'apiKpisUrl' => route('admin.api.kpis'),
            'apiChartUrl' => route('admin.api.charts.reviews'),
            'apiTopPlacesUrl' => route('admin.api.top-places'),
        ]);
    }
}
