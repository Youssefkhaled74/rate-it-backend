<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Admin\Dashboard\Controllers\DashboardController;
use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\AdminPermission;

// Mounted under /api/v1/admin
Route::middleware([AdminAuthenticate::class, AdminPermission::class . ':dashboard.view'])->group(function () {
    // Dashboard summary KPIs
    Route::get('dashboard/summary', [DashboardController::class, 'summary']);
    
    // Top places ranked by metric
    Route::get('dashboard/top-places', [DashboardController::class, 'topPlaces']);
    
    // Reviews timeseries chart
    Route::get('dashboard/reviews-chart', [DashboardController::class, 'reviewsChart']);
});
