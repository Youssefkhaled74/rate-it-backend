<?php

namespace Tests\Feature\Admin\Vendors;

use App\Models\Admin;
use App\Models\Brand;
use App\Models\VendorUser;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VendorsTest extends TestCase
{
    use RefreshDatabase;

    protected string $adminToken = '';
    protected ?Admin $admin = null;
    protected ?Brand $brand = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test brand
        $this->brand = Brand::factory()->create([
            'name_en' => 'Test Brand',
            'name_ar' => 'علامة اختبار',
        ]);

        // Create admin user
        $this->admin = Admin::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password_hash' => bcrypt('secret'),
        ]);

        // Login and get token
        $response = $this->postJson('/api/v1/admin/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'secret',
        ]);

        $this->adminToken = $response->json('data.token');
    }

    /**
     * Helper: Get auth headers
     */
    protected function adminHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->adminToken}"];
    }

    /**
     * Test: List vendors
     */
    public function test_list_vendors()
    {
        // Create test vendors
        VendorUser::factory(3)->create([
            'brand_id' => $this->brand->id,
            'role' => 'VENDOR_ADMIN',
        ]);

        $response = $this->getJson('/api/v1/admin/vendors', $this->adminHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'brand_id', 'name', 'phone', 'email', 'role', 'is_active']
            ],
            'meta' => ['total', 'per_page', 'current_page']
        ]);
    }

    /**
     * Test: Get vendor details
     */
    public function test_get_vendor_details()
    {
        $vendor = VendorUser::factory()->create([
            'brand_id' => $this->brand->id,
            'role' => 'VENDOR_ADMIN',
        ]);

        $response = $this->getJson(
            "/api/v1/admin/vendors/{$vendor->id}",
            $this->adminHeaders()
        );

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $vendor->id);
        $response->assertJsonPath('data.phone', $vendor->phone);
    }

    /**
     * Test: Create vendor successfully
     */
    public function test_create_vendor_success()
    {
        $response = $this->postJson(
            '/api/v1/admin/vendors',
            [
                'brand_id' => $this->brand->id,
                'name' => 'Ahmed Al-Khaldi',
                'phone' => '+971501234567',
                'email' => 'ahmed@example.com',
                'password' => 'SecurePass123',
                'password_confirmation' => 'SecurePass123',
            ],
            $this->adminHeaders()
        );

        $response->assertStatus(201);
        $response->assertJsonPath('data.name', 'Ahmed Al-Khaldi');
        $response->assertJsonPath('data.phone', '+971501234567');
        $response->assertJsonPath('data.role', 'VENDOR_ADMIN');
        $response->assertJsonPath('data.is_active', true);

        // Verify in database
        $this->assertDatabaseHas('vendor_users', [
            'phone' => '+971501234567',
            'name' => 'Ahmed Al-Khaldi',
            'role' => 'VENDOR_ADMIN',
            'brand_id' => $this->brand->id,
        ]);
    }

    /**
     * Test: Create vendor with duplicate phone
     */
    public function test_create_vendor_duplicate_phone()
    {
        VendorUser::factory()->create(['phone' => '+971501234567']);

        $response = $this->postJson(
            '/api/v1/admin/vendors',
            [
                'brand_id' => $this->brand->id,
                'name' => 'Another Vendor',
                'phone' => '+971501234567',
                'email' => 'another@example.com',
                'password' => 'SecurePass123',
                'password_confirmation' => 'SecurePass123',
            ],
            $this->adminHeaders()
        );

        $response->assertStatus(422);
        $response->assertJsonPath('data.errors.phone.0', __('admin.vendors.phone_already_exists'));
    }

    /**
     * Test: Create vendor with invalid brand
     */
    public function test_create_vendor_invalid_brand()
    {
        $response = $this->postJson(
            '/api/v1/admin/vendors',
            [
                'brand_id' => 99999,  // Non-existent brand
                'name' => 'Ahmed Al-Khaldi',
                'phone' => '+971501234567',
                'email' => 'ahmed@example.com',
                'password' => 'SecurePass123',
                'password_confirmation' => 'SecurePass123',
            ],
            $this->adminHeaders()
        );

        $response->assertStatus(422);
        $response->assertJsonPath('data.errors.brand_id.0', __('admin.vendors.brand_id_invalid'));
    }

    /**
     * Test: Create vendor with invalid password confirmation
     */
    public function test_create_vendor_password_mismatch()
    {
        $response = $this->postJson(
            '/api/v1/admin/vendors',
            [
                'brand_id' => $this->brand->id,
                'name' => 'Ahmed Al-Khaldi',
                'phone' => '+971501234567',
                'email' => 'ahmed@example.com',
                'password' => 'SecurePass123',
                'password_confirmation' => 'DifferentPass456',  // Mismatch
            ],
            $this->adminHeaders()
        );

        $response->assertStatus(422);
        $response->assertJsonPath('data.errors.password.0', __('admin.vendors.password_confirmation_failed'));
    }

    /**
     * Test: Create vendor with invalid email
     */
    public function test_create_vendor_invalid_email()
    {
        $response = $this->postJson(
            '/api/v1/admin/vendors',
            [
                'brand_id' => $this->brand->id,
                'name' => 'Ahmed Al-Khaldi',
                'phone' => '+971501234567',
                'email' => 'not-an-email',  // Invalid
                'password' => 'SecurePass123',
                'password_confirmation' => 'SecurePass123',
            ],
            $this->adminHeaders()
        );

        $response->assertStatus(422);
        $response->assertJsonPath('data.errors.email.0', __('admin.vendors.email_invalid'));
    }

    /**
     * Test: Create vendor without email (optional)
     */
    public function test_create_vendor_without_email()
    {
        $response = $this->postJson(
            '/api/v1/admin/vendors',
            [
                'brand_id' => $this->brand->id,
                'name' => 'Ahmed Al-Khaldi',
                'phone' => '+971501234567',
                'password' => 'SecurePass123',
                'password_confirmation' => 'SecurePass123',
            ],
            $this->adminHeaders()
        );

        $response->assertStatus(201);
        $response->assertJsonPath('data.email', null);
    }

    /**
     * Test: Update vendor details
     */
    public function test_update_vendor()
    {
        $vendor = VendorUser::factory()->create([
            'brand_id' => $this->brand->id,
            'role' => 'VENDOR_ADMIN',
        ]);

        $response = $this->patchJson(
            "/api/v1/admin/vendors/{$vendor->id}",
            [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ],
            $this->adminHeaders()
        );

        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Updated Name');
        $response->assertJsonPath('data.email', 'updated@example.com');

        // Verify in database
        $this->assertDatabaseHas('vendor_users', [
            'id' => $vendor->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /**
     * Test: Update vendor password
     */
    public function test_update_vendor_password()
    {
        $vendor = VendorUser::factory()->create([
            'brand_id' => $this->brand->id,
            'role' => 'VENDOR_ADMIN',
        ]);

        $oldPassword = $vendor->password_hash;

        $response = $this->patchJson(
            "/api/v1/admin/vendors/{$vendor->id}",
            [
                'password' => 'NewPassword123',
                'password_confirmation' => 'NewPassword123',
            ],
            $this->adminHeaders()
        );

        $response->assertStatus(200);

        // Verify password changed
        $vendor->refresh();
        $this->assertNotEquals($oldPassword, $vendor->password_hash);
    }

    /**
     * Test: Delete vendor
     */
    public function test_delete_vendor()
    {
        $vendor = VendorUser::factory()->create([
            'brand_id' => $this->brand->id,
            'role' => 'VENDOR_ADMIN',
        ]);

        $response = $this->deleteJson(
            "/api/v1/admin/vendors/{$vendor->id}",
            [],
            $this->adminHeaders()
        );

        $response->assertStatus(200);

        // Verify soft delete
        $this->assertSoftDeleted('vendor_users', ['id' => $vendor->id]);
    }

    /**
     * Test: Restore deleted vendor
     */
    public function test_restore_vendor()
    {
        $vendor = VendorUser::factory()->create([
            'brand_id' => $this->brand->id,
            'role' => 'VENDOR_ADMIN',
        ]);

        // Delete first
        $vendor->delete();

        $response = $this->postJson(
            "/api/v1/admin/vendors/{$vendor->id}/restore",
            [],
            $this->adminHeaders()
        );

        $response->assertStatus(200);

        // Verify restored
        $this->assertDatabaseHas('vendor_users', [
            'id' => $vendor->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test: Filter vendors by brand
     */
    public function test_list_vendors_filter_by_brand()
    {
        $brand2 = Brand::factory()->create();

        VendorUser::factory(2)->create(['brand_id' => $this->brand->id, 'role' => 'VENDOR_ADMIN']);
        VendorUser::factory(3)->create(['brand_id' => $brand2->id, 'role' => 'VENDOR_ADMIN']);

        $response = $this->getJson(
            "/api/v1/admin/vendors?brand_id={$this->brand->id}",
            $this->adminHeaders()
        );

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('meta.total'));
    }

    /**
     * Test: Search vendors by name
     */
    public function test_list_vendors_search_by_name()
    {
        VendorUser::factory()->create([
            'brand_id' => $this->brand->id,
            'name' => 'Ahmed Hassan',
            'role' => 'VENDOR_ADMIN',
        ]);

        VendorUser::factory()->create([
            'brand_id' => $this->brand->id,
            'name' => 'Mohammed Ali',
            'role' => 'VENDOR_ADMIN',
        ]);

        $response = $this->getJson(
            '/api/v1/admin/vendors?search=Ahmed',
            $this->adminHeaders()
        );

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals('Ahmed Hassan', $response->json('data.0.name'));
    }

    /**
     * Test: Vendor not found
     */
    public function test_get_nonexistent_vendor()
    {
        $response = $this->getJson(
            '/api/v1/admin/vendors/99999',
            $this->adminHeaders()
        );

        $response->assertStatus(404);
    }

    /**
     * Test: Vendor can login after creation
     */
    public function test_created_vendor_can_login()
    {
        // Create vendor
        $this->postJson(
            '/api/v1/admin/vendors',
            [
                'brand_id' => $this->brand->id,
                'name' => 'Ahmed Al-Khaldi',
                'phone' => '+971501234567',
                'email' => 'ahmed@example.com',
                'password' => 'SecurePass123',
                'password_confirmation' => 'SecurePass123',
            ],
            $this->adminHeaders()
        );

        // Try to login as vendor
        $response = $this->postJson('/api/v1/vendor/auth/login', [
            'phone' => '+971501234567',
            'password' => 'SecurePass123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.vendor.role', 'VENDOR_ADMIN');
        $response->assertJsonPath('data.vendor.brand.id', $this->brand->id);
    }
}
