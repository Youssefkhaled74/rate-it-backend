<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;

class UserPolicy
{
    /**
     * Global before hook to allow SUPER_ADMIN admins to bypass policies.
     */
    public function before($user, $ability)
    {
        if ($user instanceof Admin && strtoupper($user->role ?? '') === 'SUPER_ADMIN') {
            return true;
        }

        return null;
    }

    public function viewAny($user)
    {
        return false;
    }

    public function view($user, User $model)
    {
        return false;
    }
}
