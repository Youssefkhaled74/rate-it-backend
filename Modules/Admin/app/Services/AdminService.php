<?php

namespace Modules\Admin\app\Services;

use Modules\Admin\app\Models\Admin;
use Illuminate\Pagination\Paginator;

class AdminService
{
    /**
     * Get paginated list of admins.
     */
    public function getPaginatedAdmins(
        ?string $search = null,
        ?string $status = null,
        int $perPage = 15
    ): Paginator {
        $query = Admin::query();

        // Search by name or email
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status && in_array($status, ['active', 'inactive'])) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Create a new admin.
     */
    public function createAdmin(array $data): Admin
    {
        $data['password'] = bcrypt($data['password']);

        return Admin::create($data);
    }

    /**
     * Update an admin.
     */
    public function updateAdmin(Admin $admin, array $data): Admin
    {
        // Only update password if provided
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $admin->update($data);

        return $admin->fresh();
    }

    /**
     * Deactivate an admin.
     */
    public function deactivateAdmin(Admin $admin): Admin
    {
        // Prevent deactivating self
        if (auth('admin')->id() === $admin->id) {
            throw new \Exception(__('admin.cannot_deactivate_self'));
        }

        $admin->deactivate();

        return $admin;
    }

    /**
     * Activate an admin.
     */
    public function activateAdmin(Admin $admin): Admin
    {
        $admin->activate();

        return $admin;
    }

    /**
     * Delete an admin (only if not super admin and not self).
     */
    public function deleteAdmin(Admin $admin): bool
    {
        if ($admin->is_super || auth('admin')->id() === $admin->id) {
            throw new \Exception(__('admin.cannot_delete_admin'));
        }

        return $admin->delete();
    }

    /**
     * Get admin statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total' => Admin::count(),
            'active' => Admin::active()->count(),
            'inactive' => Admin::inactive()->count(),
            'super_admins' => Admin::superAdmins()->count(),
        ];
    }
}
