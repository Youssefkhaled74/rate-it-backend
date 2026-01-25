<?php

namespace App\Modules\Vendor\Rbac\Policies;

use App\Models\VendorUser;
use App\Support\Exceptions\ApiException;

/**
 * VendorVoucherPolicy: Controls access to voucher operations
 * 
 * Rules:
 * - VENDOR_ADMIN: Can view all vouchers for brand, manage settings
 * - BRANCH_STAFF: Can verify/redeem vouchers ONLY for their branch
 */
class VendorVoucherPolicy
{
    /**
     * Check if vendor can verify voucher at branch
     */
    public function verify(VendorUser $vendor, int $branchId): bool
    {
        // Only BRANCH_STAFF can verify
        return $vendor->role === 'BRANCH_STAFF' && $vendor->branch_id === $branchId;
    }

    /**
     * Check if vendor can view vouchers
     */
    public function list(VendorUser $vendor, int $brandId): bool
    {
        // VENDOR_ADMIN: can view all vouchers for brand
        if ($vendor->role === 'VENDOR_ADMIN') {
            return $vendor->brand_id === $brandId;
        }
        
        // BRANCH_STAFF: can view vouchers for their branch only
        return false;
    }

    /**
     * Check if vendor can export voucher reports
     */
    public function export(VendorUser $vendor, int $brandId): bool
    {
        return $vendor->role === 'VENDOR_ADMIN' && $vendor->brand_id === $brandId;
    }

    /**
     * Throw authorization exception if denied
     */
    public static function authorize(VendorUser $vendor, string $ability, int $resourceId): void
    {
        $policy = new self();
        $method = $ability;
        
        if (!method_exists($policy, $method) || !$policy->$method($vendor, $resourceId)) {
            throw new ApiException(__('auth.forbidden'), 403);
        }
    }
}
