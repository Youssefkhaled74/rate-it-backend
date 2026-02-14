<?php

namespace App\Modules\User\Auth\Services;

use App\Modules\User\Auth\Repositories\OtpCodeRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Support\Exceptions\ApiException;

class OtpService
{
    private const STATIC_OTP = '1111';

    protected OtpCodeRepository $repo;

    public function __construct(OtpCodeRepository $repo)
    {
        $this->repo = $repo;
    }

    public function sendOtp(string $phone): string
    {
        // rate limit should be implemented via throttle middleware; basic guard here
        $code = self::STATIC_OTP;
        $hash = Hash::make($code);
        $expires = Carbon::now()->addMinutes(5);

        $this->repo->createOrUpdateForPhone($phone, $hash, $expires);

        // In production, hook into SMS provider. For dev, log it.
        Log::info("OTP for {$phone}: {$code}");

        return $code; // note: only for dev/testing; do not return in production
    }

    public function verifyOtp(string $phone, string $otp): bool
    {
        $record = $this->repo->findActive($phone);
        if (!$record) {
            throw new ApiException('auth.otp_expired', 422);
        }

        if ($record->attempts >= 5) {
            throw new ApiException('auth.too_many_attempts', 429);
        }

        if (!Hash::check($otp, $record->code_hash)) {
            $this->repo->incrementAttempts($record);
            throw new ApiException('auth.invalid_otp', 422);
        }

        $this->repo->markVerified($record);
        return true;
    }
}
