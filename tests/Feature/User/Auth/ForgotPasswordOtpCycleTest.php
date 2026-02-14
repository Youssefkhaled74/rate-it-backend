<?php

namespace Tests\Feature\User\Auth;

use Tests\Feature\User\Auth\Support\UserAuthTestCase;

class ForgotPasswordOtpCycleTest extends UserAuthTestCase
{
    public function test_forgot_password_otp_verify_and_reset_cycle(): void
    {
        $this->createUser([
            'phone' => '+201333333333',
            'email' => 'forgot@test.local',
            'password' => 'OldPassword123!',
        ]);

        $sendOtp = $this->postJson('/api/v1/user/auth/forgot-password/send-otp', [
            'phone' => '+201333333333',
        ]);

        $sendOtp->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.otp_code', '1111');

        $verify = $this->postJson('/api/v1/user/auth/forgot-password/verify-otp', [
            'phone' => '+201333333333',
            'otp' => '1111',
        ]);

        $verify->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['reset_token'],
                'meta',
            ]);

        $resetToken = (string) $verify->json('data.reset_token');
        $this->assertNotSame('', $resetToken);

        $reset = $this->postJson('/api/v1/user/auth/forgot-password/reset', [
            'phone' => '+201333333333',
            'reset_token' => $resetToken,
            'new_password' => 'NewPassword123!',
            'new_password_confirmation' => 'NewPassword123!',
        ]);

        $reset->assertStatus(200)->assertJsonPath('success', true);

        $loginOld = $this->postJson('/api/v1/user/auth/login', [
            'phone' => '+201333333333',
            'password' => 'OldPassword123!',
        ]);
        $loginOld->assertStatus(401)->assertJsonPath('success', false);

        $loginNew = $this->postJson('/api/v1/user/auth/login', [
            'phone' => '+201333333333',
            'password' => 'NewPassword123!',
        ]);
        $loginNew->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_otp_verification_is_rate_limited_after_five_failed_attempts(): void
    {
        $this->createUser([
            'phone' => '+201444444444',
            'email' => 'otp-limit@test.local',
            'password' => 'Password123!',
        ]);

        $this->postJson('/api/v1/user/auth/forgot-password/send-otp', [
            'phone' => '+201444444444',
        ])->assertStatus(200);

        for ($i = 1; $i <= 5; $i++) {
            $attempt = $this->postJson('/api/v1/user/auth/forgot-password/verify-otp', [
                'phone' => '+201444444444',
                'otp' => '9999',
            ]);

            $attempt->assertStatus(422)->assertJsonPath('success', false);
        }

        $blocked = $this->postJson('/api/v1/user/auth/forgot-password/verify-otp', [
            'phone' => '+201444444444',
            'otp' => '9999',
        ]);

        $blocked->assertStatus(429)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', __('auth.too_many_attempts'));
    }
}

