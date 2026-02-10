<?php

namespace App\Modules\User\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Support\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;
use App\Support\PhoneNormalizer;
use App\Models\Subscription;
use Carbon\Carbon;

class AuthService
{
    public function register(array $data): User
    {
        // Normalize phone before creating user so storage is consistent
        $phone = $data['phone'] ?? null;
        $phone = PhoneNormalizer::normalize($phone);

        $user = User::create([
            // Some apps store `name` column; ensure we set it to avoid SQL errors when 'name' is required
            'name' => $data['name'] ?? ($data['full_name'] ?? ($data['first_name'] ?? null)),
            'email' => $data['email'] ?? null,
            'phone' => $phone,
            'birth_date' => $data['birth_date'] ?? null,
            'gender_id' => $data['gender_id'] ?? null,
            'nationality_id' => $data['nationality_id'] ?? null,
            'password' => $data['password'], // Let 'hashed' cast handle hashing
        ]);

        // Create default free subscription (6 months) for new users
        $now = Carbon::now();
        Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => null,
            'status' => 'FREE',
            'subscription_status' => 'trialing',
            'started_at' => $now,
            'free_until' => $now->copy()->addMonths(6),
            'paid_until' => null,
            'auto_renew' => false,
            'provider' => null,
            'provider_subscription_id' => null,
            'provider_transaction_id' => null,
            'meta' => null,
        ]);

        // If user was invited, attempt to complete the invite and award points.
        if (!empty($data['invited_by_phone'])) {
            try {
                app(\App\Modules\User\Invites\Services\InviteService::class)
                    ->completeInviteForNewUser($user, $data['invited_by_phone']);
            } catch (\Exception $e) {
                // log and continue - registering user should not fail due to invite processing
                \Illuminate\Support\Facades\Log::debug('invite.process_error', ['error'=>$e->getMessage(),'user_id'=>$user->id]);
            }
        }

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

        $valid = $user && Hash::check($password, $user->password);

        // If hash check failed, attempt to detect legacy/plain hashes and rehash safely
        if ($user && !$valid) {
            $stored = $user->password;
            $rehashPerformed = false;

            // Plain text match (very unlikely but handle in legacy imports)
            if ($stored === $password) {
                $user->password = $password; // Let 'hashed' cast handle hashing
                $user->save();
                $rehashPerformed = true;
            }

            // MD5 legacy
            if (!$rehashPerformed && md5($password) === $stored) {
                $user->password = $password; // Let 'hashed' cast handle hashing
                $user->save();
                $rehashPerformed = true;
            }

            // SHA1 legacy
            if (!$rehashPerformed && sha1($password) === $stored) {
                $user->password = $password; // Let 'hashed' cast handle hashing
                $user->save();
                $rehashPerformed = true;
            }

            if ($rehashPerformed) {
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
