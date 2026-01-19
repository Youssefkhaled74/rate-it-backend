<?php

use Illuminate\Support\Facades\Route;

// API v1 - User module routes
Route::prefix('v1/user')->group(function () {
    require base_path('app/Modules/User/Onboarding/Routes/api.php');
    // User Home (banners, etc.)
    require base_path('app/Modules/User/Home/Routes/api.php');
    // User Auth module routes
    require base_path('app/Modules/User/Auth/Routes/api.php');
    // User Lookups (genders, nationalities)
    require base_path('app/Modules/User/Lookups/Routes/api.php');
    // User Categories
    require base_path('app/Modules/User/Categories/Routes/api.php');
    // User Brands & Places
    require base_path('app/Modules/User/Brands/Routes/api.php');
    // User Reviews
    require base_path('app/Modules/User/Reviews/Routes/api.php');
    // User Notifications
    require base_path('app/Modules/User/Notifications/Routes/api.php');
    // User Points
    require base_path('app/Modules/User/Points/Routes/api.php');
    // User Invites
    require base_path('app/Modules/User/Invites/Routes/api.php');
    // User Profile
    require base_path('app/Modules/User/Profile/Routes/api.php');
    // User Subscriptions
    require base_path('app/Modules/User/Subscriptions/Routes/api.php');
});

// Demo routes for traits
Route::prefix('v1/demo')->group(function () {
    Route::get('ping', [\App\Http\Controllers\Api\DemoTraitController::class, 'ping']);
    Route::post('upload', [\App\Http\Controllers\Api\DemoTraitController::class, 'upload']);
});
