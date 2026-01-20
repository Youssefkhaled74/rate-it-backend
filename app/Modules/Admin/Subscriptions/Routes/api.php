<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuthenticate;

Route::middleware([AdminAuthenticate::class])->group(function () {
    // Plans
    Route::middleware([\App\Http\Middleware\AdminPermission::class . ':subscriptions.plans.view'])->group(function () {
        Route::get('subscription-plans', [\App\Modules\Admin\Subscriptions\Plans\Controllers\PlansController::class, 'index']);
    });
    Route::middleware([\App\Http\Middleware\AdminPermission::class . ':subscriptions.plans.manage'])->group(function () {
        Route::post('subscription-plans', [\App\Modules\Admin\Subscriptions\Plans\Controllers\PlansController::class, 'store']);
        Route::put('subscription-plans/{id}', [\App\Modules\Admin\Subscriptions\Plans\Controllers\PlansController::class, 'update']);
        Route::post('subscription-plans/{id}/activate', [\App\Modules\Admin\Subscriptions\Plans\Controllers\PlansController::class, 'activate']);
    });

    // Subscriptions monitoring
    Route::middleware([\App\Http\Middleware\AdminPermission::class . ':subscriptions.view'])->group(function () {
        Route::get('subscriptions', [\App\Modules\Admin\Subscriptions\Subscriptions\Controllers\SubscriptionsController::class, 'index']);
    });
});
