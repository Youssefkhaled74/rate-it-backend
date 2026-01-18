<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Reviews\Controllers\ReviewsController;

Route::prefix('reviews')->group(function () {
    Route::post('scan-qr', [ReviewsController::class, 'scanQr'])->middleware('auth:sanctum');
    Route::get('branch/{branch}/questions', [ReviewsController::class, 'branchQuestions']);
    Route::post('', [ReviewsController::class, 'store'])->middleware('auth:sanctum');
    Route::get('{review}', [ReviewsController::class, 'show'])->middleware('auth:sanctum');
    Route::get('me/list', [ReviewsController::class, 'myReviews'])->middleware('auth:sanctum');
});
