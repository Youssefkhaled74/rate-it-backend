<?php

namespace App\Modules\User\Profile\Services;

use App\Models\User;
use App\Models\PhoneChangeRequest;
use App\Support\PhoneNormalizer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProfileService
{
    public function getProfile(User $user): User
    {
        return $user->fresh();
    }

    public function updateProfile(User $user, array $data): User
    {
        if (isset($data['full_name'])) $user->name = $data['full_name'];
        if (isset($data['email'])) $user->email = $data['email'];

        if (isset($data['avatar_file'])) {
            $path = $data['avatar_file']->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();
        return $user->fresh();
    }

    public function sendPhoneChangeOtp(User $user, string $newPhone): array
    {
        $phone = PhoneNormalizer::normalize($newPhone);
        if (! $phone) {
            throw new \Exception('invalid phone');
        }

        // Ensure not used by another user
        $exists = User::where('phone', $phone)->where('id', '!=', $user->id)->exists();
        if ($exists) {
            throw new \Exception('profile.phone_taken');
        }

        $otp = str_pad(rand(0,9999),4,'0',STR_PAD_LEFT);
        $hash = Hash::make($otp);
        $expires = Carbon::now()->addMinutes(10);

        // create request
        $req = PhoneChangeRequest::create([
            'user_id' => $user->id,
            'new_phone' => $phone,
            'otp_hash' => $hash,
            'expires_at' => $expires,
            'attempts' => 0,
        ]);

        // Log OTP for dev (in production this should go to SMS provider)
        Log::info('profile.phone_change_otp', ['user_id'=>$user->id,'new_phone'=>$phone,'otp'=>$otp,'request_id'=>$req->id]);

        return ['expires_in_seconds' => $expires->diffInSeconds(Carbon::now())];
    }

    public function verifyPhoneChangeOtp(User $user, string $phone, string $otp): User
    {
        $normalized = PhoneNormalizer::normalize($phone);
        $req = PhoneChangeRequest::where('user_id', $user->id)
            ->where('new_phone', $normalized)
            ->whereNull('verified_at')
            ->orderBy('created_at','desc')
            ->first();

        if (! $req) {
            throw new \Exception('otp.invalid');
        }

        if ($req->isExpired()) {
            throw new \Exception('otp.expired');
        }

        if ($req->attempts >= 5) {
            throw new \Exception('otp.too_many_attempts');
        }

        $ok = Hash::check($otp, $req->otp_hash);
        $req->attempts = $req->attempts + 1;
        $req->save();

        if (! $ok) {
            throw new \Exception('otp.invalid');
        }

        // success: update user phone
        $user->phone = $normalized;
        $user->phone_verified_at = Carbon::now();
        $user->save();

        $req->verified_at = Carbon::now();
        $req->save();

        // invalidate other pending requests
        PhoneChangeRequest::where('user_id', $user->id)
            ->where('id', '!=', $req->id)
            ->whereNull('verified_at')
            ->update(['verified_at' => Carbon::now()]);

        Log::info('profile.phone_changed', ['user_id'=>$user->id,'new_phone'=>$normalized,'request_id'=>$req->id]);

        return $user->fresh();
    }
}
