<?php

namespace App\Modules\User\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Support\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['full_name'] ?? ($data['first_name'] ?? null),
            'email' => $data['email'] ?? null,
            'password' => $data['password'],
        ]);

        return $user;
    }

    public function login(string $phone, string $password): User
    {
        $user = User::where('phone', $phone)->first();
        if (!$user || !Hash::check($password, $user->password)) {
            throw new ApiException('auth.invalid_credentials', 401);
        }

        return $user;
    }

    public function logout(User $user): void
    {
        // revoke current token
        $user->currentAccessToken()?->delete();
    }

    public function createTokenForUser(User $user): string
    {
        $token = $user->createToken('api-token');
        return $token->plainTextToken;
    }

    public function me(User $user): User
    {
        return $user;
    }
}
