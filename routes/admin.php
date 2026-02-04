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

    Route::get('lang/{lang}', [\App\Http\Controllers\Admin\LocaleController::class, 'switch'])
        ->middleware('auth:admin_web')
        ->name('lang.switch');

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
        Route::get('users/export', [\App\Http\Controllers\Admin\UsersController::class, 'export'])->name('users.export');
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

    // Rating Questions (Criteria)
    Route::middleware('auth:admin_web')->group(function () {
        Route::get('rating-questions', [\App\Http\Controllers\Admin\RatingQuestionsController::class, 'index'])->name('rating-questions.index');
        Route::get('rating-questions/create', [\App\Http\Controllers\Admin\RatingQuestionsController::class, 'create'])->name('rating-questions.create');
        Route::post('rating-questions', [\App\Http\Controllers\Admin\RatingQuestionsController::class, 'store'])->name('rating-questions.store');
        Route::get('rating-questions/{question}/edit', [\App\Http\Controllers\Admin\RatingQuestionsController::class, 'edit'])->name('rating-questions.edit');
        Route::match(['put', 'patch'], 'rating-questions/{question}', [\App\Http\Controllers\Admin\RatingQuestionsController::class, 'update'])->name('rating-questions.update');
        Route::patch('rating-questions/{question}/toggle', [\App\Http\Controllers\Admin\RatingQuestionsController::class, 'toggle'])->name('rating-questions.toggle');
        Route::delete('rating-questions/{question}', [\App\Http\Controllers\Admin\RatingQuestionsController::class, 'destroy'])->name('rating-questions.destroy');
    });

    // User Lookups
    Route::middleware('auth:admin_web')->group(function () {
        Route::get('lookups', [\App\Http\Controllers\Admin\LookupsController::class, 'index'])->name('lookups.index');

        Route::get('lookups/genders', [\App\Http\Controllers\Admin\GendersController::class, 'index'])->name('lookups.genders.index');
        Route::get('lookups/genders/create', [\App\Http\Controllers\Admin\GendersController::class, 'create'])->name('lookups.genders.create');
        Route::post('lookups/genders', [\App\Http\Controllers\Admin\GendersController::class, 'store'])->name('lookups.genders.store');
        Route::get('lookups/genders/{gender}/edit', [\App\Http\Controllers\Admin\GendersController::class, 'edit'])->name('lookups.genders.edit');
        Route::match(['put', 'patch'], 'lookups/genders/{gender}', [\App\Http\Controllers\Admin\GendersController::class, 'update'])->name('lookups.genders.update');
        Route::patch('lookups/genders/{gender}/toggle', [\App\Http\Controllers\Admin\GendersController::class, 'toggle'])->name('lookups.genders.toggle');
        Route::delete('lookups/genders/{gender}', [\App\Http\Controllers\Admin\GendersController::class, 'destroy'])->name('lookups.genders.destroy');

        Route::get('lookups/nationalities', [\App\Http\Controllers\Admin\NationalitiesController::class, 'index'])->name('lookups.nationalities.index');
        Route::get('lookups/nationalities/create', [\App\Http\Controllers\Admin\NationalitiesController::class, 'create'])->name('lookups.nationalities.create');
        Route::post('lookups/nationalities', [\App\Http\Controllers\Admin\NationalitiesController::class, 'store'])->name('lookups.nationalities.store');
        Route::get('lookups/nationalities/{nationality}/edit', [\App\Http\Controllers\Admin\NationalitiesController::class, 'edit'])->name('lookups.nationalities.edit');
        Route::match(['put', 'patch'], 'lookups/nationalities/{nationality}', [\App\Http\Controllers\Admin\NationalitiesController::class, 'update'])->name('lookups.nationalities.update');
        Route::patch('lookups/nationalities/{nationality}/toggle', [\App\Http\Controllers\Admin\NationalitiesController::class, 'toggle'])->name('lookups.nationalities.toggle');
        Route::delete('lookups/nationalities/{nationality}', [\App\Http\Controllers\Admin\NationalitiesController::class, 'destroy'])->name('lookups.nationalities.destroy');

        Route::get('lookups/cities', [\App\Http\Controllers\Admin\CitiesController::class, 'index'])->name('lookups.cities.index');
        Route::get('lookups/cities/create', [\App\Http\Controllers\Admin\CitiesController::class, 'create'])->name('lookups.cities.create');
        Route::post('lookups/cities', [\App\Http\Controllers\Admin\CitiesController::class, 'store'])->name('lookups.cities.store');
        Route::get('lookups/cities/{city}/edit', [\App\Http\Controllers\Admin\CitiesController::class, 'edit'])->name('lookups.cities.edit');
        Route::match(['put', 'patch'], 'lookups/cities/{city}', [\App\Http\Controllers\Admin\CitiesController::class, 'update'])->name('lookups.cities.update');
        Route::patch('lookups/cities/{city}/toggle', [\App\Http\Controllers\Admin\CitiesController::class, 'toggle'])->name('lookups.cities.toggle');
        Route::delete('lookups/cities/{city}', [\App\Http\Controllers\Admin\CitiesController::class, 'destroy'])->name('lookups.cities.destroy');

        Route::get('lookups/areas', [\App\Http\Controllers\Admin\AreasController::class, 'index'])->name('lookups.areas.index');
        Route::get('lookups/areas/create', [\App\Http\Controllers\Admin\AreasController::class, 'create'])->name('lookups.areas.create');
        Route::post('lookups/areas', [\App\Http\Controllers\Admin\AreasController::class, 'store'])->name('lookups.areas.store');
        Route::get('lookups/areas/{area}/edit', [\App\Http\Controllers\Admin\AreasController::class, 'edit'])->name('lookups.areas.edit');
        Route::match(['put', 'patch'], 'lookups/areas/{area}', [\App\Http\Controllers\Admin\AreasController::class, 'update'])->name('lookups.areas.update');
        Route::patch('lookups/areas/{area}/toggle', [\App\Http\Controllers\Admin\AreasController::class, 'toggle'])->name('lookups.areas.toggle');
        Route::delete('lookups/areas/{area}', [\App\Http\Controllers\Admin\AreasController::class, 'destroy'])->name('lookups.areas.destroy');
    });
});
