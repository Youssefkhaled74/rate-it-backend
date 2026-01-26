<?php

namespace Modules\Admin\app\Services;

use Modules\Admin\app\Models\Admin;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    /**
     * Get admin profile.
     */
    public function getProfile(Admin $admin): Admin
    {
        return $admin;
    }

    /**
     * Update admin profile.
     */
    public function updateProfile(Admin $admin, array $data): Admin
    {
        $admin->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        return $admin->fresh();
    }

    /**
     * Update password with validation.
     */
    public function updatePassword(Admin $admin, string $currentPassword, string $newPassword): bool
    {
        // Verify current password
        if (!Hash::check($currentPassword, $admin->password)) {
            throw new \Exception(__('admin.current_password_incorrect'));
        }

        // Update password
        $admin->update(['password' => bcrypt($newPassword)]);

        return true;
    }

    /**
     * Get profile data for display.
     */
    public function getProfileData(Admin $admin): array
    {
        return [
            'id' => $admin->id,
            'name' => $admin->name,
            'email' => $admin->email,
            'phone' => $admin->phone,
            'status' => $admin->status,
            'is_super' => $admin->is_super,
            'last_login_at' => $admin->last_login_at,
            'created_at' => $admin->created_at,
        ];
    }
}
