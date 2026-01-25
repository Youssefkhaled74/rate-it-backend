<?php

namespace App\Modules\Vendor\Rbac\Services;

use App\Models\VendorUser;
use App\Models\Review;
use App\Models\Voucher;
use App\Models\Branch;
use App\Support\Traits\Vendor\VendorScoping;
use App\Support\Traits\Vendor\VendorRoleCheck;
use App\Modules\Vendor\Rbac\Policies\VendorBrandPolicy;
use App\Modules\Vendor\Rbac\Policies\VendorBranchPolicy;
use App\Modules\Vendor\Rbac\Policies\VendorVoucherPolicy;
use App\Support\Exceptions\ApiException;

/**
 * VendorDataService: Demonstrates scoped queries for vendor access
 * 
 * All queries are automatically scoped to vendor's brand/branch
 */
class VendorDataService
{
    use VendorScoping, VendorRoleCheck;

    /**
     * Get reviews for vendor's brand (VENDOR_ADMIN only)
     * 
     * @throws ApiException
     */
    public function getVendorBrandReviews(VendorUser $vendor, int $limit = 50)
    {
        $this->requireVendorAdmin($vendor, 'vendor.only_admin_can_view_brand_reviews');

        $brandId = $this->getVendorBrandId($vendor);
        
        return Review::where('status', 'ACTIVE')
            ->whereHas('branch.place', function ($q) use ($brandId) {
                $q->where('brand_id', $brandId);
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get branch staff list for vendor's brand (VENDOR_ADMIN only)
     * 
     * @throws ApiException
     */
    public function getBranchStaffList(VendorUser $vendor)
    {
        $this->requireVendorAdmin($vendor, 'vendor.only_admin_can_manage_staff');

        $brandId = $this->getVendorBrandId($vendor);

        return VendorUser::where('brand_id', $brandId)
            ->where('role', 'BRANCH_STAFF')
            ->where('is_active', true)
            ->with('branch')
            ->get();
    }

    /**
     * Get branches for vendor (scoped appropriately)
     */
    public function getVendorBranches(VendorUser $vendor)
    {
        $brandId = $this->getVendorBrandId($vendor);

        $query = Branch::whereHas('place', function ($q) use ($brandId) {
            $q->where('brand_id', $brandId);
        });

        // BRANCH_STAFF can only see their own branch
        if ($this->isBranchStaff($vendor)) {
            $query->where('id', $vendor->branch_id);
        }

        return $query->get();
    }

    /**
     * Get vouchers for vendor
     * 
     * - VENDOR_ADMIN: all vouchers for brand
     * - BRANCH_STAFF: vouchers verified at their branch
     */
    public function getVendorVouchers(VendorUser $vendor, int $limit = 50)
    {
        if ($this->isVendorAdmin($vendor)) {
            $brandId = $this->getVendorBrandId($vendor);
            
            return Voucher::where('brand_id', $brandId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }

        if ($this->isBranchStaff($vendor)) {
            return Voucher::where('used_branch_id', $vendor->branch_id)
                ->where('status', 'USED')
                ->orderBy('used_at', 'desc')
                ->limit($limit)
                ->get();
        }

        return collect();
    }

    /**
     * Update branch settings
     * 
     * @throws ApiException
     */
    public function updateBranchSettings(VendorUser $vendor, int $branchId, array $data): Branch
    {
        // Check authorization
        VendorBranchPolicy::authorize($vendor, 'manage', $branchId);

        $branch = Branch::findOrFail($branchId);
        
        // Only certain fields can be updated
        $allowed = ['review_cooldown_days', 'working_hours'];
        $updates = array_intersect_key($data, array_flip($allowed));
        
        $branch->update($updates);
        return $branch;
    }

    /**
     * Verify voucher at branch
     * 
     * @throws ApiException
     */
    public function verifyVoucher(VendorUser $vendor, string $voucherCode, int $branchId): Voucher
    {
        // Check if staff can verify at this branch
        VendorVoucherPolicy::authorize($vendor, 'verify', $branchId);

        $voucher = Voucher::where('code', $voucherCode)->firstOrFail();

        // Verify voucher is for this branch's brand
        if ($voucher->brand_id !== $this->getVendorBrandId($vendor)) {
            throw new ApiException('Voucher does not belong to your brand', 403);
        }

        // Mark as used
        $voucher->update([
            'status' => 'USED',
            'used_at' => now(),
            'used_branch_id' => $branchId,
            'verified_by_vendor_user_id' => $vendor->id,
        ]);

        return $voucher;
    }

    /**
     * View branch details
     * 
     * @throws ApiException
     */
    public function viewBranch(VendorUser $vendor, int $branchId): Branch
    {
        VendorBranchPolicy::authorize($vendor, 'view', $branchId);
        return Branch::findOrFail($branchId);
    }

    /**
     * View brand analytics
     * 
     * @throws ApiException
     */
    public function getBrandAnalytics(VendorUser $vendor)
    {
        VendorBrandPolicy::authorize($vendor, 'viewAnalytics', $vendor->brand_id);

        $brandId = $vendor->brand_id;

        return [
            'total_reviews' => Review::whereHas('branch.place', function ($q) use ($brandId) {
                $q->where('brand_id', $brandId);
            })->count(),
            'total_vouchers_issued' => Voucher::where('brand_id', $brandId)->count(),
            'vouchers_verified' => Voucher::where('brand_id', $brandId)
                ->where('status', 'USED')
                ->count(),
            'branches' => Branch::whereHas('place', function ($q) use ($brandId) {
                $q->where('brand_id', $brandId);
            })->count(),
        ];
    }
}
