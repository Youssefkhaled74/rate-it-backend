<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VendorAuthenticate;
use App\Http\Middleware\VendorPermissionWithScoping;
use App\Modules\Vendor\Reviews\Controllers\ReviewsController;

Route::middleware([VendorAuthenticate::class, VendorPermissionWithScoping::class . ':vendor.reviews.list'])
    ->prefix('reviews')
    ->group(function () {
        Route::get('/', [ReviewsController::class, 'index']);
        Route::get('{id}', [ReviewsController::class, 'show']);
    });
