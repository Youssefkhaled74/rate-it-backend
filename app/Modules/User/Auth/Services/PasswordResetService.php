<?php

namespace App\Modules\User\Auth\Services;

use App\Modules\User\Auth\Repositories\PasswordResetRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Support\Exceptions\ApiException;

class PasswordResetService
{
    protected PasswordResetRepository $repo;

    public function __construct(PasswordResetRepository $repo)
    {
        $this->repo = $repo;
    }

    public function issueResetToken(string $phone): string
    {
        $token = Str::random(40);
        $hash = Hash::make($token);
        $expires = Carbon::now()->addMinutes(15);
        $this->repo->createToken($phone, $hash, $expires);
        return $token; // raw token to return to client
    }

    public function resetPassword(string $phone, string $resetToken, string $newPassword): void
    {
        $record = $this->repo->findByPhone($phone);
        if (!$record) {
            throw new ApiException('auth.invalid_otp', 422);
        }

        if ($record->expires_at && $record->expires_at->isPast()) {
            throw new ApiException('auth.otp_expired', 422);
        }

        if (!Hash::check($resetToken, $record->token_hash)) {
            throw new ApiException('auth.invalid_otp', 422);
        }

        $user = User::where('phone', $phone)->first();
        if (!$user) {
            throw new ApiException('auth.invalid_credentials', 404);
        }

        $user->password = $newPassword;
        $user->save();

        // revoke tokens
        $user->tokens()->delete();

        // delete reset token
        $this->repo->deleteToken($record);
    }
}
