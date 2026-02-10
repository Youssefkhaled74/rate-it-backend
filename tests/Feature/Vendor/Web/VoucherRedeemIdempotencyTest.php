<?php

namespace Tests\Feature\Vendor\Web;

use App\Models\Brand;
use App\Models\Place;
use App\Models\Branch;
use App\Models\Voucher;
use App\Models\VendorUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherRedeemIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_redeeming_used_voucher_is_blocked()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $brand = Brand::factory()->create();
        $place = Place::factory()->create(['brand_id' => $brand->id]);
        $branch = Branch::factory()->create(['place_id' => $place->id]);

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
