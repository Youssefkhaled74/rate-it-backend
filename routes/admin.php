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
        Route::get('categories/{category}', [\App\Http\Controllers\Admin\CategoriesController::class, 'show'])->name('categories.show');
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

    // Brands module
    Route::middleware('auth:admin_web')->group(function () {
        Route::get('brands', [\App\Http\Controllers\Admin\BrandsController::class, 'index'])->name('brands.index');
        Route::get('brands/create', [\App\Http\Controllers\Admin\BrandsController::class, 'create'])->name('brands.create');
        Route::get('brands/{brand}', [\App\Http\Controllers\Admin\BrandsController::class, 'show'])->name('brands.show');
        Route::post('brands', [\App\Http\Controllers\Admin\BrandsController::class, 'store'])->name('brands.store');
        Route::get('brands/{brand}/edit', [\App\Http\Controllers\Admin\BrandsController::class, 'edit'])->name('brands.edit');
        Route::match(['put', 'patch'], 'brands/{brand}', [\App\Http\Controllers\Admin\BrandsController::class, 'update'])->name('brands.update');
        Route::patch('brands/{brand}/toggle', [\App\Http\Controllers\Admin\BrandsController::class, 'toggle'])->name('brands.toggle');
        Route::delete('brands/{brand}', [\App\Http\Controllers\Admin\BrandsController::class, 'destroy'])->name('brands.destroy');
    });

    // Places module
    Route::middleware('auth:admin_web')->group(function () {
        Route::get('places', [\App\Http\Controllers\Admin\PlacesController::class, 'index'])->name('places.index');
        Route::get('places/create', [\App\Http\Controllers\Admin\PlacesController::class, 'create'])->name('places.create');
        Route::get('places/{place}', [\App\Http\Controllers\Admin\PlacesController::class, 'show'])->name('places.show');
        Route::post('places', [\App\Http\Controllers\Admin\PlacesController::class, 'store'])->name('places.store');
        Route::get('places/{place}/edit', [\App\Http\Controllers\Admin\PlacesController::class, 'edit'])->name('places.edit');
        Route::match(['put', 'patch'], 'places/{place}', [\App\Http\Controllers\Admin\PlacesController::class, 'update'])->name('places.update');
        Route::patch('places/{place}/toggle', [\App\Http\Controllers\Admin\PlacesController::class, 'toggle'])->name('places.toggle');
        Route::delete('places/{place}', [\App\Http\Controllers\Admin\PlacesController::class, 'destroy'])->name('places.destroy');
    });

    // Branches module
    Route::middleware('auth:admin_web')->group(function () {
        Route::get('branches', [\App\Http\Controllers\Admin\BranchesController::class, 'index'])->name('branches.index');
        Route::get('branches/create', [\App\Http\Controllers\Admin\BranchesController::class, 'create'])->name('branches.create');
        Route::get('branches/{branch}', [\App\Http\Controllers\Admin\BranchesController::class, 'show'])->name('branches.show');
        Route::post('branches', [\App\Http\Controllers\Admin\BranchesController::class, 'store'])->name('branches.store');
        Route::get('branches/{branch}/edit', [\App\Http\Controllers\Admin\BranchesController::class, 'edit'])->name('branches.edit');
        Route::match(['put', 'patch'], 'branches/{branch}', [\App\Http\Controllers\Admin\BranchesController::class, 'update'])->name('branches.update');
        Route::patch('branches/{branch}/toggle', [\App\Http\Controllers\Admin\BranchesController::class, 'toggle'])->name('branches.toggle');
        Route::delete('branches/{branch}', [\App\Http\Controllers\Admin\BranchesController::class, 'destroy'])->name('branches.destroy');
    });

    // Banners & Onboarding module
    Route::middleware('auth:admin_web')->group(function () {
        Route::get('banners', [\App\Http\Controllers\Admin\BannersController::class, 'index'])->name('banners.index');
        Route::get('banners/create', [\App\Http\Controllers\Admin\BannersController::class, 'create'])->name('banners.create');
        Route::get('banners/{banner}', [\App\Http\Controllers\Admin\BannersController::class, 'show'])->name('banners.show');
        Route::post('banners', [\App\Http\Controllers\Admin\BannersController::class, 'store'])->name('banners.store');
        Route::get('banners/{banner}/edit', [\App\Http\Controllers\Admin\BannersController::class, 'edit'])->name('banners.edit');
        Route::match(['put', 'patch'], 'banners/{banner}', [\App\Http\Controllers\Admin\BannersController::class, 'update'])->name('banners.update');
        Route::patch('banners/{banner}/toggle', [\App\Http\Controllers\Admin\BannersController::class, 'toggle'])->name('banners.toggle');
        Route::delete('banners/{banner}', [\App\Http\Controllers\Admin\BannersController::class, 'destroy'])->name('banners.destroy');

        Route::get('onboardings/create', [\App\Http\Controllers\Admin\OnboardingsController::class, 'create'])->name('onboardings.create');
        Route::get('onboardings/{onboarding}', [\App\Http\Controllers\Admin\OnboardingsController::class, 'show'])->name('onboardings.show');
        Route::post('onboardings', [\App\Http\Controllers\Admin\OnboardingsController::class, 'store'])->name('onboardings.store');
        Route::get('onboardings/{onboarding}/edit', [\App\Http\Controllers\Admin\OnboardingsController::class, 'edit'])->name('onboardings.edit');
        Route::match(['put', 'patch'], 'onboardings/{onboarding}', [\App\Http\Controllers\Admin\OnboardingsController::class, 'update'])->name('onboardings.update');
        Route::patch('onboardings/{onboarding}/toggle', [\App\Http\Controllers\Admin\OnboardingsController::class, 'toggle'])->name('onboardings.toggle');
        Route::delete('onboardings/{onboarding}', [\App\Http\Controllers\Admin\OnboardingsController::class, 'destroy'])->name('onboardings.destroy');
    });
});
