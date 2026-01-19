<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Points\Controllers\PointsController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('points/summary', [PointsController::class, 'summary']);
    Route::get('points/history', [PointsController::class, 'history']);
    Route::post('points/redeem', [PointsController::class, 'redeem']);
});
