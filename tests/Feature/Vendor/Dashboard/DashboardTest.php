<?php

namespace Tests\Feature\Vendor\Dashboard;

use Tests\Feature\Vendor\Support\VendorTestCase;
use App\Models\Review;
use App\Models\Voucher;
use Carbon\Carbon;

class DashboardTest extends VendorTestCase
{
    /**
     * Test: Admin can access dashboard
     */
    public function test_dashboard_admin_access()
    {
        // Create some data to display
        Review::factory(5)->create(['branch_id' => $this->branch->id]);
        Voucher::factory(3)->create([
            'brand_id' => $this->brand->id,
            'status' => 'USED',
            'used_at' => Carbon::now()->subDays(2),
        ]);

        $response = $this->getJson(
            '/api/v1/vendor/dashboard/summary',
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'total_branches',
                'total_reviews_7d',
                'total_reviews_30d',
                'average_rating',
                'top_branches' => [
                    '*' => ['id', 'name_en', 'rating'],
                ],
                'vouchers_used_7d',
                'vouchers_used_30d',
            ]
        ]);
    }

    /**
     * Test: Dashboard returns correct KPI values
     */
    public function test_dashboard_kpi_values()
    {
        // Create exactly known data
        Review::factory(3)->create([
            'branch_id' => $this->branch->id,
            'rating' => 5,
            'created_at' => Carbon::now()->subDays(1),  // Within 7d
        ]);
        Review::factory(2)->create([
            'branch_id' => $this->branch->id,
            'rating' => 4,
            'created_at' => Carbon::now()->subDays(15),  // Within 30d but not 7d
        ]);

        $response = $this->getJson(
            '/api/v1/vendor/dashboard/summary',
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('data.total_branches'));
        $this->assertEquals(3, $response->json('data.total_reviews_7d'));
        $this->assertEquals(5, $response->json('data.total_reviews_30d'));
        $this->assertGreaterThan(0, $response->json('data.average_rating'));
    }

    /**
     * Test: Dashboard includes top branches by rating
     */
    public function test_dashboard_top_branches()
    {
        $branch2 = $this->place->branches()->create([
            'name_en' => 'Branch 2',
            'name_ar' => 'فرع 2',
            'address_en' => 'Address 2',
            'address_ar' => 'العنوان 2',
        ]);

        // Branch 1: 5-star reviews
        Review::factory(2)->create(['branch_id' => $this->branch->id, 'rating' => 5]);

        // Branch 2: 3-star reviews
        Review::factory(2)->create(['branch_id' => $branch2->id, 'rating' => 3]);

        $response = $this->getJson(
            '/api/v1/vendor/dashboard/summary',
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        
        $topBranches = $response->json('data.top_branches');
        $this->assertCount(2, $topBranches);
        
        // First should have highest rating
        $this->assertEquals($this->branch->id, $topBranches[0]['id']);
        $this->assertGreaterThan($topBranches[1]['rating'], $topBranches[0]['rating']);
    }

    /**
     * Test: Dashboard shows vouchers used in past 7 and 30 days
     */
    public function test_dashboard_vouchers_by_period()
    {
        // Used 2 days ago (within 7d)
        Voucher::factory(2)->create([
            'brand_id' => $this->brand->id,
            'status' => 'USED',
            'used_at' => Carbon::now()->subDays(2),
        ]);

        // Used 10 days ago (within 30d but not 7d)
        Voucher::factory(3)->create([
            'brand_id' => $this->brand->id,
            'status' => 'USED',
            'used_at' => Carbon::now()->subDays(10),
        ]);

        // Used 40 days ago (outside 30d)
        Voucher::factory(1)->create([
            'brand_id' => $this->brand->id,
            'status' => 'USED',
            'used_at' => Carbon::now()->subDays(40),
        ]);

        $response = $this->getJson(
            '/api/v1/vendor/dashboard/summary',
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('data.vouchers_used_7d'));
        $this->assertEquals(5, $response->json('data.vouchers_used_30d'));
    }

    /**
     * Test: Dashboard only shows data for vendor's brand
     */
    public function test_dashboard_brand_scope()
    {
        // Create data in other brand
        $otherBrand = \App\Models\Brand::factory()->create();
        $otherPlace = \App\Models\Place::factory()->create(['brand_id' => $otherBrand->id]);
        $otherBranch = \App\Models\Branch::factory()->create(['place_id' => $otherPlace->id]);

        Review::factory(10)->create(['branch_id' => $otherBranch->id]);
        Voucher::factory(10)->create(['brand_id' => $otherBrand->id]);

        // Vendor sees only their brand's data
        $response = $this->getJson(
            '/api/v1/vendor/dashboard/summary',
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        $this->assertEquals(0, $response->json('data.total_reviews_7d'));
        $this->assertEquals(0, $response->json('data.vouchers_used_7d'));
    }

    /**
     * Test: Staff cannot access dashboard (forbidden)
     */
    public function test_dashboard_staff_forbidden()
    {
        $response = $this->getJson(
            '/api/v1/vendor/dashboard/summary',
            $this->vendorStaffHeaders()
        );

        $response->assertStatus(403);
    }

    /**
     * Test: Dashboard requires authentication
     */
    public function test_dashboard_requires_auth()
    {
        $response = $this->getJson('/api/v1/vendor/dashboard/summary');

        $response->assertStatus(401);
    }

    /**
     * Test: Dashboard with no data returns zero values
     */
    public function test_dashboard_empty_data()
    {
        // Don't create any reviews or vouchers
        
        $response = $this->getJson(
            '/api/v1/vendor/dashboard/summary',
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('data.total_branches'));
        $this->assertEquals(0, $response->json('data.total_reviews_7d'));
        $this->assertEquals(0, $response->json('data.total_reviews_30d'));
        $this->assertEquals(0, $response->json('data.average_rating'));
        $this->assertEmpty($response->json('data.top_branches'));
        $this->assertEquals(0, $response->json('data.vouchers_used_7d'));
        $this->assertEquals(0, $response->json('data.vouchers_used_30d'));
    }

    /**
     * Test: Average rating is calculated correctly
     */
    public function test_dashboard_average_rating_calculation()
    {
        // Create reviews with known ratings
        Review::factory()->create(['branch_id' => $this->branch->id, 'rating' => 5]);
        Review::factory()->create(['branch_id' => $this->branch->id, 'rating' => 3]);
        Review::factory()->create(['branch_id' => $this->branch->id, 'rating' => 4]);

        $response = $this->getJson(
            '/api/v1/vendor/dashboard/summary',
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        
        // Average of 5, 3, 4 = 4
        $avgRating = $response->json('data.average_rating');
        $this->assertEquals(4, round($avgRating));
    }
}
