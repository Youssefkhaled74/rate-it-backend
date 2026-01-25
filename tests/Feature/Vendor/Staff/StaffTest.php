<?php

namespace Tests\Feature\Vendor\Staff;

use Tests\Feature\Vendor\Support\VendorTestCase;
use App\Models\VendorUser;

class StaffTest extends VendorTestCase
{
    /**
     * Test list staff (admin can see all staff in brand)
     */
    public function test_list_staff_admin()
    {
        VendorUser::factory(3)->create([
            'brand_id' => null,
            'branch_id' => $this->branch->id,
            'role' => 'BRANCH_STAFF',
        ]);

        $response = $this->getJson('/api/v1/vendor/staff',
            $this->vendorAdminHeaders());

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'phone', 'role', 'is_active'],
            ],
            'meta' => ['page', 'limit', 'total'],
        ]);
    }

    /**
     * Test create staff (admin only)
     */
    public function test_create_staff_admin()
    {
        $response = $this->postJson('/api/v1/vendor/staff', [
            'name' => 'New Staff',
            'phone' => '0599999999',
            'email' => 'staff@example.com',
            'branch_id' => $this->branch->id,
        ], $this->vendorAdminHeaders());

        $this->assertSuccessJson($response);
        $response->assertJsonPath('data.role', 'BRANCH_STAFF');
        
        $this->assertDatabaseHas('vendor_users', [
            'name' => 'New Staff',
            'phone' => '0599999999',
            'branch_id' => $this->branch->id,
            'role' => 'BRANCH_STAFF',
        ]);
    }

    /**
     * Test update staff
     */
    public function test_update_staff()
    {
        $response = $this->patchJson("/api/v1/vendor/staff/{$this->vendorStaff->id}", [
            'name' => 'Updated Name',
            'is_active' => false,
        ], $this->vendorAdminHeaders());

        $this->assertSuccessJson($response);
        
        $this->assertDatabaseHas('vendor_users', [
            'id' => $this->vendorStaff->id,
            'name' => 'Updated Name',
            'is_active' => false,
        ]);
    }

    /**
     * Test reset staff password
     */
    public function test_reset_staff_password()
    {
        $response = $this->postJson("/api/v1/vendor/staff/{$this->vendorStaff->id}/reset-password",
            [], $this->vendorAdminHeaders());

        $this->assertSuccessJson($response);
        
        // Old password should not work
        $loginResponse = $this->postJson('/api/v1/vendor/auth/login', [
            'phone' => $this->vendorStaff->phone,
            'password' => 'secret',
        ]);

        $loginResponse->assertStatus(401);
    }

    /**
     * Test staff cannot create/update staff (forbidden)
     */
    public function test_staff_cannot_manage_staff()
    {
        $this->loginAsVendor($this->vendorStaff, 'secret');

        $response = $this->postJson('/api/v1/vendor/staff', [
            'name' => 'Hacker',
            'phone' => '0511111111',
            'branch_id' => $this->branch->id,
        ], $this->vendorStaffHeaders());

        $response->assertStatus(403);
    }
}
