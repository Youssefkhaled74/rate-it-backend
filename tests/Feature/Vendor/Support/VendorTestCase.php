<?php

namespace Tests\Feature\Vendor\Support;

use App\Models\VendorUser;
use App\Models\Brand;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
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
    protected ?Branch $branch = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test brand and branch
        $this->brand = Brand::factory()->create();
        $this->branch = Branch::factory()->create([
            'brand_id' => $this->brand->id,
        ]);

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
                'brand_id' => $this->brand->id,
                'branch_id' => $this->branch->id,
                'role' => 'BRANCH_STAFF',
                'password_hash' => bcrypt('secret'),
            ]);

        $this->seedVendorRbacForTests();

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

    protected function seedVendorRbacForTests(): void
    {
        $permissions = ['vendor.reviews.list', 'vendor.staff.manage'];

        foreach ($permissions as $permissionName) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permissionName],
                [
                    'guard' => 'vendor',
                    'description' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'VENDOR_ADMIN_ROLE_TEST',
            'guard' => 'vendor',
            'description' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $staffRoleId = DB::table('roles')->insertGetId([
            'name' => 'BRANCH_STAFF_ROLE_TEST',
            'guard' => 'vendor',
            'description' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissions)
            ->pluck('id')
            ->all();

        foreach ($permissionIds as $permissionId) {
            DB::table('role_has_permissions')->insert([
                'role_id' => $adminRoleId,
                'permission_id' => $permissionId,
            ]);
        }

        DB::table('role_has_permissions')->insert([
            'role_id' => $staffRoleId,
            'permission_id' => DB::table('permissions')->where('name', 'vendor.reviews.list')->value('id'),
        ]);

        DB::table('model_has_roles')->insert([
            'role_id' => $adminRoleId,
            'model_type' => VendorUser::class,
            'model_id' => $this->vendorAdmin->id,
        ]);

        DB::table('model_has_roles')->insert([
            'role_id' => $staffRoleId,
            'model_type' => VendorUser::class,
            'model_id' => $this->vendorStaff->id,
        ]);
    }
}
