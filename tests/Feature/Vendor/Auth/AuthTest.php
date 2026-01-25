<?php

namespace Tests\Feature\Vendor\Auth;

use Tests\Feature\Vendor\Support\VendorTestCase;
use App\Models\VendorUser;

class AuthTest extends VendorTestCase
{
    /**
     * Test vendor login
     */
    public function test_vendor_login()
    {
        $response = $this->postJson('/api/v1/vendor/auth/login', [
            'phone' => $this->vendorAdmin->phone,
            'password' => 'secret',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['token', 'vendor'],
        ]);
    }

    /**
     * Test vendor login with wrong password
     */
    public function test_vendor_login_wrong_password()
    {
        $response = $this->postJson('/api/v1/vendor/auth/login', [
            'phone' => $this->vendorAdmin->phone,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test get current vendor (me)
     */
    public function test_get_current_vendor()
    {
        $response = $this->getJson('/api/v1/vendor/auth/me',
            $this->vendorAdminHeaders());

        $this->assertSuccessJson($response);
        $response->assertJson([
            'data' => [
                'id' => $this->vendorAdmin->id,
                'role' => 'VENDOR_ADMIN',
            ],
        ]);
    }

    /**
     * Test vendor logout
     */
    public function test_vendor_logout()
    {
        $response = $this->postJson('/api/v1/vendor/auth/logout',
            [], $this->vendorAdminHeaders());

        $this->assertSuccessJson($response);

        // Token should be invalid after logout
        $response = $this->getJson('/api/v1/vendor/auth/me',
            $this->vendorAdminHeaders());

        $response->assertStatus(401);
    }
}
