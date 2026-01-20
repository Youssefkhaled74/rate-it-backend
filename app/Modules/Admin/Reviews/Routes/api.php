<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Admin\Reviews\Controllers\ReviewsController;
use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\AdminPermission;

// Mounted under /api/v1/admin
Route::middleware([AdminAuthenticate::class])->group(function () {
    // listing & details require basic reviews.manage permission
    Route::middleware([\App\Http\Middleware\AdminPermission::class.':reviews.manage'])->group(function () {
        Route::get('reviews', [ReviewsController::class, 'index']);
        Route::get('reviews/{id}', [ReviewsController::class, 'show']);
        Route::post('reviews/{id}/hide', [ReviewsController::class, 'hide']);
    });

    // reply requires explicit reply permission
    Route::middleware([\App\Http\Middleware\AdminPermission::class.':reviews.reply'])->group(function () {
        Route::post('reviews/{id}/reply', [ReviewsController::class, 'reply']);
    });

    // feature/unfeature requires explicit feature permission
    Route::middleware([\App\Http\Middleware\AdminPermission::class.':reviews.feature'])->group(function () {
        Route::post('reviews/{id}/mark-featured', [ReviewsController::class, 'markFeatured']);
    });
});
