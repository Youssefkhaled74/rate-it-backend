<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Auth\Controllers\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forgot-password/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('forgot-password/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('forgot-password/reset', [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});
