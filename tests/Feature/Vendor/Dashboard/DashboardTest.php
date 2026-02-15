<?php

namespace Tests\Feature\Vendor\Dashboard;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Review;
use App\Models\Voucher;
use Carbon\Carbon;
use Tests\Feature\Vendor\Support\VendorTestCase;

class DashboardTest extends VendorTestCase
{
    public function test_dashboard_admin_access_and_contract(): void
    {
        $response = $this->getJson('/api/v1/vendor/dashboard/summary', $this->vendorAdminHeaders());

        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'total_branches',
                'reviews_count' => ['last_7_days', 'last_30_days'],
                'average_rating_brand',
                'top_branches_by_rating' => [
                    '*' => ['id', 'name', 'place_id', 'place_name', 'reviews_count', 'average_rating'],
                ],
                'vouchers_used' => ['last_7_days', 'last_30_days'],
            ],
        ]);
    }

    public function test_dashboard_staff_forbidden(): void
    {
        $this->loginAsVendor($this->vendorStaff, 'secret');

        $response = $this->getJson('/api/v1/vendor/dashboard/summary', $this->vendorStaffHeaders());

        $response->assertStatus(403);
    }

    public function test_dashboard_requires_auth(): void
    {
        $response = $this->getJson('/api/v1/vendor/dashboard/summary');
        $response->assertStatus(401);
    }

    public function test_dashboard_kpis_are_calculated_correctly(): void
    {
        $secondBranch = Branch::factory()->create([
            'brand_id' => $this->brand->id,
            'name' => 'Second Branch',
            'review_cooldown_days' => 0,
        ]);

        // 7d: 3 reviews on branch 1
        Review::factory(3)->create([
            'branch_id' => $this->branch->id,
            'overall_rating' => 5,
            'created_at' => Carbon::now()->subDays(2),
        ]);

        // 30d only: 2 reviews on branch 2
        Review::factory(2)->create([
            'branch_id' => $secondBranch->id,
            'overall_rating' => 3,
            'created_at' => Carbon::now()->subDays(15),
        ]);

        // vouchers in last 7d = 2
        Voucher::factory(2)->create([
            'brand_id' => $this->brand->id,
            'status' => 'USED',
            'used_at' => Carbon::now()->subDays(3),
        ]);

        // vouchers in 30d only = +3
        Voucher::factory(3)->create([
            'brand_id' => $this->brand->id,
            'status' => 'USED',
            'used_at' => Carbon::now()->subDays(10),
        ]);

        // outside 30d
        Voucher::factory()->create([
            'brand_id' => $this->brand->id,
            'status' => 'USED',
            'used_at' => Carbon::now()->subDays(40),
        ]);

        $response = $this->getJson('/api/v1/vendor/dashboard/summary', $this->vendorAdminHeaders());
        $response->assertStatus(200);

        $response->assertJsonPath('data.total_branches', 2);
        $response->assertJsonPath('data.reviews_count.last_7_days', 3);
        $response->assertJsonPath('data.reviews_count.last_30_days', 5);
        $response->assertJsonPath('data.vouchers_used.last_7_days', 2);
        $response->assertJsonPath('data.vouchers_used.last_30_days', 5);

        // average rating across 5 reviews: (3*5 + 2*3)/5 = 4.2
        $this->assertSame(4.2, (float) $response->json('data.average_rating_brand'));
    }

    public function test_dashboard_top_branches_include_reviews_count_and_sorting(): void
    {
        $high = Branch::factory()->create([
            'brand_id' => $this->brand->id,
            'name' => 'High Branch',
            'review_cooldown_days' => 0,
        ]);
        $low = Branch::factory()->create([
            'brand_id' => $this->brand->id,
            'name' => 'Low Branch',
            'review_cooldown_days' => 0,
        ]);

        Review::factory(2)->create([
            'branch_id' => $high->id,
            'overall_rating' => 5,
        ]);
        Review::factory(2)->create([
            'branch_id' => $low->id,
            'overall_rating' => 2,
        ]);

        $response = $this->getJson('/api/v1/vendor/dashboard/summary', $this->vendorAdminHeaders());
        $response->assertStatus(200);

        $top = $response->json('data.top_branches_by_rating');
        $this->assertNotEmpty($top);
        $this->assertSame($high->id, $top[0]['id']);
        $this->assertSame(2, (int) $top[0]['reviews_count']);
        $this->assertGreaterThan($top[1]['average_rating'], $top[0]['average_rating']);
    }

    public function test_dashboard_brand_scoping_regression(): void
    {
        $otherBrand = Brand::factory()->create();
        $otherBranch = Branch::factory()->create([
            'brand_id' => $otherBrand->id,
            'name' => 'Other Brand Branch',
            'review_cooldown_days' => 0,
        ]);

        Review::factory(7)->create([
            'branch_id' => $otherBranch->id,
            'overall_rating' => 1,
            'created_at' => Carbon::now()->subDays(2),
        ]);
        Voucher::factory(4)->create([
            'brand_id' => $otherBrand->id,
            'status' => 'USED',
            'used_at' => Carbon::now()->subDays(2),
        ]);

        $response = $this->getJson('/api/v1/vendor/dashboard/summary', $this->vendorAdminHeaders());
        $response->assertStatus(200);

        $response->assertJsonPath('data.reviews_count.last_7_days', 0);
        $response->assertJsonPath('data.vouchers_used.last_7_days', 0);
    }
}
