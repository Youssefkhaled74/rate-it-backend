<?php

namespace Tests\Feature\Vendor\Web;

use App\Models\Brand;
use App\Models\Branch;
use App\Models\VendorUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorWebAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_branch_staff_cannot_access_admin_pages(): void
    {
        $brand = Brand::factory()->create();
        $branch = Branch::factory()->create(['brand_id' => $brand->id]);

        $staff = VendorUser::factory()->create([
            'brand_id' => null,
            'branch_id' => $branch->id,
            'role' => 'BRANCH_STAFF',
            'password_hash' => bcrypt('secret'),
        ]);

        $this->actingAs($staff, 'vendor_web');

        $this->get('/vendor/dashboard')->assertRedirect('/vendor');
        $this->get('/vendor/reviews')->assertRedirect('/vendor');
        $this->get('/vendor/branches/settings')->assertRedirect('/vendor');
        $this->get('/vendor/staff')->assertRedirect('/vendor');
    }

    public function test_vendor_admin_can_access_admin_pages(): void
    {
        $brand = Brand::factory()->create();
        $branch = Branch::factory()->create(['brand_id' => $brand->id]);

        $admin = VendorUser::factory()->create([
            'brand_id' => $brand->id,
            'branch_id' => null,
            'role' => 'VENDOR_ADMIN',
            'password_hash' => bcrypt('secret'),
        ]);

        $this->actingAs($admin, 'vendor_web');

        $this->get('/vendor/dashboard')->assertOk();
        $this->get('/vendor/reviews')->assertOk();
        $this->get('/vendor/branches/settings')->assertOk();
        $this->get('/vendor/staff')->assertOk();
    }
}

