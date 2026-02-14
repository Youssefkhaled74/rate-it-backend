<?php

namespace Tests\Feature\User\Auth;

use App\Models\User;
use Carbon\Carbon;
use Tests\Feature\User\Auth\Support\UserAuthTestCase;

class PhoneVerificationCycleTest extends UserAuthTestCase
{
    public function test_user_phone_verification_cycle_works_with_static_otp(): void
    {
        $this->createUser([
            'phone' => '+201555555555',
            'email' => 'phone-verify@test.local',
            'password' => 'Password123!',
            'phone_verified_at' => null,
        ]);

        $send = $this->postJson('/api/v1/user/auth/phone/send-otp', [
            'phone' => '+201555555555',
        ]);

        $send->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.otp_code', '1111');

        $verify = $this->postJson('/api/v1/user/auth/phone/verify-otp', [
            'phone' => '+201555555555',
            'otp' => '1111',
        ]);

        $verify->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertNotNull(
            User::where('phone', '+201555555555')->value('phone_verified_at')
        );
    }

    public function test_verify_phone_otp_fails_with_wrong_code(): void
    {
        $this->createUser([
            'phone' => '+201666666666',
            'email' => 'phone-verify-wrong@test.local',
            'password' => 'Password123!',
            'phone_verified_at' => null,
        ]);

        $this->postJson('/api/v1/user/auth/phone/send-otp', [
            'phone' => '+201666666666',
        ])->assertStatus(200);

        $verify = $this->postJson('/api/v1/user/auth/phone/verify-otp', [
            'phone' => '+201666666666',
            'otp' => '0000',
        ]);

        $verify->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', __('auth.invalid_otp'));
    }

    public function test_verify_phone_otp_is_idempotent_for_already_verified_user(): void
    {
        $user = $this->createUser([
            'phone' => '+201777777777',
            'email' => 'phone-verify-idempotent@test.local',
            'password' => 'Password123!',
        ]);
        $user->forceFill(['phone_verified_at' => Carbon::now()])->save();

        $response = $this->postJson('/api/v1/user/auth/phone/verify-otp', [
            'phone' => '+201777777777',
            'otp' => '1111',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', __('auth.phone_already_verified'));
    }
}
