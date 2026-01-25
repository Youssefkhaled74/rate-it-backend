<?php

namespace App\Modules\Vendor\Branches\Services;

use App\Models\Branch;
use App\Models\VendorUser;
use App\Support\Traits\Vendor\VendorScoping;
use App\Support\Traits\Vendor\VendorRoleCheck;
use App\Modules\Vendor\Rbac\Policies\VendorBranchPolicy;
use App\Support\Exceptions\ApiException;

class BranchService
{
    use VendorScoping, VendorRoleCheck;

    /**
     * List branches accessible by vendor
     * 
     * - VENDOR_ADMIN: all branches in their brand
     * - BRANCH_STAFF: only their assigned branch
     */
    public function listBranches(VendorUser $vendor)
    {
        $brandId = $this->getVendorBrandId($vendor);

        $query = Branch::whereHas('place', function ($q) use ($brandId) {
            $q->where('brand_id', $brandId);
        });

        // BRANCH_STAFF can only see their own branch
        if ($this->isBranchStaff($vendor)) {
            $query->where('id', $vendor->branch_id);
        }

        return $query->with('place')->orderBy('name')->get();
    }

    /**
     * Get single branch with authorization check
     * 
     * @throws ApiException
     */
    public function getBranch(VendorUser $vendor, int $branchId): Branch
    {
        VendorBranchPolicy::authorize($vendor, 'view', $branchId);

        $branch = Branch::with('place')->findOrFail($branchId);
        return $branch;
    }

    /**
     * Update branch cooldown setting
     * 
     * VENDOR_ADMIN only
     * 
     * @throws ApiException
     */
    public function updateCooldown(VendorUser $vendor, int $branchId, int $cooldownDays): Branch
    {
        // Check authorization
        VendorBranchPolicy::authorize($vendor, 'manage', $branchId);

        $branch = Branch::findOrFail($branchId);

        // Verify branch belongs to vendor's brand
        if (!$this->vendorCanAccessBrand($vendor, $branch->place->brand_id)) {
            throw new ApiException(__('auth.forbidden'), 403);
        }

        // Update cooldown
        $branch->update(['review_cooldown_days' => $cooldownDays]);

        return $branch;
    }

    /**
     * Create new branch
     * VENDOR_ADMIN only
     * 
     * @throws ApiException
     */
    public function createBranch(VendorUser $vendor, array $data): Branch
    {
        // Only VENDOR_ADMIN can create branches
        if (!$this->isVendorAdmin($vendor)) {
            throw new ApiException(__('auth.forbidden'), 403);
        }

        // Verify place belongs to vendor's brand
        $place = \App\Models\Place::findOrFail($data['place_id']);
        if (!$this->vendorCanAccessBrand($vendor, $place->brand_id)) {
            throw new ApiException(__('auth.forbidden'), 403);
        }

        // Create branch
        $branch = Branch::create([
            'place_id' => $data['place_id'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'address' => $data['address'],
            'working_hours' => $data['working_hours'] ?? [],
        ]);

        return $branch->load('place');
    }

    /**
     * Update branch details
     * VENDOR_ADMIN only
     * 
     * @throws ApiException
     */
    public function updateBranch(VendorUser $vendor, int $branchId, array $data): Branch
    {
        // Check authorization
        VendorBranchPolicy::authorize($vendor, 'manage', $branchId);

        $branch = Branch::findOrFail($branchId);

        // Verify branch belongs to vendor's brand
        if (!$this->vendorCanAccessBrand($vendor, $branch->place->brand_id)) {
            throw new ApiException(__('auth.forbidden'), 403);
        }

        // Update branch
        $branch->update(array_filter([
            'name' => $data['name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'working_hours' => $data['working_hours'] ?? null,
        ], fn($value) => $value !== null));

        return $branch->load('place');
    }

    /**
     * Delete branch
     * VENDOR_ADMIN only
     * 
     * @throws ApiException
     */
    public function deleteBranch(VendorUser $vendor, int $branchId): bool
    {
        // Check authorization
        VendorBranchPolicy::authorize($vendor, 'manage', $branchId);

        $branch = Branch::findOrFail($branchId);

        // Verify branch belongs to vendor's brand
        if (!$this->vendorCanAccessBrand($vendor, $branch->place->brand_id)) {
            throw new ApiException(__('auth.forbidden'), 403);
        }

        // Check if branch has staff members
        if ($branch->vendorUsers()->count() > 0) {
            throw new ApiException(__('vendor.branch.has_staff'), 400);
        }

        // Check if branch has active reviews (optional, depending on business logic)
        if ($branch->reviews()->count() > 0) {
            throw new ApiException(__('vendor.branch.has_reviews'), 400);
        }

        return $branch->delete();
    }

    /**
     * Get branch by ID (no authorization check)
     * Useful for internal queries
     */
    public function find(int $id): ?Branch
    {
        return Branch::find($id);
    }
}
