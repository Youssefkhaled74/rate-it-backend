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

// API v1 - Admin module routes
Route::prefix('v1/admin')->group(function () {
    require base_path('app/Modules/Admin/Auth/Routes/api.php');
    // Admin Catalog (Categories, Brands, Places, etc.)
    require base_path('app/Modules/Admin/Catalog/Routes/api.php');
    // Admin Dashboard
    require base_path('app/Modules/Admin/Dashboard/Routes/api.php');
    // Admin Reviews moderation
    require base_path('app/Modules/Admin/Reviews/Routes/api.php');
    // Admin Users management
    require base_path('app/Modules/Admin/Users/Routes/api.php');
    // Admin Loyalty Settings
    require base_path('app/Modules/Admin/LoyaltySettings/Routes/api.php');
    // Admin Points monitoring
    require base_path('app/Modules/Admin/Points/Routes/api.php');
    // Admin Notifications
    require base_path('app/Modules/Admin/Notifications/Routes/api.php');
    // Admin Invites monitoring
    require base_path('app/Modules/Admin/Invites/Routes/api.php');
    // Admin Subscriptions
    require base_path('app/Modules/Admin/Subscriptions/Routes/api.php');
});

// API v1 - Vendor module routes
Route::prefix('v1/vendor')->group(function () {
    require base_path('app/Modules/Vendor/Auth/Routes/api.php');
    // Vendor branches management
    require base_path('app/Modules/Vendor/Branches/Routes/api.php');
    // Vendor reviews
    require base_path('app/Modules/Vendor/Reviews/Routes/api.php');
    // Vendor staff management
    require base_path('app/Modules/Vendor/Staff/Routes/api.php');
    // Vendor vouchers
    require base_path('app/Modules/Vendor/Vouchers/Routes/api.php');
    // Vendor RBAC with brand/branch scoping
    require base_path('app/Modules/Vendor/Rbac/Routes/api.php');
});

