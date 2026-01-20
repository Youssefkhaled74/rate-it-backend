<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Admin\Points\Controllers\PointsController;
use App\Http\Middleware\AdminAuthenticate;

Route::middleware([AdminAuthenticate::class])->group(function () {
    Route::middleware([\App\Http\Middleware\AdminPermission::class . ':points.transactions.view'])->group(function () {
        Route::get('points/transactions', [PointsController::class, 'index']);
        Route::get('points/transactions/{id}', [PointsController::class, 'show']);
    });
});
