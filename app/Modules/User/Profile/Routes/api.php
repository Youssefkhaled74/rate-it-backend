<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Profile\Controllers\ProfileController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('profile', [ProfileController::class, 'show']);
    Route::match(['put','post'], 'profile', [ProfileController::class, 'update']);
    Route::post('profile/phone/send-otp', [ProfileController::class, 'sendPhoneOtp']);
    Route::post('profile/phone/verify-otp', [ProfileController::class, 'verifyPhoneOtp']);
});
