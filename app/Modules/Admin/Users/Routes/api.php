<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Admin\Users\Controllers\UsersController;
use App\Http\Middleware\AdminAuthenticate;

Route::middleware([AdminAuthenticate::class])->group(function () {
    // list & details
    Route::middleware([\App\Http\Middleware\AdminPermission::class.':users.view'])->group(function () {
        Route::get('users', [UsersController::class, 'index']);
        Route::get('users/{id}', [UsersController::class, 'show']);
    });

    // block/unblock
    Route::middleware([\App\Http\Middleware\AdminPermission::class.':users.block'])->group(function () {
        Route::post('users/{id}/block', [UsersController::class, 'block']);
    });

    // user reviews and points
    Route::middleware([\App\Http\Middleware\AdminPermission::class.':users.reviews.view'])->group(function () {
        Route::get('users/{id}/reviews', [UsersController::class, 'reviews']);
    });
    Route::middleware([\App\Http\Middleware\AdminPermission::class.':users.points.view'])->group(function () {
        Route::get('users/{id}/points', [UsersController::class, 'points']);
    });
});
