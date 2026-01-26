<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\app\Http\Controllers\Auth\LoginController;
use Modules\Admin\app\Http\Controllers\Auth\ForgotPasswordController;
use Modules\Admin\app\Http\Controllers\Auth\ResetPasswordController;
use Modules\Admin\app\Http\Controllers\ProfileController;
use Modules\Admin\app\Http\Controllers\AdminsController;
use Modules\Admin\app\Http\Controllers\LocaleController;
use Modules\Admin\app\Http\Controllers\DashboardController;

/**
 * Admin Blade Dashboard Routes
 * 
 * Prefix: /admin
 * Middleware: web (registered in service provider)
 * Name prefix: admin. (registered in service provider)
 */

// ============================================================================
// GUEST ROUTES (Unauthenticated)
// ============================================================================

Route::middleware('guest:admin')->group(function () {
    // Login
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    // Forgot Password
    Route::get('password/forgot', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

    // Reset Password
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('password/update', [ResetPasswordController::class, 'resetPassword'])->name('password.update');
});

// ============================================================================
// AUTHENTICATED ROUTES (Require admin guard + auth)
// ============================================================================

Route::middleware(['auth:admin', 'admin.locale', 'admin.guard'])->group(function () {

    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('update', [ProfileController::class, 'update'])->name('update');
        Route::get('password', [ProfileController::class, 'showChangePasswordForm'])->name('password');
        Route::put('password/update', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    // Admin Management (CRUD)
    Route::resource('admins', AdminsController::class)->except(['show']);
    Route::post('admins/{admin}/deactivate', [AdminsController::class, 'deactivate'])->name('admins.deactivate');
    Route::post('admins/{admin}/activate', [AdminsController::class, 'activate'])->name('admins.activate');

    // Locale Switching
    Route::get('locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
});

