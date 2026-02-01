<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('login', [AdminAuthController::class, 'show'])
        ->middleware('guest:admin_web')
        ->name('login');

    Route::post('login', [AdminAuthController::class, 'login'])
        ->middleware(['guest:admin_web', 'throttle:6,1'])
        ->name('login.submit');

    Route::post('logout', [AdminAuthController::class, 'logout'])
        ->middleware('auth:admin_web')
        ->name('logout');

    Route::get('/', [DashboardController::class, 'index'])
        ->middleware('auth:admin_web')
        ->name('dashboard');

    // Admin management
    Route::middleware('auth:admin_web')->group(function () {
        Route::get('admins', [\App\Http\Controllers\Admin\AdminsController::class, 'index'])->name('admins.index');
        Route::get('admins/create', [\App\Http\Controllers\Admin\AdminsController::class, 'create'])->name('admins.create');
        Route::post('admins', [\App\Http\Controllers\Admin\AdminsController::class, 'store'])->name('admins.store');
        Route::get('admins/{admin}/edit', [\App\Http\Controllers\Admin\AdminsController::class, 'edit'])->name('admins.edit');
        Route::match(['put', 'patch'], 'admins/{admin}', [\App\Http\Controllers\Admin\AdminsController::class, 'update'])->name('admins.update');
        Route::patch('admins/{admin}/toggle', [\App\Http\Controllers\Admin\AdminsController::class, 'toggle'])->name('admins.toggle');
        Route::delete('admins/{admin}', [\App\Http\Controllers\Admin\AdminsController::class, 'destroy'])->name('admins.destroy');
    });

    // Users module
    Route::middleware('auth:admin_web')->group(function () {
        Route::get('users', [\App\Http\Controllers\Admin\UsersController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [\App\Http\Controllers\Admin\UsersController::class, 'show'])->name('users.show');
    });

    // Profile routes (self)
    Route::middleware('auth:admin_web')->group(function () {
        Route::get('profile', [\App\Http\Controllers\Admin\AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile/photo', [\App\Http\Controllers\Admin\AdminProfileController::class, 'updatePhoto'])->name('profile.photo.update');
        Route::delete('profile/photo', [\App\Http\Controllers\Admin\AdminProfileController::class, 'removePhoto'])->name('profile.photo.remove');
    });
    Route::middleware('auth:admin_web')->group(function () {
        Route::get('categories', [\App\Http\Controllers\Admin\CategoriesController::class, 'index'])->name('categories.index');
        Route::get('categories/create', [\App\Http\Controllers\Admin\CategoriesController::class, 'create'])->name('categories.create');
        Route::post('categories', [\App\Http\Controllers\Admin\CategoriesController::class, 'store'])->name('categories.store');
        Route::get('categories/{category}/edit', [\App\Http\Controllers\Admin\CategoriesController::class, 'edit'])->name('categories.edit');
        Route::match(['put', 'patch'], 'categories/{category}', [\App\Http\Controllers\Admin\CategoriesController::class, 'update'])->name('categories.update');
        Route::patch('categories/{category}/toggle', [\App\Http\Controllers\Admin\CategoriesController::class, 'toggle'])->name('categories.toggle');
        Route::delete('categories/{category}', [\App\Http\Controllers\Admin\CategoriesController::class, 'destroy'])->name('categories.destroy');
        // Subcategories management (داخل نفس صفحة categories)
        Route::post('categories/{category}/subcategories', [\App\Http\Controllers\Admin\SubcategoriesController::class, 'store'])
            ->name('categories.subcategories.store');

        Route::match(['put', 'patch'], 'subcategories/{subcategory}', [\App\Http\Controllers\Admin\SubcategoriesController::class, 'update'])
            ->name('subcategories.update');

        Route::patch('subcategories/{subcategory}/toggle', [\App\Http\Controllers\Admin\SubcategoriesController::class, 'toggle'])
            ->name('subcategories.toggle');

        Route::delete('subcategories/{subcategory}', [\App\Http\Controllers\Admin\SubcategoriesController::class, 'destroy'])
            ->name('subcategories.destroy');
    });
});
