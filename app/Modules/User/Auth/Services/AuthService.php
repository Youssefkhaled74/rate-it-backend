<?php

namespace App\Modules\User\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Support\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;
use App\Support\PhoneNormalizer;

class AuthService
{
    public function register(array $data): User
    {
        // Normalize phone before creating user so storage is consistent
        $phone = $data['phone'] ?? null;
        $phone = PhoneNormalizer::normalize($phone);

        $user = User::create([
            // Some apps store `name` column; ensure we set it to avoid SQL errors when 'name' is required
            'name' => $data['full_name'] ?? ($data['first_name'] ?? null),
            'email' => $data['email'] ?? null,
            'phone' => $phone,
            'birth_date' => $data['birth_date'] ?? null,
            'gender_id' => $data['gender_id'] ?? null,
            'nationality_id' => $data['nationality_id'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }

    public function login(string $phone, string $password): User
    {

        // Allow identifier to be phone or email by checking both columns.
        $identifier = $phone;

        // Normalize incoming identifier for phone lookups
        $normalizedIdentifier = PhoneNormalizer::normalize($identifier);

        $user = null;
        // If identifier contains @ assume email lookup first
        if (str_contains($identifier, '@')) {
            $user = User::where('email', $identifier)->first();
        }

        // Try phone lookup by normalized value
        if (!$user && $normalizedIdentifier) {
            $user = User::where('phone', $normalizedIdentifier)->first();
        }

        // Log helpful debug info before password check
        if ($user) {
            $hasPassword = !empty($user->password);
            $hashLength = is_string($user->password) ? strlen($user->password) : null;
            $checkResult = Hash::check($password, $user->password);
            Log::info('Auth login attempt', [
                'identifier' => $identifier,
                'db_phone' => $user->phone,
                'user_id' => $user->id,
                'has_password' => $hasPassword,
                'password_hash_length' => $hashLength,
                'hash_check' => $checkResult ? 'true' : 'false',
            ]);
            if (!$checkResult) {
                Log::error('Auth password mismatch', ['user_id' => $user->id]);
            }
        } else {
            Log::info('Auth login failed - user not found', ['identifier' => $identifier]);
        }

        $valid = $user && Hash::check($password, $user->password);

        // If hash check failed, attempt to detect legacy/plain hashes and rehash safely
        if ($user && !$valid) {
            $stored = $user->password;
            $rehashPerformed = false;

            // Plain text match (very unlikely but handle in legacy imports)
            if ($stored === $password) {
                $user->password = Hash::make($password);
                $user->save();
                $rehashPerformed = true;
            }

            // MD5 legacy
            if (!$rehashPerformed && md5($password) === $stored) {
                $user->password = Hash::make($password);
                $user->save();
                $rehashPerformed = true;
            }

            // SHA1 legacy
            if (!$rehashPerformed && sha1($password) === $stored) {
                $user->password = Hash::make($password);
                $user->save();
                $rehashPerformed = true;
            }

            if ($rehashPerformed) {
                Log::info('Auth migrated legacy password hash', ['user_id' => $user->id]);
                $valid = Hash::check($password, $user->password);
            }
        }

        if (!$valid) {
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
        $token = $user->createToken('user');
        return $token->plainTextToken;
    }

    public function me(User $user): User
    {
        return $user;
    }
}
