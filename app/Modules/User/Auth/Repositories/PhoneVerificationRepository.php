<?php

namespace App\Modules\User\Auth\Repositories;

use App\Modules\User\Auth\Models\PhoneVerificationToken;
use Carbon\Carbon;

class PhoneVerificationRepository
{
    public function createToken(string $phone, string $otpHash, Carbon $expiresAt)
    {
        // remove existing active tokens
        try {
            PhoneVerificationToken::where('phone', $phone)->delete();
        } catch (\Exception $e) {
        }

        return PhoneVerificationToken::create([
            'phone' => $phone,
            'otp_hash' => $otpHash,
            'expires_at' => $expiresAt,
        ]);
    }

    public function findActiveToken(string $phone)
    {
        return PhoneVerificationToken::where('phone', $phone)
            ->whereNull('consumed_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
            })->latest()->first();
    }

    public function incrementAttempt(PhoneVerificationToken $token)
    {
        $token->attempt_count = $token->attempt_count + 1;
        $token->save();
    }

    public function markConsumed(PhoneVerificationToken $token)
    {
        $token->consumed_at = Carbon::now();
        $token->save();
    }
}
