<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VendorAuthenticate;
use App\Http\Middleware\VendorPermissionWithScoping;
use App\Modules\Vendor\Staff\Controllers\StaffController;

Route::middleware([VendorAuthenticate::class, VendorPermissionWithScoping::class . ':vendor.staff.manage'])
    ->prefix('staff')
    ->group(function () {
        Route::get('/', [StaffController::class, 'index']);
        Route::get('{id}', [StaffController::class, 'show']);
        Route::post('/', [StaffController::class, 'store']);
        Route::patch('{id}', [StaffController::class, 'update']);
        Route::post('{id}/reset-password', [StaffController::class, 'resetPassword']);
    });
