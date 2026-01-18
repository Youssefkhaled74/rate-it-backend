<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Brands\Controllers\BrandController;
use App\Modules\User\Brands\Controllers\PlaceController;

Route::prefix('brands')->group(function () {
    Route::get('{brand}', [BrandController::class, 'show']);
    Route::get('{brand}/places', [BrandController::class, 'places']);
});

Route::prefix('places')->group(function () {
    Route::get('{place}', [PlaceController::class, 'show']);
    Route::get('{place}/reviews', [PlaceController::class, 'reviews']);
});
