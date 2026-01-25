<?php

namespace App\Modules\Admin\Vendors\Services;

use App\Models\VendorUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class VendorAdminService
{
    /**
     * List all vendors with optional filtering
     */
    public function list(array $filters): LengthAwarePaginator
    {
        $query = VendorUser::where('role', 'VENDOR_ADMIN');

        // Filter by brand
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', (int)$filters['brand_id']);
        }

        // Search by name, phone, or email
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by active status
        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool)$filters['is_active']);
        }

        // With relationships
        $query->with('brand');

        // Pagination
        $perPage = $filters['per_page'] ?? 20;
        return $query->paginate($perPage);
    }

    /**
     * Find single vendor
     */
    public function find(int $vendorId): ?VendorUser
    {
        return VendorUser::where('role', 'VENDOR_ADMIN')->with('brand')->find($vendorId);
    }

    /**
     * Create new vendor admin account
     */
    public function create(array $data): VendorUser
    {
        return VendorUser::create([
            'brand_id' => (int)$data['brand_id'],
            'branch_id' => null,  // Admins don't have branch assignment
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'password_hash' => Hash::make($data['password']),
            'role' => 'VENDOR_ADMIN',
            'is_active' => true,
        ]);
    }

    /**
     * Update vendor details
     */
    public function update(VendorUser $vendor, array $data): VendorUser
    {
        $updates = [];

        if (isset($data['name'])) {
            $updates['name'] = $data['name'];
        }

        if (isset($data['email'])) {
            $updates['email'] = $data['email'];
        }

        if (isset($data['is_active'])) {
            $updates['is_active'] = (bool)$data['is_active'];
        }

        if (isset($data['password'])) {
            $updates['password_hash'] = Hash::make($data['password']);
        }

        if (isset($data['photo'])) {
            // Delete old photo if exists
            if ($vendor->photo && Storage::exists($vendor->photo)) {
                Storage::delete($vendor->photo);
            }

            // Store new photo
            if ($data['photo']) {
                $path = $data['photo']->store('vendors', 'public');
                $updates['photo'] = $path;
            }
        }

        if (!empty($updates)) {
            $vendor->update($updates);
        }

        return $vendor;
    }

    /**
     * Delete vendor (soft delete)
     */
    public function delete(VendorUser $vendor): bool
    {
        return $vendor->delete();
    }

    /**
     * Restore deleted vendor
     */
    public function restore(VendorUser $vendor): bool
    {
        return $vendor->restore();
    }
}
