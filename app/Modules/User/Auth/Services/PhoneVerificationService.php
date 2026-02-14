<?php

namespace App\Modules\User\Auth\Services;

use App\Modules\User\Auth\Repositories\PhoneVerificationRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Support\Exceptions\ApiException;

class PhoneVerificationService
{
    private const STATIC_OTP = '1111';

    protected PhoneVerificationRepository $repo;

    public function __construct(PhoneVerificationRepository $repo)
    {
        $this->repo = $repo;
    }

    public function sendOtp(string $phone): string
    {
        $otp = self::STATIC_OTP;
        $hash = Hash::make($otp);
        $expires = Carbon::now()->addMinutes(5);
        $this->repo->createToken($phone, $hash, $expires);
        return $otp; // return raw for dev/logging; remove in production
    }

    public function verifyOtp(string $phone, string $otp): void
    {
        $token = $this->repo->findActiveToken($phone);
        if (! $token) {
            throw new ApiException('auth.invalid_otp', 422);
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            throw new ApiException('auth.otp_expired', 422);
        }

        if (! Hash::check($otp, $token->otp_hash)) {
            $this->repo->incrementAttempt($token);
            throw new ApiException('auth.invalid_otp', 422);
        }

        // mark consumed
        $this->repo->markConsumed($token);

        // mark user verified
        $user = User::where('phone', $phone)->first();
        if (! $user) {
            throw new ApiException('auth.user_not_found', 404);
        }

        if (! $user->phone_verified_at) {
            $user->phone_verified_at = Carbon::now();
            $user->save();
        }
    }
}
