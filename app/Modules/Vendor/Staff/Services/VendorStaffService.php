<?php

namespace App\Modules\Vendor\Staff\Services;

use App\Models\VendorUser;
use App\Models\Branch;
use App\Support\Traits\Vendor\VendorScoping;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class VendorStaffService
{
    use VendorScoping;

    /**
     * List staff members for vendor's brand
     */
    public function list(VendorUser $vendor, array $filters): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 15);
        $brandId = $this->getVendorBrandId($vendor);

        $query = VendorUser::where('brand_id', $brandId)
            ->where('role', 'BRANCH_STAFF')
            ->with(['branch:id,name,name_en,name_ar']);

        // Filter by branch
        if (! empty($filters['branch_id'])) {
            $branchId = (int) $filters['branch_id'];
            
            // Verify branch belongs to vendor's brand
            $branch = Branch::find($branchId);
            if (! $branch || (int) $branch->brand_id !== (int) $brandId) {
                return VendorUser::paginate(0); // Empty paginator
            }
            
            $query->where('branch_id', $branchId);
        }

        // Search by name or phone
        if (! empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // Filter by active status
        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        $query->orderBy('created_at', 'desc');

        $page = (int) ($filters['page'] ?? 1);
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get staff member details
     */
    public function find(VendorUser $vendor, int $staffId): ?VendorUser
    {
        $brandId = $this->getVendorBrandId($vendor);
        
        return VendorUser::where('id', $staffId)
            ->where('brand_id', $brandId)
            ->where('role', 'BRANCH_STAFF')
            ->with(['branch', 'brand'])
            ->first();
    }

    /**
     * Create new staff member
     */
    public function create(VendorUser $vendor, array $data): VendorUser
    {
        $brandId = $this->getVendorBrandId($vendor);
        $branchId = (int) $data['branch_id'];

        // Verify branch belongs to vendor's brand
        $branch = Branch::find($branchId);
        if (! $branch || (int) $branch->brand_id !== (int) $brandId) {
            throw new \Exception('Branch does not belong to your brand');
        }

        // Generate temporary password
        $tempPassword = Str::random(12);

        $staff = VendorUser::create([
            'brand_id' => $brandId,
            'branch_id' => $branchId,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'password_hash' => Hash::make($tempPassword),
            'role' => 'BRANCH_STAFF',
            'is_active' => true,
        ]);

        // Store temp password on object (for return to client during creation only)
        $staff->setAttribute('temporary_password', $tempPassword);

        return $staff;
    }

    /**
     * Update staff member
     */
    public function update(VendorUser $vendor, int $staffId, array $data): ?VendorUser
    {
        $staff = $this->find($vendor, $staffId);
        if (! $staff) {
            return null;
        }

        // Update name if provided
        if (! empty($data['name'])) {
            $staff->name = $data['name'];
        }

        // Update branch if provided (with validation)
        if (! empty($data['branch_id'])) {
            $brandId = $this->getVendorBrandId($vendor);
            $newBranchId = (int) $data['branch_id'];

            $branch = Branch::find($newBranchId);
            if (! $branch || (int) $branch->brand_id !== (int) $brandId) {
                throw new \Exception('Branch does not belong to your brand');
            }

            $staff->branch_id = $newBranchId;
        }

        // Update active status if provided
        if (isset($data['is_active'])) {
            $staff->is_active = (bool) $data['is_active'];
        }

        $staff->save();

        return $this->find($vendor, $staffId);
    }

    /**
     * Delete staff member
     */
    public function delete(VendorUser $vendor, int $staffId): bool
    {
        $staff = $this->find($vendor, $staffId);
        if (! $staff) {
            return false;
        }

        $staff->tokens()->delete();
        $staff->delete();

        return true;
    }

    /**
     * Reset staff password
     */
    public function resetPassword(VendorUser $vendor, int $staffId, string $newPassword): ?VendorUser
    {
        $staff = $this->find($vendor, $staffId);
        if (! $staff) {
            return null;
        }

        $staff->password_hash = Hash::make($newPassword);
        $staff->save();

        // Revoke all tokens
        $staff->tokens()->delete();

        return $this->find($vendor, $staffId);
    }
}
