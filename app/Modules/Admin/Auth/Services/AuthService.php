<?php

namespace App\Modules\Admin\Auth\Services;

use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        // create personal access token record
        $token = Str::random(64);
        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => Admin::class,
            'tokenable_id' => $admin->id,
            'name' => 'admin-token',
            'token' => $token,
            'abilities' => null,
            'last_used_at' => null,
            'expires_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ['admin' => $admin, 'token' => $token];
    }

    public function logout($admin, $token)
    {
        DB::table('personal_access_tokens')->where('token', $token)->where('tokenable_type', Admin::class)->delete();
    }
}
