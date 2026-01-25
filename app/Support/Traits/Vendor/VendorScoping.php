<?php

namespace App\Support\Traits\Vendor;

use App\Models\VendorUser;
use Illuminate\Database\Eloquent\Builder;

/**
 * VendorScoping: Ensures queries are scoped to vendor's brand/branch
 * 
 * Use in services to guarantee data access control at DB level.
 */
trait VendorScoping
{
    /**
     * Scope a query by vendor's brand
     * 
     * For VENDOR_ADMIN: returns their brand_id
     * For BRANCH_STAFF: returns their branch's brand_id
     * 
     * @param Builder $query
     * @param VendorUser $vendor
     * @return Builder
     */
    public function scopeByVendorBrand(Builder $query, VendorUser $vendor): Builder
    {
        $brandId = $this->getVendorBrandId($vendor);
        return $query->where('brand_id', $brandId);
    }

    /**
     * Scope a query by vendor's branch
     * 
     * Only works for BRANCH_STAFF. Throws for VENDOR_ADMIN.
     * 
     * @param Builder $query
     * @param VendorUser $vendor
     * @return Builder
     */
    public function scopeByVendorBranch(Builder $query, VendorUser $vendor): Builder
    {
        if ($vendor->role !== 'BRANCH_STAFF') {
            throw new \RuntimeException('Branch scoping only for BRANCH_STAFF');
        }
        return $query->where('branch_id', $vendor->branch_id);
    }

    /**
     * Get vendor's effective brand ID
     * 
     * @param VendorUser $vendor
     * @return int|null
     */
    public function getVendorBrandId(VendorUser $vendor): ?int
    {
        if ($vendor->role === 'VENDOR_ADMIN') {
            return $vendor->brand_id;
        }
        
        // BRANCH_STAFF: get brand from branch
        if ($vendor->branch_id && $vendor->branch) {
            return $vendor->branch->place->brand_id ?? null;
        }
        
        return null;
    }

    /**
     * Get vendor's effective branch ID
     * 
     * @param VendorUser $vendor
     * @return int|null
     */
    public function getVendorBranchId(VendorUser $vendor): ?int
    {
        if ($vendor->role === 'BRANCH_STAFF') {
            return $vendor->branch_id;
        }
        return null;
    }

    /**
     * Verify vendor can access resource by brand
     * 
     * @param VendorUser $vendor
     * @param int $resourceBrandId
     * @return bool
     */
    public function vendorCanAccessBrand(VendorUser $vendor, int $resourceBrandId): bool
    {
        return $this->getVendorBrandId($vendor) === $resourceBrandId;
    }

    /**
     * Verify vendor can access resource by branch
     * 
     * @param VendorUser $vendor
     * @param int $resourceBranchId
     * @return bool
     */
    public function vendorCanAccessBranch(VendorUser $vendor, int $resourceBranchId): bool
    {
        if ($vendor->role === 'BRANCH_STAFF') {
            return $vendor->branch_id === $resourceBranchId;
        }
        
        // VENDOR_ADMIN can access all branches in their brand
        if ($vendor->role === 'VENDOR_ADMIN') {
            // Check if branch belongs to their brand
            $branch = \App\Models\Branch::find($resourceBranchId);
            if (!$branch) return false;
            
            return $branch->place->brand_id === $vendor->brand_id;
        }
        
        return false;
    }
}
