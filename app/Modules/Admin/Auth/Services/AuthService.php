<?php

namespace App\Modules\Admin\Auth\Services;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthService
{
    public function login(string $email, string $password)
    {
        $admin = Admin::where('email', $email)->where('is_active', true)->first();
        if (! $admin) {
            return null;
        }

        if (! $admin->verifyPassword($password)) {
            return null;
        }

        // Create token using the admin guard
        $token = $admin->createToken('admin-token')->plainTextToken;

        return ['admin' => $admin, 'token' => $token];
    }

    public function logout($admin, $token)
    {
        $admin->tokens()->where('token', hash('sha256', $token))->delete();
    }
}
