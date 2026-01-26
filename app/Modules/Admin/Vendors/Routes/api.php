<?php


use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuthenticate;

Route::prefix('vendors')->group(function () {
    // List all vendors
    Route::get('/', [\App\Modules\Admin\Vendors\Controllers\VendorsController::class, 'index']);

    // Get vendor details
    Route::get('/{id}', [\App\Modules\Admin\Vendors\Controllers\VendorsController::class, 'show']);

    // Create new vendor admin
    Route::post('/', [\App\Modules\Admin\Vendors\Controllers\VendorsController::class, 'store']);

    // Update vendor
    Route::put('/{id}', [\App\Modules\Admin\Vendors\Controllers\VendorsController::class, 'update']);

    // Delete vendor
    Route::delete('/{id}', [\App\Modules\Admin\Vendors\Controllers\VendorsController::class, 'destroy']);

    // Restore deleted vendor
    Route::post('/{id}/restore', [\App\Modules\Admin\Vendors\Controllers\VendorsController::class, 'restore']);
});
