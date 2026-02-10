<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\Auth\LoginController;
use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\Reviews\ReviewController;
use App\Http\Controllers\Vendor\Branches\BranchSettingsController;
use App\Http\Controllers\Vendor\BranchUsers\BranchUserController;
use App\Http\Controllers\Vendor\Vouchers\VoucherVerificationController;
use App\Http\Middleware\UseVendorGuard;
use App\Http\Middleware\VendorWebAuthenticate;
use App\Http\Middleware\VendorRole;

Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::get('login', [LoginController::class, 'show'])->middleware('guest:vendor_web')->name('login');
    Route::post('login', [LoginController::class, 'login'])->middleware('guest:vendor_web')->name('login.submit');
    Route::post('logout', [LoginController::class, 'logout'])->middleware('auth:vendor_web')->name('logout');

    Route::middleware([UseVendorGuard::class, VendorWebAuthenticate::class])->group(function () {
        Route::get('/', [DashboardController::class, 'home'])->name('home');

        // Vendor Admin only
        Route::middleware([VendorRole::class . ':VENDOR_ADMIN'])->group(function () {
            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
            Route::get('reviews/export.csv', [ReviewController::class, 'exportCsv'])->name('reviews.export.csv');
            Route::get('reviews/export.xlsx', [ReviewController::class, 'exportXlsx'])->name('reviews.export.xlsx');
            Route::get('reviews/{id}', [ReviewController::class, 'show'])->name('reviews.show');

            Route::get('branches/settings', [BranchSettingsController::class, 'index'])->name('branches.settings');
            Route::post('branches/{id}/cooldown', [BranchSettingsController::class, 'updateCooldown'])->name('branches.cooldown.update');

            Route::get('staff', [BranchUserController::class, 'index'])->name('staff.index');
            Route::get('staff/create', [BranchUserController::class, 'create'])->name('staff.create');
            Route::post('staff', [BranchUserController::class, 'store'])->name('staff.store');
            Route::get('staff/{id}/edit', [BranchUserController::class, 'edit'])->name('staff.edit');
            Route::post('staff/{id}', [BranchUserController::class, 'update'])->name('staff.update');
            Route::delete('staff/{id}', [BranchUserController::class, 'destroy'])->name('staff.destroy');
        });

        // Voucher verify & history (both roles, scoped)
        Route::get('vouchers/verify', [VoucherVerificationController::class, 'verifyForm'])->name('vouchers.verify');
        Route::post('vouchers/check', [VoucherVerificationController::class, 'check'])->name('vouchers.check');
        Route::post('vouchers/redeem', [VoucherVerificationController::class, 'redeem'])->name('vouchers.redeem');
        Route::get('vouchers/history', [VoucherVerificationController::class, 'history'])->name('vouchers.history');
    });
});
