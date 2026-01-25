<?php

namespace Tests\Feature\Vendor\Branches;

use App\Models\Branch;
use Tests\Feature\Vendor\Support\VendorTestCase;

class BranchesTest extends VendorTestCase
{
    /**
     * Test: Admin lists all branches in brand
     */
    public function test_list_branches_admin()
    {
        // Create additional branches
        $branch2 = $this->place->branches()->create([
            'name_en' => 'Branch 2',
            'name_ar' => 'فرع 2',
            'address_en' => 'Address 2',
            'address_ar' => 'العنوان 2',
        ]);

        $response = $this->getJson(
            '/api/v1/vendor/branches',
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name_en',
                    'name_ar',
                    'address_en',
                    'address_ar',
                    'review_cooldown_minutes',
                    'created_at',
                ]
            ]
        ]);

        $this->assertEquals(2, count($response->json('data')));
    }

    /**
     * Test: Staff sees only their assigned branch
     */
    public function test_list_branches_staff_sees_only_assigned()
    {
        // Create additional branches
        $branch2 = $this->place->branches()->create([
            'name_en' => 'Branch 2',
            'name_ar' => 'فرع 2',
            'address_en' => 'Address 2',
            'address_ar' => 'العنوان 2',
        ]);

        $response = $this->getJson(
            '/api/v1/vendor/branches',
            $this->vendorStaffHeaders()
        );

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
        $this->assertEquals($this->branch->id, $response->json('data.0.id'));
    }

    /**
     * Test: Update branch cooldown (admin only)
     */
    public function test_update_branch_cooldown_admin()
    {
        $response = $this->patchJson(
            "/api/v1/vendor/branches/{$this->branch->id}/cooldown",
            ['review_cooldown_minutes' => 30],
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        $response->assertJsonPath('data.review_cooldown_minutes', 30);
        
        // Verify in database
        $this->assertEquals(
            30,
            $this->branch->fresh()->review_cooldown_minutes
        );
    }

    /**
     * Test: Staff cannot update cooldown (forbidden)
     */
    public function test_update_branch_cooldown_staff_forbidden()
    {
        $response = $this->patchJson(
            "/api/v1/vendor/branches/{$this->branch->id}/cooldown",
            ['review_cooldown_minutes' => 30],
            $this->vendorStaffHeaders()
        );

        $response->assertStatus(403);
    }

    /**
     * Test: Cannot update branch from another brand
     */
    public function test_update_branch_wrong_brand_forbidden()
    {
        $otherBrand = \App\Models\Brand::factory()->create();
        $otherPlace = \App\Models\Place::factory()->create(['brand_id' => $otherBrand->id]);
        $otherBranch = Branch::factory()->create(['place_id' => $otherPlace->id]);

        $response = $this->patchJson(
            "/api/v1/vendor/branches/{$otherBranch->id}/cooldown",
            ['review_cooldown_minutes' => 30],
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(404);
    }

    /**
     * Test: Cooldown validation (min/max)
     */
    public function test_update_branch_cooldown_validation()
    {
        // Test too low
        $response = $this->patchJson(
            "/api/v1/vendor/branches/{$this->branch->id}/cooldown",
            ['review_cooldown_minutes' => 0],
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(422);

        // Test too high
        $response = $this->patchJson(
            "/api/v1/vendor/branches/{$this->branch->id}/cooldown",
            ['review_cooldown_minutes' => 100000],
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(422);
    }

    /**
     * Test: Update with invalid branch ID
     */
    public function test_update_branch_not_found()
    {
        $response = $this->patchJson(
            '/api/v1/vendor/branches/99999/cooldown',
            ['review_cooldown_minutes' => 30],
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(404);
    }

    /**
     * Test: List includes all required fields
     */
    public function test_branches_list_structure()
    {
        $response = $this->getJson(
            '/api/v1/vendor/branches',
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        
        $branch = $response->json('data.0');
        $this->assertNotNull($branch['id']);
        $this->assertNotNull($branch['name_en']);
        $this->assertNotNull($branch['name_ar']);
        $this->assertNotNull($branch['address_en']);
        $this->assertNotNull($branch['address_ar']);
        $this->assertIsInt($branch['review_cooldown_minutes']);
    }
}
