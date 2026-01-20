<?php

namespace Tests\Feature\Admin\Auth;

use Tests\Feature\Admin\Support\AdminTestCase;
use App\Models\Admin;

class AuthTest extends AdminTestCase
{
    /**
     * Test admin login with valid credentials
     */
    public function test_admin_login_with_valid_credentials()
    {
        $response = $this->postAsGuest('/api/v1/admin/auth/login', [
            'email' => 'admin@test.local',
            'password' => 'password',
        ]);

        $this->assertCreatedJson($response);
        $response->assertJsonStructure([
            'data' => [
                'admin' => [
                    'id',
                    'name',
                    'email',
                ],
                'token',
            ],
        ]);
        $this->assertNotNull($response->json('data.token'));
    }

    /**
     * Test admin login fails with invalid email
     */
    public function test_admin_login_fails_with_invalid_email()
    {
        $response = $this->postAsGuest('/api/v1/admin/auth/login', [
            'email' => 'nonexistent@test.local',
            'password' => 'password',
        ]);

        $this->assertUnauthorizedJson($response);
    }

    /**
     * Test admin login fails with wrong password
     */
    public function test_admin_login_fails_with_wrong_password()
    {
        $response = $this->postAsGuest('/api/v1/admin/auth/login', [
            'email' => 'admin@test.local',
            'password' => 'wrong_password',
        ]);

        $this->assertUnauthorizedJson($response);
    }

    /**
     * Test admin login fails when admin is inactive
     */
    public function test_admin_login_fails_when_admin_inactive()
    {
        $admin = Admin::where('email', 'admin@test.local')->first();
        $admin->update(['is_active' => false]);

        $response = $this->postAsGuest('/api/v1/admin/auth/login', [
            'email' => 'admin@test.local',
            'password' => 'password',
        ]);

        $this->assertUnauthorizedJson($response);
    }

    /**
     * Test me endpoint returns authenticated admin details
     */
    public function test_get_authenticated_admin_details()
    {
        $response = $this->getAsAdmin('/api/v1/admin/auth/me');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'is_active',
            ],
        ]);
        $response->assertJsonPath('data.email', 'admin@test.local');
    }

    /**
     * Test me endpoint fails without authentication
     */
    public function test_get_auth_details_fails_without_token()
    {
        $response = $this->getAsGuest('/api/v1/admin/auth/me');

        $this->assertUnauthorizedJson($response);
    }

    /**
     * Test me endpoint fails with invalid token
     */
    public function test_get_auth_details_fails_with_invalid_token()
    {
        $response = $this->getAsAdmin('/api/v1/admin/auth/me', 'invalid_token_xyz');

        $this->assertUnauthorizedJson($response);
    }

    /**
     * Test admin logout invalidates token
     */
    public function test_admin_logout_invalidates_token()
    {
        // Ensure we have a valid token
        $this->assertNotEmpty($this->adminToken);

        // Logout
        $response = $this->postAsAdmin('/api/v1/admin/auth/logout');
        $this->assertSuccessJson($response);

        // Try to use the token after logout - should fail
        $response = $this->getAsAdmin('/api/v1/admin/auth/me', $this->adminToken);
        $this->assertUnauthorizedJson($response);
    }

    /**
     * Test logout without authentication fails
     */
    public function test_logout_without_authentication_fails()
    {
        $response = $this->postAsGuest('/api/v1/admin/auth/logout', []);

        $this->assertUnauthorizedJson($response);
    }
}
