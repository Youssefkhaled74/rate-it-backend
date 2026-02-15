<?php

namespace Tests\Feature\Vendor\Branches;

use App\Models\Branch;
use App\Models\Brand;
use Tests\Feature\Vendor\Support\VendorTestCase;

class BranchesTest extends VendorTestCase
{
    public function test_list_branches_admin(): void
    {
        Branch::factory()->create([
            'brand_id' => $this->brand->id,
        ]);

        $response = $this->getJson('/api/v1/vendor/branches', $this->vendorAdminHeaders());

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'name_en', 'name_ar', 'review_cooldown_days'],
            ],
        ]);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_list_branches_staff_sees_only_assigned(): void
    {
        Branch::factory()->create([
            'brand_id' => $this->brand->id,
        ]);

        $this->loginAsVendor($this->vendorStaff, 'secret');
        $response = $this->getJson('/api/v1/vendor/branches', $this->vendorStaffHeaders());

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($this->branch->id, $response->json('data.0.id'));
    }

    public function test_update_branch_cooldown_admin(): void
    {
        $response = $this->patchJson(
            "/api/v1/vendor/branches/{$this->branch->id}/cooldown",
            ['review_cooldown_days' => 30],
            $this->vendorAdminHeaders()
        );

        $response->assertOk();
        $response->assertJsonPath('data.review_cooldown_days', 30);
        $this->assertEquals(30, $this->branch->fresh()->review_cooldown_days);
    }

    public function test_update_branch_cooldown_staff_forbidden(): void
    {
        $this->loginAsVendor($this->vendorStaff, 'secret');

        $response = $this->patchJson(
            "/api/v1/vendor/branches/{$this->branch->id}/cooldown",
            ['review_cooldown_days' => 10],
            $this->vendorStaffHeaders()
        );

        $response->assertStatus(403);
    }

    public function test_update_branch_wrong_brand_forbidden(): void
    {
        $otherBrand = Brand::factory()->create();
        $otherBranch = Branch::factory()->create([
            'brand_id' => $otherBrand->id,
        ]);

        $response = $this->patchJson(
            "/api/v1/vendor/branches/{$otherBranch->id}/cooldown",
            ['review_cooldown_days' => 7],
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(403);
    }
}
