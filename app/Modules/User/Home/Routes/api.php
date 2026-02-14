<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Home\Controllers\HomeBannerController;
use App\Modules\User\Home\Controllers\HomeCategoriesController;
use App\Modules\User\Home\Controllers\HomePageController;

Route::get('home', [HomePageController::class, 'index']);
Route::get('home/banners', [HomeBannerController::class, 'index']);
Route::get('home/categories', [HomeCategoriesController::class, 'index']);
Route::get('home/search', [\App\Modules\User\Home\Controllers\HomeSearchController::class, 'index']);
