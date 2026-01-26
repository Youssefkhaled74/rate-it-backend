<?php

namespace Modules\Admin\app\Policies;

use Modules\Admin\app\Models\Admin;

class AdminPolicy
{
    /**
     * Determine whether the user can view any model.
     */
    public function viewAny(Admin $user): bool
    {
        return $user->is_super || $user->hasPermission('admins.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Admin $user, Admin $model): bool
    {
        return $user->is_super || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Admin $user): bool
    {
        return $user->is_super || $user->hasPermission('admins.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Admin $user, Admin $model): bool
    {
        return $user->is_super || ($user->hasPermission('admins.update') && $user->id !== $model->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Admin $user, Admin $model): bool
    {
        return $user->is_super && $user->id !== $model->id && !$model->is_super;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Admin $user, Admin $model): bool
    {
        return $user->is_super;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Admin $user, Admin $model): bool
    {
        return $user->is_super && $user->id !== $model->id && !$model->is_super;
    }

    /**
     * Determine whether the user can deactivate the model.
     */
    public function deactivate(Admin $user, Admin $model): bool
    {
        return $user->is_super && $user->id !== $model->id;
    }
}
