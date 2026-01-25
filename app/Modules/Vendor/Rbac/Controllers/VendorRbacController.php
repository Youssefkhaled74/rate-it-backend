<?php

namespace App\Modules\Vendor\Rbac\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\Vendor\Rbac\Services\VendorDataService;
use App\Modules\Vendor\Rbac\Policies\VendorBranchPolicy;
use App\Modules\Vendor\Rbac\Policies\VendorVoucherPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Example: VendorRbacController
 * 
 * Demonstrates how to apply RBAC checks and scoping in practice
 */
class VendorRbacController extends BaseApiController
{
    protected VendorDataService $dataService;

    public function __construct(VendorDataService $dataService)
    {
        $this->dataService = $dataService;
    }

    /**
     * GET /api/v1/vendor/rbac/branches
     * 
     * List branches (scoped by role)
     * - VENDOR_ADMIN: all branches in their brand
     * - BRANCH_STAFF: only their assigned branch
     */
    public function listBranches(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();

        try {
            $branches = $this->dataService->getVendorBranches($vendor);
            return $this->success($branches, 'vendor.branches.list');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 403);
        }
    }

    /**
     * GET /api/v1/vendor/rbac/branches/{branchId}
     * 
     * View branch details (with authorization check)
     */
    public function viewBranch(Request $request, $branchId)
    {
        $vendor = Auth::guard('vendor')->user();

        try {
            $branch = $this->dataService->viewBranch($vendor, (int)$branchId);
            return $this->success($branch, 'vendor.branch.details');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, $e instanceof \App\Support\Exceptions\ApiException ? 403 : 400);
        }
    }

    /**
     * PUT /api/v1/vendor/rbac/branches/{branchId}/settings
     * 
     * Update branch settings (VENDOR_ADMIN only)
     */
    public function updateBranchSettings(Request $request, $branchId)
    {
        $vendor = Auth::guard('vendor')->user();

        $request->validate([
            'review_cooldown_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'working_hours' => ['nullable', 'array'],
        ]);

        try {
            $branch = $this->dataService->updateBranchSettings(
                $vendor,
                (int)$branchId,
                $request->only(['review_cooldown_days', 'working_hours'])
            );
            return $this->success($branch, 'vendor.branch.updated');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 403);
        }
    }

    /**
     * GET /api/v1/vendor/rbac/vouchers
     * 
     * List vouchers (scoped by role)
     * - VENDOR_ADMIN: all brand vouchers
     * - BRANCH_STAFF: vouchers verified at their branch
     */
    public function listVouchers(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();

        try {
            $vouchers = $this->dataService->getVendorVouchers($vendor);
            return $this->success($vouchers, 'vendor.vouchers.list');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 403);
        }
    }

    /**
     * POST /api/v1/vendor/rbac/vouchers/verify
     * 
     * Verify voucher (BRANCH_STAFF only)
     */
    public function verifyVoucher(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();

        $request->validate([
            'code' => ['required', 'string'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
        ]);

        try {
            $voucher = $this->dataService->verifyVoucher(
                $vendor,
                $request->input('code'),
                $request->input('branch_id')
            );
            return $this->success($voucher, 'vendor.voucher.verified');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 403);
        }
    }

    /**
     * GET /api/v1/vendor/rbac/brand/analytics
     * 
     * View brand analytics (VENDOR_ADMIN only)
     */
    public function viewAnalytics(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();

        try {
            $analytics = $this->dataService->getBrandAnalytics($vendor);
            return $this->success($analytics, 'vendor.analytics');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 403);
        }
    }

    /**
     * GET /api/v1/vendor/rbac/staff
     * 
     * List branch staff (VENDOR_ADMIN only)
     */
    public function listStaff(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();

        try {
            $staff = $this->dataService->getBranchStaffList($vendor);
            return $this->success($staff, 'vendor.staff.list');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 403);
        }
    }

    /**
     * GET /api/v1/vendor/rbac/reviews
     * 
     * List brand reviews (VENDOR_ADMIN only)
     */
    public function listReviews(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();

        try {
            $reviews = $this->dataService->getVendorBrandReviews($vendor);
            return $this->success($reviews, 'vendor.reviews.list');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 403);
        }
    }
}
