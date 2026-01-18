<?php

namespace App\Modules\User\Auth\Repositories;

use App\Modules\User\Auth\Models\PasswordResetToken;
use Carbon\Carbon;

class PasswordResetRepository
{
    public function createToken(string $phone, string $tokenHash, Carbon $expiresAt)
    {
        // Remove any existing tokens for the phone to keep a single active token
        try {
            PasswordResetToken::where('phone', $phone)->delete();
        } catch (\Exception $e) {
            // ignore deletion errors (best-effort)
        }

        return PasswordResetToken::create([
            'phone' => $phone,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
        ]);
    }

    public function findByPhone(string $phone)
    {
        return PasswordResetToken::where('phone', $phone)->latest()->first();
    }

    public function deleteToken(PasswordResetToken $token): void
    {
        // Some legacy schemas may not have an `id` primary key column.
        // Deleting the model directly will fail if `id` column is missing (SQL: where `id` is null).
        // Prefer deleting by identifying attributes as a safe fallback.
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('password_reset_tokens', 'id')) {
                $token->delete();
                return;
            }
        } catch (\Exception $e) {
            // ignore schema check errors and fall back to attribute-based deletion
        }

        try {
            $query = PasswordResetToken::query();
            if (! empty($token->phone)) {
                $query->where('phone', $token->phone);
            }
            if (! empty($token->token_hash)) {
                $query->where('token_hash', $token->token_hash);
            }
            $query->delete();
        } catch (\Exception $e) {
            // last resort: attempt to delete by phone only
            try {
                if (! empty($token->phone)) {
                    PasswordResetToken::where('phone', $token->phone)->delete();
                }
            } catch (\Exception $e2) {
                // give up silently - non-fatal
            }
        }
    }
}
