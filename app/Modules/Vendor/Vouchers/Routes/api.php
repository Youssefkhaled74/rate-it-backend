<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VendorAuthenticate;
use App\Modules\Vendor\Vouchers\Controllers\VouchersController;

Route::middleware([VendorAuthenticate::class])
    ->prefix('vouchers')
    ->group(function () {
        Route::post('check', [VouchersController::class, 'check']);
    });
