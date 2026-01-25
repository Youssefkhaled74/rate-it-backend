<?php

namespace Tests\Feature\Vendor\Support;

use App\Models\VendorUser;
use App\Models\Brand;
use App\Models\Place;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class VendorTestCase extends TestCase
{
    use RefreshDatabase;

    protected string $vendorAdminToken = '';
    protected string $vendorStaffToken = '';
    protected ?VendorUser $vendorAdmin = null;
    protected ?VendorUser $vendorStaff = null;
    protected ?Brand $brand = null;
    protected ?Place $place = null;
    protected ?Branch $branch = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test brand, place, branches
        $this->brand = Brand::factory()->create();
        $this->place = Place::factory()->create(['brand_id' => $this->brand->id]);
        $this->branch = Branch::factory()->create(['place_id' => $this->place->id]);

        // Create vendor admin
        $this->vendorAdmin = VendorUser::factory()
            ->create([
                'brand_id' => $this->brand->id,
                'branch_id' => null,
                'role' => 'VENDOR_ADMIN',
                'password_hash' => bcrypt('secret'),
            ]);

        // Create vendor staff
        $this->vendorStaff = VendorUser::factory()
            ->create([
                'brand_id' => null,
                'branch_id' => $this->branch->id,
                'role' => 'BRANCH_STAFF',
                'password_hash' => bcrypt('secret'),
            ]);

        // Login as admin
        $this->loginAsVendor($this->vendorAdmin, 'secret');
    }

    /**
     * Login as vendor and store the token
     */
    protected function loginAsVendor(VendorUser $vendor, string $password): void
    {
        $response = $this->postJson('/api/v1/vendor/auth/login', [
            'phone' => $vendor->phone,
            'password' => $password,
        ]);

        if ($vendor->role === 'VENDOR_ADMIN') {
            $this->vendorAdminToken = $response->json('data.token');
        } else {
            $this->vendorStaffToken = $response->json('data.token');
        }
    }

    /**
     * Get authorization headers for admin
     */
    protected function vendorAdminHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->vendorAdminToken}",
            'Accept' => 'application/json',
        ];
    }

    /**
     * Get authorization headers for staff
     */
    protected function vendorStaffHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->vendorStaffToken}",
            'Accept' => 'application/json',
        ];
    }

    /**
     * Assert successful JSON response (success=true)
     */
    protected function assertSuccessJson($response)
    {
        $response->assertJson(['success' => true]);
    }
}
