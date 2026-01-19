<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Invites\Controllers\InvitesController;

Route::post('invites/check-phones', [InvitesController::class, 'checkPhones']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('invites', [InvitesController::class, 'store']);
    Route::get('invites', [InvitesController::class, 'index']);
});
