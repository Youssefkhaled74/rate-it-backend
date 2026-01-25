<?php

namespace App\Support\Traits\Vendor;

use App\Models\VendorUser;

/**
 * VendorRoleCheck: Simple role-based checks
 * 
 * Use in controllers/services for role validation
 */
trait VendorRoleCheck
{
    /**
     * Check if vendor is admin for their brand
     */
    public function isVendorAdmin(VendorUser $vendor): bool
    {
        return $vendor->role === 'VENDOR_ADMIN' && $vendor->is_active;
    }

    /**
     * Check if vendor is staff for their branch
     */
    public function isBranchStaff(VendorUser $vendor): bool
    {
        return $vendor->role === 'BRANCH_STAFF' && $vendor->is_active && $vendor->branch_id;
    }

    /**
     * Throw if vendor is not admin
     * 
     * @throws \Exception
     */
    public function requireVendorAdmin(VendorUser $vendor, string $message = 'Requires vendor admin role'): void
    {
        if (!$this->isVendorAdmin($vendor)) {
            throw new \Exception($message);
        }
    }

    /**
     * Throw if vendor is not branch staff
     * 
     * @throws \Exception
     */
    public function requireBranchStaff(VendorUser $vendor, string $message = 'Requires branch staff role'): void
    {
        if (!$this->isBranchStaff($vendor)) {
            throw new \Exception($message);
        }
    }
}
