<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_and_login_with_same_credentials()
    {
        // Register
        $registerData = [
            'full_name' => 'Test User',
            'phone' => '+201234567890',
            'email' => 'test@example.com',
            'birth_date' => '1990-01-01',
            'gender_id' => 1,
            'nationality_id' => 1,
            'password' => 'TestPassword123!',
            'password_confirmation' => 'TestPassword123!',
        ];

        $registerResponse = $this->postJson('/api/v1/user/auth/register', $registerData);
        $registerResponse->assertStatus(200);
        $registerResponse->assertJsonStructure([
            'success',
            'data' => ['token', 'user'],
        ]);
        $this->assertTrue($registerResponse->json('success'));

        // Login with same credentials
        $loginResponse = $this->postJson('/api/v1/user/auth/login', [
            'phone' => '+201234567890',
            'password' => 'TestPassword123!',
        ]);

        $loginResponse->assertStatus(200);
        $loginResponse->assertJsonStructure([
            'success',
            'data' => ['token', 'user'],
        ]);
        $this->assertTrue($loginResponse->json('success'));
        $this->assertNotEmpty($loginResponse->json('data.token'));

        // Verify password is properly hashed in DB
        $user = User::where('phone', '+201234567890')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('TestPassword123!', $user->password));
        $this->assertStringStartsWith('$2y$', $user->password); // bcrypt format
    }

    /** @test */
    public function login_fails_with_wrong_password()
    {
        // Create user directly
        $user = User::create([
            'name' => 'Test User',
            'phone' => '+201234567890',
            'email' => 'test@example.com',
            'password' => 'CorrectPassword123!', // Cast will hash this
        ]);

        // Attempt login with wrong password
        $response = $this->postJson('/api/v1/user/auth/login', [
            'phone' => '+201234567890',
            'password' => 'WrongPassword123!',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /** @test */
    public function password_is_properly_hashed_on_create()
    {
        $plainPassword = 'TestPassword123!';

        $user = User::create([
            'name' => 'Test User',
            'phone' => '+201234567890',
            'email' => 'test@example.com',
            'password' => $plainPassword, // Let cast handle hashing
        ]);

        // Verify hash was applied
        $this->assertNotEquals($plainPassword, $user->password);
        $this->assertStringStartsWith('$2y$', $user->password);
        $this->assertTrue(Hash::check($plainPassword, $user->password));
    }
}
