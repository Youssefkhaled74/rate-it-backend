<?php

namespace App\Modules\Vendor\Rbac\Policies;

use App\Models\VendorUser;
use App\Support\Exceptions\ApiException;

/**
 * VendorBrandPolicy: Controls access to brand-level resources
 * 
 * Rules:
 * - VENDOR_ADMIN: Full access to own brand
 * - BRANCH_STAFF: No access
 */
class VendorBrandPolicy
{
    /**
     * Check if vendor can view brand
     */
    public function view(VendorUser $vendor, int $brandId): bool
    {
        return $vendor->role === 'VENDOR_ADMIN' && $vendor->brand_id === $brandId;
    }

    /**
     * Check if vendor can update brand settings
     */
    public function update(VendorUser $vendor, int $brandId): bool
    {
        return $vendor->role === 'VENDOR_ADMIN' && $vendor->brand_id === $brandId;
    }

    /**
     * Check if vendor can view brand analytics/reports
     */
    public function viewAnalytics(VendorUser $vendor, int $brandId): bool
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
