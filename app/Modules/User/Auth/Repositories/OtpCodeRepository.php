<?php

namespace App\Modules\User\Auth\Repositories;

use App\Modules\User\Auth\Models\OtpCode;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class OtpCodeRepository
{
    public function createOrUpdateForPhone(string $phone, string $codeHash, Carbon $expiresAt): OtpCode
    {
        return OtpCode::updateOrCreate(
            ['phone' => $phone, 'purpose' => 'PASSWORD_RESET'],
            ['code_hash' => $codeHash, 'expires_at' => $expiresAt, 'last_sent_at' => now(), 'attempts' => 0, 'verified_at' => null]
        );
    }

    public function findActive(string $phone): ?OtpCode
    {
        return OtpCode::where('phone', $phone)
            ->where('purpose','PASSWORD_RESET')
            ->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at','>', now()); })
            ->first();
    }

    public function incrementAttempts(OtpCode $otp): void
    {
        $otp->increment('attempts');
    }

    public function markVerified(OtpCode $otp): void
    {
        $otp->verified_at = now();
        $otp->save();
    }
}
