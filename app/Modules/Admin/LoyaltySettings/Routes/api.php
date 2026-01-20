<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Admin\LoyaltySettings\Controllers\LoyaltySettingsController;
use App\Http\Middleware\AdminAuthenticate;

Route::middleware([AdminAuthenticate::class])->group(function () {
    Route::middleware([\App\Http\Middleware\AdminPermission::class . ':loyalty.settings.view'])->group(function () {
        Route::get('loyalty-settings', [LoyaltySettingsController::class, 'index']);
    });

    Route::middleware([\App\Http\Middleware\AdminPermission::class . ':loyalty.settings.manage'])->group(function () {
        Route::post('loyalty-settings', [LoyaltySettingsController::class, 'create']);
        Route::post('loyalty-settings/{id}/activate', [LoyaltySettingsController::class, 'activate']);
    });
});
