<?php

use Illuminate\Support\Facades\Route;

// API v1 - User module routes
Route::prefix('v1/user')->group(function () {
    require base_path('app/Modules/User/Onboarding/Routes/api.php');
    // User Auth module routes
    require base_path('app/Modules/User/Auth/Routes/api.php');
});

// Demo routes for traits
Route::prefix('v1/demo')->group(function () {
    Route::get('ping', [\App\Http\Controllers\Api\DemoTraitController::class, 'ping']);
    Route::post('upload', [\App\Http\Controllers\Api\DemoTraitController::class, 'upload']);
});
