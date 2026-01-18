<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Home\Controllers\HomeBannerController;
use App\Modules\User\Home\Controllers\HomeCategoriesController;

Route::get('home/banners', [HomeBannerController::class, 'index']);
Route::get('home/categories', [HomeCategoriesController::class, 'index']);
