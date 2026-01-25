<?php

namespace App\Modules\Vendor\Rbac\Policies;

use App\Models\VendorUser;
use App\Models\Branch;
use App\Support\Exceptions\ApiException;

/**
 * VendorBranchPolicy: Controls access to branch-level resources
 * 
 * Rules:
 * - VENDOR_ADMIN: Full access to all branches in own brand
 * - BRANCH_STAFF: Access ONLY to assigned branch
 */
class VendorBranchPolicy
{
    /**
     * Check if vendor can view branch
     */
    public function view(VendorUser $vendor, int $branchId): bool
    {
        if ($vendor->role === 'BRANCH_STAFF') {
            return $vendor->branch_id === $branchId;
        }
        
        if ($vendor->role === 'VENDOR_ADMIN') {
            // Check if branch belongs to their brand
            $branch = Branch::find($branchId);
            return $branch && $branch->place && $branch->place->brand_id === $vendor->brand_id;
        }
        
        return false;
    }

    /**
     * Check if vendor can manage branch (settings, staff, etc.)
     */
    public function manage(VendorUser $vendor, int $branchId): bool
    {
        if ($vendor->role !== 'VENDOR_ADMIN') {
            return false;
        }
        
        $branch = Branch::find($branchId);
        return $branch && $branch->place && $branch->place->brand_id === $vendor->brand_id;
    }

    /**
     * Check if vendor can verify vouchers at branch
     */
    public function verifyVouchers(VendorUser $vendor, int $branchId): bool
    {
        // BRANCH_STAFF can only verify in own branch
        if ($vendor->role === 'BRANCH_STAFF') {
            return $vendor->branch_id === $branchId;
        }
        
        // VENDOR_ADMIN cannot verify (admin delegates to branch staff)
        return false;
    }

    /**
     * Throw authorization exception if denied
     */
    public static function authorize(VendorUser $vendor, string $ability, int $branchId): void
    {
        $policy = new self();
        $method = $ability;
        
        if (!method_exists($policy, $method) || !$policy->$method($vendor, $branchId)) {
            throw new ApiException(__('auth.forbidden'), 403);
        }
    }
}
