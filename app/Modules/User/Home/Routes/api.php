<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Home\Controllers\HomeBannerController;

Route::get('home/banners', [HomeBannerController::class, 'index']);
