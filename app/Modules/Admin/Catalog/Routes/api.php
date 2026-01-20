<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Admin\Catalog\Controllers\CategoriesController;
use App\Modules\Admin\Catalog\Controllers\SubcategoriesController;
use App\Modules\Admin\Catalog\Controllers\BrandsController;
use App\Modules\Admin\Catalog\Controllers\PlacesController;
use App\Modules\Admin\Catalog\Controllers\BranchesController;
use App\Modules\Admin\Catalog\Controllers\RatingCriteriaController;
use App\Modules\Admin\Catalog\Controllers\RatingCriteriaChoicesController;
use App\Modules\Admin\Catalog\Controllers\SubcategoryRatingCriteriaController;
use App\Http\Middleware\AdminAuthenticate;
use App\Modules\Admin\CatalogIntegrity\Controllers\LookupController;
use App\Http\Middleware\AdminPermission;

// All routes in this file are mounted under /api/v1/admin
Route::middleware([AdminAuthenticate::class])->group(function () {
    // Categories
    Route::get('categories', [CategoriesController::class, 'index']);
    Route::post('categories', [CategoriesController::class, 'store']);
    Route::get('categories/{id}', [CategoriesController::class, 'show']);
    Route::put('categories/{id}', [CategoriesController::class, 'update']);
    Route::delete('categories/{id}', [CategoriesController::class, 'destroy']);

    // Subcategories
    Route::get('subcategories', [SubcategoriesController::class, 'index']);
    Route::post('subcategories', [SubcategoriesController::class, 'store']);
    Route::get('subcategories/{id}', [SubcategoriesController::class, 'show']);
    Route::put('subcategories/{id}', [SubcategoriesController::class, 'update']);
    Route::delete('subcategories/{id}', [SubcategoriesController::class, 'destroy']);

    // Subcategory <-> Rating Criteria mapping
    Route::get('subcategories/{id}/rating-criteria', [SubcategoryRatingCriteriaController::class, 'index']);
    Route::post('subcategories/{id}/rating-criteria/sync', [SubcategoryRatingCriteriaController::class, 'sync']);
    Route::post('subcategories/{id}/rating-criteria/reorder', [SubcategoryRatingCriteriaController::class, 'reorder']);
    Route::delete('subcategories/{id}/rating-criteria/{criteria_id}', [SubcategoryRatingCriteriaController::class, 'destroy']);

    // Brands
    Route::get('brands', [BrandsController::class, 'index']);
    Route::post('brands', [BrandsController::class, 'store']);
    Route::get('brands/{id}', [BrandsController::class, 'show']);
    Route::put('brands/{id}', [BrandsController::class, 'update']);
    Route::delete('brands/{id}', [BrandsController::class, 'destroy']);

    // Places
    Route::get('places', [PlacesController::class, 'index']);
    Route::post('places', [PlacesController::class, 'store']);
    Route::get('places/{id}', [PlacesController::class, 'show']);
    Route::put('places/{id}', [PlacesController::class, 'update']);
    Route::delete('places/{id}', [PlacesController::class, 'destroy']);

    // Branches
    Route::get('branches', [BranchesController::class, 'index']);
    Route::post('branches', [BranchesController::class, 'store']);
    Route::get('branches/{id}', [BranchesController::class, 'show']);
    Route::put('branches/{id}', [BranchesController::class, 'update']);
    Route::delete('branches/{id}', [BranchesController::class, 'destroy']);
    Route::post('branches/{id}/regenerate-qr', [BranchesController::class, 'regenerateQr']);

    // Rating criteria
    Route::get('rating-criteria', [RatingCriteriaController::class, 'index']);
    Route::post('rating-criteria', [RatingCriteriaController::class, 'store']);
    Route::get('rating-criteria/{id}', [RatingCriteriaController::class, 'show']);
    Route::put('rating-criteria/{id}', [RatingCriteriaController::class, 'update']);
    Route::delete('rating-criteria/{id}', [RatingCriteriaController::class, 'destroy']);

    // Choices
    Route::get('rating-criteria/{criteria_id}/choices', [RatingCriteriaChoicesController::class, 'index']);
    Route::post('rating-criteria/{criteria_id}/choices', [RatingCriteriaChoicesController::class, 'store']);
    Route::put('rating-criteria/{criteria_id}/choices/{choice_id}', [RatingCriteriaChoicesController::class, 'update']);
    Route::delete('rating-criteria/{criteria_id}/choices/{choice_id}', [RatingCriteriaChoicesController::class, 'destroy']);

    // Catalog integrity helpers
    Route::get('categories/{id}/subcategories', [LookupController::class, 'subcategories']);
    Route::get('places/{id}/branches', [LookupController::class, 'placeBranches']);
});
