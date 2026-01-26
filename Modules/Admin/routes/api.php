<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\app\Http\Controllers\DashboardApiController;

/**
 * Admin Dashboard API Routes (AJAX/JSON)
 * 
 * Prefix: /admin/api
 * Middleware: api + auth:admin (registered in service provider)
 * Name prefix: admin.api. (registered in service provider)
 * 
 * These endpoints are consumed by the Blade dashboard via fetch/AJAX
 * Consistent JSON response format for all endpoints
 */

// ============================================================================
// DASHBOARD APIs
// ============================================================================

Route::group(['middleware' => 'auth:admin'], function () {

    // Get KPIs/Summary data
    Route::get('kpis', [DashboardApiController::class, 'kpis'])->name('kpis');

    // Get reviews chart data (timeseries)
    Route::get('charts/reviews', [DashboardApiController::class, 'reviewsChart'])->name('charts.reviews');

    // Get top places ranking
    Route::get('top-places', [DashboardApiController::class, 'topPlaces'])->name('top-places');

    // Get admin statistics
    Route::get('stats', [DashboardApiController::class, 'stats'])->name('stats');
});

// ============================================================================
// CATEGORY APIs (Example CRUD via AJAX)
// ============================================================================

Route::group(['middleware' => 'auth:admin', 'prefix' => 'categories', 'as' => 'categories.'], function () {
    
    // List categories (with search/filter/pagination)
    Route::get('/', [DashboardApiController::class, 'listCategories'])->name('index');
    
    // Get single category
    Route::get('{id}', [DashboardApiController::class, 'getCategory'])->name('show');
    
    // Create category
    Route::post('/', [DashboardApiController::class, 'storeCategory'])->name('store');
    
    // Update category
    Route::put('{id}', [DashboardApiController::class, 'updateCategory'])->name('update');
    
    // Delete category
    Route::delete('{id}', [DashboardApiController::class, 'deleteCategory'])->name('destroy');
});
