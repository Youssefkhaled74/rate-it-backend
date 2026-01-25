<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Vendor\Branches\Controllers\BranchController;
use App\Http\Middleware\VendorAuthenticate;

/**
 * Vendor Branch Management Routes
 * 
 * All routes require VendorAuthenticate middleware
 * Role-based access is enforced in service layer
 */

Route::middleware([VendorAuthenticate::class])->group(function () {
    Route::prefix('branches')->group(function () {
        // GET /api/v1/vendor/branches
        // List branches (scoped by role: admin sees all, staff sees one)
        Route::get('/', [BranchController::class, 'index']);

        // POST /api/v1/vendor/branches
        // Create new branch (VENDOR_ADMIN only)
        Route::post('/', [BranchController::class, 'store']);

        // GET /api/v1/vendor/branches/{branchId}
        // View branch details (with authorization)
        Route::get('{branchId}', [BranchController::class, 'show']);

        // PUT /api/v1/vendor/branches/{branchId}
        // Update branch details (VENDOR_ADMIN only)
        Route::put('{branchId}', [BranchController::class, 'update']);

        // DELETE /api/v1/vendor/branches/{branchId}
        // Delete branch (VENDOR_ADMIN only)
        Route::delete('{branchId}', [BranchController::class, 'destroy']);

        // PATCH /api/v1/vendor/branches/{branchId}/cooldown
        // Update review cooldown (VENDOR_ADMIN only)
        Route::patch('{branchId}/cooldown', [BranchController::class, 'updateCooldown']);
    });
});
