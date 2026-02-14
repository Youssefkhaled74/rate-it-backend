<?php

namespace Tests\Feature\User\Auth;

use App\Models\User;
use Tests\Feature\User\Auth\Support\UserAuthTestCase;

class AuthCycleTest extends UserAuthTestCase
{
    public function test_user_can_register_login_get_me_and_logout_cycle(): void
    {
        $lookups = $this->createLookups();

        $register = $this->postJson('/api/v1/user/auth/register', [
            'name' => 'Cycle User',
            'phone' => '+201111111111',
            'email' => 'cycle@test.local',
            'birth_date' => '1995-01-01',
            'gender_id' => $lookups['gender']->id,
            'nationality_id' => $lookups['nationality']->id,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $register->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'phone', 'email'],
                ],
                'meta',
            ]);

        $login = $this->postJson('/api/v1/user/auth/login', [
            'phone' => '+201111111111',
            'password' => 'Password123!',
        ]);

        $login->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'phone', 'email'],
                ],
                'meta',
            ]);

        $token = (string) $login->json('data.token');
        $this->assertNotSame('', $token);
        $userId = (int) $login->json('data.user.id');

        User::whereKey($userId)->update([
            'city_id' => $lookups['city']->id,
            'area_id' => $lookups['area']->id,
        ]);

        $me = $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/v1/user/auth/me');

        $me->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'phone',
                    'email',
                    'gender',
                    'nationality',
                    'notifications_count',
                    'notifications',
                    'reviews_count',
                    'reviews',
                    'is_phone_verified',
                ],
                'meta',
            ]);
        $this->assertNotNull($me->json('data.city'));
        $this->assertNotNull($me->json('data.area'));

        $logout = $this->withHeaders($this->authHeaders($token))
            ->postJson('/api/v1/user/auth/logout');

        $logout->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $this->createUser([
            'phone' => '+201222222222',
            'email' => 'invalid-login@test.local',
            'password' => 'CorrectPassword123!',
        ]);

        $response = $this->postJson('/api/v1/user/auth/login', [
            'phone' => '+201222222222',
            'password' => 'WrongPassword123!',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', __('auth.invalid_credentials'));
    }
}
