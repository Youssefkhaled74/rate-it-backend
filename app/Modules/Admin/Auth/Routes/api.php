<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Admin\Auth\Controllers\AuthController;
use App\Modules\Admin\Rbac\Controllers\RolesController;
use App\Modules\Admin\Rbac\Controllers\PermissionsController;
use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\AdminPermission;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware([AdminAuthenticate::class])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

// RBAC (protected by admin auth + permission checks)
Route::middleware([AdminAuthenticate::class, AdminPermission::class.':rbac.roles.manage'])->group(function () {
    Route::get('roles', [RolesController::class, 'index']);
    Route::post('roles', [RolesController::class, 'store']);
    Route::post('roles/{role}/sync-permissions', [RolesController::class, 'syncPermissions']);
});

Route::middleware([AdminAuthenticate::class, AdminPermission::class.':rbac.permissions.manage'])->group(function () {
    Route::get('permissions', [PermissionsController::class, 'index']);
});
