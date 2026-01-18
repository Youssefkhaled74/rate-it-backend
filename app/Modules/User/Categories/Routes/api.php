<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Categories\Controllers\CategoriesController;
use App\Modules\User\Categories\Controllers\CategorySubcategoriesController;
use App\Modules\User\Categories\Controllers\CategoriesSearchController;

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoriesController::class, 'index']);
    Route::get('/search', [CategoriesSearchController::class, 'index']);
    Route::get('/{category}/subcategories', [CategorySubcategoriesController::class, 'index']);
});
