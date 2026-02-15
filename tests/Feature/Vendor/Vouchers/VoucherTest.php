<?php

namespace Tests\Feature\Vendor\Vouchers;

use App\Models\Brand;
use App\Models\Branch;
use App\Models\Voucher;
use Carbon\Carbon;
use Tests\Feature\Vendor\Support\VendorTestCase;

class VoucherTest extends VendorTestCase
{
    public function test_check_voucher_valid(): void
    {
        Voucher::factory()->create([
            'brand_id' => $this->brand->id,
            'status' => 'VALID',
            'code' => 'ABC123',
        ]);

        $response = $this->postJson('/api/v1/vendor/vouchers/check', [
            'code_or_link' => 'ABC123',
        ], $this->vendorAdminHeaders());

        $this->assertSuccessJson($response);
        $response->assertJsonPath('data.code', 'ABC123');
        $response->assertJsonPath('data.status', 'VALID');
    }

    public function test_check_voucher_wrong_brand_scope(): void
    {
        $otherBrand = Brand::factory()->create();
        Voucher::factory()->create([
            'brand_id' => $otherBrand->id,
            'status' => 'VALID',
            'code' => 'OTHER123',
        ]);

        $response = $this->postJson('/api/v1/vendor/vouchers/check', [
            'code_or_link' => 'OTHER123',
        ], $this->vendorAdminHeaders());

        $response->assertStatus(403);
    }

    public function test_redeem_voucher_admin_success(): void
    {
        Voucher::factory()->create([
            'brand_id' => $this->brand->id,
            'status' => 'VALID',
            'code' => 'REDEEM1',
        ]);

        $response = $this->postJson('/api/v1/vendor/vouchers/redeem', [
            'code_or_link' => 'REDEEM1',
            'branch_id' => $this->branch->id,
        ], $this->vendorAdminHeaders());

        $this->assertSuccessJson($response);
        $response->assertJsonPath('data.code', 'REDEEM1');
        $response->assertJsonPath('data.status', 'USED');
        $response->assertJsonPath('data.used_branch.id', $this->branch->id);
        $this->assertNotNull($response->json('data.used_at'));

        $this->assertDatabaseHas('vouchers', [
            'code' => 'REDEEM1',
            'status' => 'USED',
            'used_branch_id' => $this->branch->id,
            'verified_by_vendor_user_id' => $this->vendorAdmin->id,
        ]);
    }

    public function test_redeem_voucher_staff_forced_branch(): void
    {
        $this->loginAsVendor($this->vendorStaff, 'secret');

        Voucher::factory()->create([
            'brand_id' => $this->brand->id,
            'status' => 'VALID',
            'code' => 'STAFFTEST',
        ]);

        $otherBranch = Branch::factory()->create(['brand_id' => $this->brand->id]);

        $this->postJson('/api/v1/vendor/vouchers/redeem', [
            'code_or_link' => 'STAFFTEST',
            'branch_id' => $otherBranch->id,
        ], $this->vendorStaffHeaders())->assertOk();

        $this->assertDatabaseHas('vouchers', [
            'code' => 'STAFFTEST',
            'status' => 'USED',
            'used_branch_id' => $this->vendorStaff->branch_id,
        ]);
    }

    public function test_redeem_voucher_expired(): void
    {
        Voucher::factory()->create([
            'brand_id' => $this->brand->id,
            'status' => 'VALID',
            'code' => 'EXPIRED1',
            'expires_at' => Carbon::now()->subDay(),
        ]);

        $this->postJson('/api/v1/vendor/vouchers/redeem', [
            'code_or_link' => 'EXPIRED1',
            'branch_id' => $this->branch->id,
        ], $this->vendorAdminHeaders())->assertStatus(422);
    }

    public function test_list_redemptions(): void
    {
        Voucher::factory(3)->create([
            'brand_id' => $this->brand->id,
            'status' => 'USED',
            'used_branch_id' => $this->branch->id,
            'verified_by_vendor_user_id' => $this->vendorAdmin->id,
            'used_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/vendor/vouchers/redemptions', $this->vendorAdminHeaders());

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'code', 'status', 'used_at', 'used_branch', 'verified_by'],
            ],
            'meta' => ['page', 'limit', 'total', 'has_next', 'last_page'],
        ]);
    }
}
