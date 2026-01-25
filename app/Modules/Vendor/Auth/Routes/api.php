<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Vendor\Auth\Controllers\AuthController;
use App\Http\Middleware\VendorAuthenticate;

Route::prefix('auth')->group(function () {
    // Public auth endpoints
    Route::post('login', [AuthController::class, 'login']);

    // Protected auth endpoints
    Route::middleware([VendorAuthenticate::class])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});
