<?php

namespace Tests\Feature\Vendor\Web;

use App\Models\Brand;
use App\Models\Branch;
use App\Models\VendorUser;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherRedeemIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_redeeming_used_voucher_is_blocked(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $brand = Brand::factory()->create();
        $branch = Branch::factory()->create(['brand_id' => $brand->id]);

        $staff = VendorUser::factory()->create([
            'brand_id' => null,
            'branch_id' => $branch->id,
            'role' => 'BRANCH_STAFF',
            'password_hash' => bcrypt('secret'),
        ]);

        $voucher = Voucher::factory()->create([
            'brand_id' => $brand->id,
            'status' => 'VALID',
        ]);

        $this->actingAs($staff, 'vendor_web');

        $this->post('/vendor/vouchers/redeem', [
            'code_or_link' => $voucher->code,
        ])->assertRedirect('/vendor/vouchers/verify');

        $this->post('/vendor/vouchers/redeem', [
            'code_or_link' => $voucher->code,
        ])->assertSessionHasErrors(['code_or_link']);
    }
}

