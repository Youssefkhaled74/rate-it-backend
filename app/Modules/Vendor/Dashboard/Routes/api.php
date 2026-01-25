<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VendorAuthenticate;
use App\Modules\Vendor\Dashboard\Controllers\DashboardController;

Route::middleware([VendorAuthenticate::class])
    ->prefix('dashboard')
    ->group(function () {
        Route::get('summary', [DashboardController::class, 'summary']);
    });
