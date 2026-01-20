<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuthenticate;

Route::middleware([AdminAuthenticate::class])->group(function () {
    Route::middleware([\App\Http\Middleware\AdminPermission::class . ':invites.view'])->group(function () {
        Route::get('invites', [\App\Modules\Admin\Invites\Controllers\InvitesController::class, 'index']);
        Route::get('invites/{id}', [\App\Modules\Admin\Invites\Controllers\InvitesController::class, 'show']);
    });
});
