<?php

namespace App\Modules\User\Auth\Repositories;

use App\Modules\User\Auth\Models\PasswordResetToken;
use Carbon\Carbon;

class PasswordResetRepository
{
    public function createToken(string $phone, string $tokenHash, Carbon $expiresAt)
    {
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
        $token->delete();
    }
}
