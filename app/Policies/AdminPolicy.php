<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    public function before(Admin $user, $ability)
    {
        // keep default behavior; permission checks already grant SUPER_ADMIN via hasPermission()
        return null;
    }

    public function viewAny(Admin $user)
    {
        return $this->hasPermission($user, 'admins.view');
    }

    public function view(Admin $user, Admin $model)
    {
        return $this->hasPermission($user, 'admins.view');
    }

    public function create(Admin $user)
    {
        return $this->hasPermission($user, 'admins.create');
    }

    public function update(Admin $user, Admin $model)
    {
        // allow updating others if permission
        return $this->hasPermission($user, 'admins.update');
    }

    public function delete(Admin $user, Admin $model)
    {
        // cannot delete yourself
        if ($user->id === $model->id) return false;
        return $this->hasPermission($user, 'admins.delete');
    }

    public function toggle(Admin $user, Admin $model)
    {
        if ($user->id === $model->id) return false;
        return $this->hasPermission($user, 'admins.toggle');
    }

    protected function hasPermission(Admin $user, string $perm): bool
    {
        // if permissions relation exists, check it; otherwise rely on role
        if (method_exists($user, 'permissions')) {
            return $user->permissions()->contains('name', $perm) || strtoupper($user->role ?? '') === 'SUPER_ADMIN';
        }

        return strtoupper($user->role ?? '') === 'SUPER_ADMIN';
    }
}
