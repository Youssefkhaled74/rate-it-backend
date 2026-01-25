<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Vendor\Rbac\Controllers\VendorRbacController;
use App\Http\Middleware\VendorAuthenticate;
use App\Http\Middleware\VendorPermissionWithScoping;

/**
 * Vendor RBAC Routes with Role Scoping
 * 
 * All routes require VendorAuthenticate middleware
 * Additional middleware enforces role-based access
 * 
 * Role enforcement examples:
 *   - VendorPermissionWithScoping::class.':permission:VENDOR_ADMIN'
 *   - VendorPermissionWithScoping::class.':permission:BRANCH_STAFF'
 *   - VendorPermissionWithScoping::class.':permission:ANY'
 */

Route::middleware([VendorAuthenticate::class])->group(function () {
    
    /**
     * Branch Management (Role-scoped endpoints)
     */
    Route::prefix('branches')->group(function () {
        // GET /api/v1/vendor/rbac/branches
        // VENDOR_ADMIN: all branches in brand
        // BRANCH_STAFF: only their branch
        Route::get('/', [VendorRbacController::class, 'listBranches']);

        // GET /api/v1/vendor/rbac/branches/{branchId}
        Route::get('{branchId}', [VendorRbacController::class, 'viewBranch']);

        // PUT /api/v1/vendor/rbac/branches/{branchId}/settings
        // VENDOR_ADMIN only (enforced at service level)
        Route::put('{branchId}/settings', [VendorRbacController::class, 'updateBranchSettings']);
    });

    /**
     * Voucher Management (Role-scoped endpoints)
     */
    Route::prefix('vouchers')->group(function () {
        // GET /api/v1/vendor/rbac/vouchers
        // VENDOR_ADMIN: all brand vouchers
        // BRANCH_STAFF: only verified at their branch
        Route::get('/', [VendorRbacController::class, 'listVouchers']);

        // POST /api/v1/vendor/rbac/vouchers/verify
        // BRANCH_STAFF only (enforced at service level)
        Route::post('verify', [VendorRbacController::class, 'verifyVoucher']);
    });

    /**
     * Brand Analytics (VENDOR_ADMIN only)
     */
    Route::prefix('brand')->group(function () {
        // GET /api/v1/vendor/rbac/brand/analytics
        Route::get('analytics', [VendorRbacController::class, 'viewAnalytics']);

        // GET /api/v1/vendor/rbac/brand/reviews
        Route::get('reviews', [VendorRbacController::class, 'listReviews']);
    });

    /**
     * Staff Management (VENDOR_ADMIN only)
     */
    Route::prefix('staff')->group(function () {
        // GET /api/v1/vendor/rbac/staff
        Route::get('/', [VendorRbacController::class, 'listStaff']);
    });
});
