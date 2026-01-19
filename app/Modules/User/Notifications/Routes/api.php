<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Notifications\Controllers\NotificationsController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('notifications', [NotificationsController::class, 'index']);
    Route::get('notifications/unread-count', [NotificationsController::class, 'unreadCount']);
    Route::post('notifications/{id}/read', [NotificationsController::class, 'markRead']);
    Route::post('notifications/read-all', [NotificationsController::class, 'markAllRead']);
    Route::delete('notifications/{id}', [NotificationsController::class, 'destroy']);
    Route::delete('notifications', [NotificationsController::class, 'clearAll']);
    Route::get('notifications/{id}', [NotificationsController::class, 'show']);
});
