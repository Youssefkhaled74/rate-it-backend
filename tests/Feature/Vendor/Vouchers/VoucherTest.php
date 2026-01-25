<?php

namespace Tests\Feature\Vendor\Vouchers;

use Tests\Feature\Vendor\Support\VendorTestCase;
use App\Models\Voucher;
use Carbon\Carbon;

class VoucherTest extends VendorTestCase
{
    /**
     * Test check voucher with valid code
     */
    public function test_check_voucher_valid()
    {
        $voucher = Voucher::factory()->create([
            'brand_id' => $this->brand->id,
            'status' => 'VALID',
            'code' => 'ABC123',
        ]);

        $response = $this->postJson('/api/v1/vendor/vouchers/check', [
            'code_or_link' => 'ABC123',
        ], $this->vendorAdminHeaders());

        $this->assertSuccessJson($response);
        $response->assertJson([
            'data' => [
                'code' => 'ABC123',
                'status' => 'VALID',
            ],
        ]);
    }

    /**
     * Test check voucher with prefixed code format
     */
    public function test_check_voucher_prefixed_format()
    {
        $voucher = Voucher::factory()->create([
            'brand_id' => $this->brand->id,
            'status' => 'VALID',
            'code' => 'ABC123',
        ]);

        $response = $this->postJson('/api/v1/vendor/vouchers/check', [
            'code_or_link' => 'VOUCHER-ABC123',
        ], $this->vendorAdminHeaders());

        $this->assertSuccessJson($response);
        $response->assertJson(['data' => ['code' => 'ABC123']]);
    }

    /**
     * Test check voucher with URL format
     */
    public function test_check_voucher_url_format()
    {
        $voucher = Voucher::factory()->create([
            'brand_id' => $this->brand->id,
            'status' => 'VALID',
            'code' => 'ABC123',
        ]);

        $response = $this->postJson('/api/v1/vendor/vouchers/check', [
            'code_or_link' => 'https://example.com?code=ABC123',
        ], $this->vendorAdminHeaders());

        $this->assertSuccessJson($response);
        $response->assertJson(['data' => ['code' => 'ABC123']]);
    }

    /**
     * Test check voucher - invalid code (not found)
     */
    public function test_check_voucher_not_found()
    {
        $response = $this->postJson('/api/v1/vendor/vouchers/check', [
            'code_or_link' => 'INVALID-CODE',
        ], $this->vendorAdminHeaders());

        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    }

    /**
     * Test check voucher - wrong brand scope (security)
     */
    public function test_check_voucher_wrong_brand_scope()
    {
        $otherBrand = Brand::factory()->create();
        $otherPlace = Place::factory()->create(['brand_id' => $otherBrand->id]);
        
        $otherVoucher = Voucher::factory()->create([
            'brand_id' => $otherBrand->id,
            'status' => 'VALID',
            'code' => 'OTHER123',
        ]);

        $response = $this->postJson('/api/v1/vendor/vouchers/check', [
            'code_or_link' => 'OTHER123',
        ], $this->vendorAdminHeaders());

        // Should not find vouchers from other brands
        $response->assertStatus(404);
    }

    /**
     * Test redeem voucher successful (admin with branch_id)
     */
    public function test_redeem_voucher_admin_success()
    {
        $voucher = Voucher::factory()->create([
            'brand_id' => $this->brand->id,
            'status' => 'VALID',
            'code' => 'REDEEM1',
        ]);

        $response = $this->postJson('/api/v1/vendor/vouchers/redeem', [
            'code_or_link' => 'REDEEM1',
            'branch_id' => $this->branch->id,
        ], $this->vendorAdminHeaders());

        $this->assertSuccessJson($response);
        $response->assertJson([
            'data' => [
                'code' => 'REDEEM1',
                'status' => 'USED',
            ],
        ]);
        $response->assertJsonPath('data.used_at', null); // Not in structure for USED
        
        // Verify in database
        $this->assertDatabaseHas('vouchers', [
            'code' => 'REDEEM1',
            'status' => 'USED',
            'used_branch_id' => $this->branch->id,
            'verified_by_vendor_user_id' => $this->vendorAdmin->id,
        ]);
    }

    /**
     * Test redeem voucher - staff forced to their branch
     */
    public function test_redeem_voucher_staff_forced_branch()
    {
        $this->loginAsVendor($this->vendorStaff, 'secret');
        
        $voucher = Voucher::factory()->create([
            'brand_id' => $this->brand->id,
            'status' => 'VALID',
            'code' => 'STAFFTEST',
        ]);

        // Staff tries to redeem at wrong branch
        $otherBranch = Branch::factory()->create(['place_id' => $this->place->id]);
        
        $response = $this->postJson('/api/v1/vendor/vouchers/redeem', [
            'code_or_link' => 'STAFFTEST',
            'branch_id' => $otherBranch->id,
        ], $this->vendorStaffHeaders());

        // Should redeem at their assigned branch, not the requested one
        $this->assertDatabaseHas('vouchers', [
            'code' => 'STAFFTEST',
            'status' => 'USED',
            'used_branch_id' => $this->vendorStaff->branch_id, // Forced to their branch
        ]);
    }

    /**
     * Test redeem voucher - already redeemed (double redeem prevention)
     */
    public function test_redeem_voucher_already_redeemed()
    {
        $voucher = Voucher::factory()->create([
            'brand_id' => $this->brand->id,
            'status' => 'USED',
            'code' => 'USED1',
            'used_at' => now(),
            'used_branch_id' => $this->branch->id,
            'verified_by_vendor_user_id' => $this->vendorAdmin->id,
        ]);

        $response = $this->postJson('/api/v1/vendor/vouchers/redeem', [
            'code_or_link' => 'USED1',
            'branch_id' => $this->branch->id,
        ], $this->vendorAdminHeaders());

        $response->assertStatus(422);
        $response->assertJsonPath('message', __('vendor.vouchers.already_redeemed'));
    }

    /**
     * Test redeem voucher - expired (past expiry_at)
     */
    public function test_redeem_voucher_expired()
    {
        $voucher = Voucher::factory()->create([
            'brand_id' => $this->brand->id,
            'status' => 'VALID',
            'code' => 'EXPIRED1',
            'expires_at' => Carbon::now()->subDays(1),
        ]);

        $response = $this->postJson('/api/v1/vendor/vouchers/redeem', [
            'code_or_link' => 'EXPIRED1',
            'branch_id' => $this->branch->id,
        ], $this->vendorAdminHeaders());

        $response->assertStatus(422);
        $response->assertJsonPath('message', __('vendor.vouchers.voucher_expired'));
    }

    /**
     * Test redeem voucher - admin missing branch_id
     */
    public function test_redeem_voucher_admin_missing_branch_id()
    {
        $voucher = Voucher::factory()->create([
            'brand_id' => $this->brand->id,
            'status' => 'VALID',
            'code' => 'NOBRANCH',
        ]);

        $response = $this->postJson('/api/v1/vendor/vouchers/redeem', [
            'code_or_link' => 'NOBRANCH',
            'branch_id' => null,
        ], $this->vendorAdminHeaders());

        $response->assertStatus(422);
    }

    /**
     * Test redeem voucher - concurrency (row locking)
     * Simulates double-redeem attempt during transaction
     */
    public function test_redeem_voucher_concurrency_safety()
    {
        $voucher = Voucher::factory()->create([
            'brand_id' => $this->brand->id,
            'status' => 'VALID',
            'code' => 'CONCURRENT1',
        ]);

        // First redemption succeeds
        $response1 = $this->postJson('/api/v1/vendor/vouchers/redeem', [
            'code_or_link' => 'CONCURRENT1',
            'branch_id' => $this->branch->id,
        ], $this->vendorAdminHeaders());

        $this->assertSuccessJson($response1);

        // Second redemption (simulating concurrent request after first completes)
        $response2 = $this->postJson('/api/v1/vendor/vouchers/redeem', [
            'code_or_link' => 'CONCURRENT1',
            'branch_id' => $this->branch->id,
        ], $this->vendorAdminHeaders());

        // Should fail with already_redeemed
        $response2->assertStatus(422);
        $response2->assertJsonPath('message', __('vendor.vouchers.already_redeemed'));
    }

    /**
     * Test voucher redemption history list
     */
    public function test_list_redemptions()
    {
        // Create redeemed vouchers
        Voucher::factory(3)->create([
            'brand_id' => $this->brand->id,
            'status' => 'USED',
            'used_branch_id' => $this->branch->id,
            'verified_by_vendor_user_id' => $this->vendorAdmin->id,
            'used_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/vendor/vouchers/redemptions', 
            $this->vendorAdminHeaders());

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'code', 'status', 'used_at', 'used_branch', 'verified_by'],
            ],
            'meta' => ['page', 'limit', 'total', 'has_next', 'last_page'],
        ]);
    }

    /**
     * Test redemption history filtering by branch (staff sees only their branch)
     */
    public function test_redemptions_staff_branch_scope()
    {
        $this->loginAsVendor($this->vendorStaff, 'secret');

        // Create vouchers redeemed at different branches
        $otherBranch = Branch::factory()->create(['place_id' => $this->place->id]);
        
        Voucher::factory()->create([
            'brand_id' => $this->brand->id,
            'status' => 'USED',
            'used_branch_id' => $this->branch->id, // Staff's branch
            'used_at' => now(),
        ]);

        Voucher::factory()->create([
            'brand_id' => $this->brand->id,
            'status' => 'USED',
            'used_branch_id' => $otherBranch->id, // Other branch
            'used_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/vendor/vouchers/redemptions',
            $this->vendorStaffHeaders());

        // Staff should only see their branch's redemptions
        $this->assertSuccessJson($response);
        $data = $response->json('data');
        $this->assertEquals(1, count($data));
        $this->assertEquals($this->branch->id, $data[0]['used_branch']['id']);
    }

    /**
     * Test dashboard (admin only access)
     */
    public function test_dashboard_admin_access()
    {
        $response = $this->getJson('/api/v1/vendor/dashboard/summary',
            $this->vendorAdminHeaders());

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'total_branches',
                'reviews_count',
                'average_rating_brand',
                'top_branches_by_rating',
                'vouchers_used',
            ],
        ]);
    }

    /**
     * Test dashboard - staff forbidden
     */
    public function test_dashboard_staff_forbidden()
    {
        $this->loginAsVendor($this->vendorStaff, 'secret');

        $response = $this->getJson('/api/v1/vendor/dashboard/summary',
            $this->vendorStaffHeaders());

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
    }
}
