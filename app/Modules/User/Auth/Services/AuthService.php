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
            // Some apps store `name` column; ensure we set it to avoid SQL errors when 'name' is required
            'name' => $data['full_name'] ?? ($data['first_name'] ?? null),
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'gender_id' => $data['gender_id'] ?? null,
            'nationality_id' => $data['nationality_id'] ?? null,
            'password' => Hash::make($data['password']),
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
        /**
         * Revoke current access token issued to the user.
         *
         * @param \App\Models\User $user
         * @return void
         */
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
