<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuthenticate;

Route::middleware([AdminAuthenticate::class])->group(function () {
    // Templates
    Route::middleware([\App\Http\Middleware\AdminPermission::class.':notifications.templates.view'])->group(function () {
        Route::get('notifications/templates', [\App\Modules\Admin\Notifications\Templates\Controllers\TemplatesController::class, 'index']);
    });
    Route::middleware([\App\Http\Middleware\AdminPermission::class.':notifications.templates.manage'])->group(function () {
        Route::post('notifications/templates', [\App\Modules\Admin\Notifications\Templates\Controllers\TemplatesController::class, 'store']);
        Route::put('notifications/templates/{id}', [\App\Modules\Admin\Notifications\Templates\Controllers\TemplatesController::class, 'update']);
    });

    // Broadcast
    Route::middleware([\App\Http\Middleware\AdminPermission::class.':notifications.broadcast.send'])->group(function () {
        Route::post('notifications/broadcast', [\App\Modules\Admin\Notifications\Broadcast\Controllers\BroadcastController::class, 'send']);
    });

    // Send to single user
    Route::middleware([\App\Http\Middleware\AdminPermission::class.':notifications.user.send'])->group(function () {
        Route::post('users/{id}/notifications', [\App\Modules\Admin\Notifications\Send\Controllers\SendController::class, 'sendToUser']);
    });
});
