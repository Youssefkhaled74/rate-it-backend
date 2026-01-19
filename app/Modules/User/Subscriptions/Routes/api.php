<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Subscriptions\Controllers\SubscriptionsController;

Route::get('subscriptions/plans', [SubscriptionsController::class, 'plans']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('subscriptions/me', [SubscriptionsController::class, 'me']);
    Route::post('subscriptions/checkout', [SubscriptionsController::class, 'checkout']);
    Route::post('subscriptions/cancel-auto-renew', [SubscriptionsController::class, 'cancelAutoRenew']);
    Route::post('subscriptions/resume-auto-renew', [SubscriptionsController::class, 'resumeAutoRenew']);
    Route::get('subscriptions/history', [SubscriptionsController::class, 'history']);
});
