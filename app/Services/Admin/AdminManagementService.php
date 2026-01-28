<?php

namespace App\Services\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminManagementService
{
    public function paginateAdmins(array $filters = [], int $perPage = 15)
    {
        // include photo_path so views can use the `photo_url` accessor
        $query = Admin::query()->select(['id','name','email','phone','role','is_active','created_at','photo_path']);

        if (! empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function($s) use ($q) {
                $s->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') $query->where('is_active', 1);
            if ($filters['status'] === 'inactive') $query->where('is_active', 0);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createAdmin(array $data): Admin
    {
        return DB::transaction(function() use ($data) {
            $admin = new Admin();
            $admin->name = $data['name'];
            $admin->email = $data['email'];
            $admin->phone = $data['phone'] ?? null;
            $admin->role = $data['role'] ?? null;
            $admin->is_active = isset($data['is_active']) ? (bool) $data['is_active'] : true;
            $admin->password_hash = Hash::make($data['password']);
            $admin->save();
            return $admin;
        });
    }

    public function updateAdmin(Admin $admin, array $data): Admin
    {
        return DB::transaction(function() use ($admin, $data) {
            $admin->name = $data['name'] ?? $admin->name;
            $admin->email = $data['email'] ?? $admin->email;
            if (array_key_exists('phone', $data)) $admin->phone = $data['phone'];
            if (isset($data['role'])) $admin->role = $data['role'];
            if (isset($data['is_active'])) $admin->is_active = (bool) $data['is_active'];
            if (! empty($data['password'])) {
                $admin->password_hash = Hash::make($data['password']);
            }
            $admin->save();
            return $admin;
        });
    }

    public function toggleAdmin(Admin $admin, Admin $by): Admin
    {
        if ($admin->id === $by->id) {
            throw new \RuntimeException('Cannot toggle own account');
        }
        $admin->is_active = ! (bool) $admin->is_active;
        $admin->save();
        return $admin;
    }

    public function deleteAdmin(Admin $admin, Admin $by): bool
    {
        if ($admin->id === $by->id) {
            throw new \RuntimeException('Cannot delete own account');
        }
        return (bool) $admin->delete();
    }
}
