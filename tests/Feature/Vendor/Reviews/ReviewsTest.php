<?php

namespace Tests\Feature\Vendor\Reviews;

use App\Models\Review;
use App\Models\RatingCriteria;
use App\Models\ReviewPhoto;
use Carbon\Carbon;
use Tests\Feature\Vendor\Support\VendorTestCase;

class ReviewsTest extends VendorTestCase
{
    /**
     * Test: List reviews with pagination
     */
    public function test_list_reviews_admin()
    {
        // Create test reviews for this brand
        Review::factory(3)->create([
            'branch_id' => $this->branch->id,
            'rating' => 5,
        ]);

        $response = $this->getJson(
            '/api/v1/vendor/reviews',
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'rating',
                    'comment',
                    'branch_id',
                    'created_at',
                ]
            ],
            'meta' => ['total', 'per_page', 'current_page'],
        ]);
    }

    /**
     * Test: List reviews with branch filter
     */
    public function test_list_reviews_branch_filter()
    {
        // Create reviews in multiple branches
        $branch2 = $this->place->branches()->create([
            'name_en' => 'Branch 2',
            'name_ar' => 'فرع 2',
            'address_en' => 'Address 2',
            'address_ar' => 'العنوان 2',
        ]);

        Review::factory(3)->create(['branch_id' => $this->branch->id]);
        Review::factory(2)->create(['branch_id' => $branch2->id]);

        $response = $this->getJson(
            "/api/v1/vendor/reviews?branch_id={$this->branch->id}",
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        $this->assertEquals(3, $response->json('meta.total'));
    }

    /**
     * Test: List reviews with date range filter
     */
    public function test_list_reviews_date_range_filter()
    {
        // Create reviews with different dates
        Review::factory()->create([
            'branch_id' => $this->branch->id,
            'created_at' => Carbon::now()->subDays(10),
        ]);
        Review::factory()->create([
            'branch_id' => $this->branch->id,
            'created_at' => Carbon::now()->subDays(5),
        ]);
        Review::factory()->create([
            'branch_id' => $this->branch->id,
            'created_at' => Carbon::now(),
        ]);

        $dateFrom = Carbon::now()->subDays(7)->toDateString();
        $dateTo = Carbon::now()->toDateString();

        $response = $this->getJson(
            "/api/v1/vendor/reviews?date_from={$dateFrom}&date_to={$dateTo}",
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('meta.total'));
    }

    /**
     * Test: List reviews with rating range filter
     */
    public function test_list_reviews_rating_filter()
    {
        Review::factory()->create(['branch_id' => $this->branch->id, 'rating' => 1]);
        Review::factory()->create(['branch_id' => $this->branch->id, 'rating' => 3]);
        Review::factory()->create(['branch_id' => $this->branch->id, 'rating' => 5]);

        $response = $this->getJson(
            '/api/v1/vendor/reviews?min_rating=4&max_rating=5',
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
    }

    /**
     * Test: List reviews with has_photos filter
     */
    public function test_list_reviews_with_photos_filter()
    {
        $review1 = Review::factory()->create(['branch_id' => $this->branch->id]);
        $review2 = Review::factory()->create(['branch_id' => $this->branch->id]);

        // Add photo to review1 only
        ReviewPhoto::factory()->create(['review_id' => $review1->id]);

        $response = $this->getJson(
            '/api/v1/vendor/reviews?has_photos=1',
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
    }

    /**
     * Test: List reviews with keyword search
     */
    public function test_list_reviews_keyword_search()
    {
        Review::factory()->create([
            'branch_id' => $this->branch->id,
            'comment' => 'Excellent service and fast delivery',
        ]);
        Review::factory()->create([
            'branch_id' => $this->branch->id,
            'comment' => 'Great food quality',
        ]);
        Review::factory()->create([
            'branch_id' => $this->branch->id,
            'comment' => 'Not satisfied with experience',
        ]);

        $response = $this->getJson(
            '/api/v1/vendor/reviews?keyword=excellent',
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
    }

    /**
     * Test: Get review detail
     */
    public function test_get_review_detail()
    {
        $review = Review::factory()->create(['branch_id' => $this->branch->id]);

        // Create review answers (rating criteria)
        $criteria = RatingCriteria::factory(2)->create(['brand_id' => $this->brand->id]);
        
        foreach ($criteria as $criterion) {
            $review->answers()->create([
                'rating_criteria_id' => $criterion->id,
                'rating' => 4,
            ]);
        }

        // Add photos
        ReviewPhoto::factory(2)->create(['review_id' => $review->id]);

        $response = $this->getJson(
            "/api/v1/vendor/reviews/{$review->id}",
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'rating',
                'comment',
                'branch_id',
                'answers' => [
                    '*' => [
                        'id',
                        'rating_criteria_id',
                        'rating',
                    ]
                ],
                'photos' => [
                    '*' => [
                        'id',
                        'url',
                    ]
                ],
                'created_at',
            ]
        ]);
    }

    /**
     * Test: Review detail not found (wrong brand)
     */
    public function test_get_review_detail_wrong_brand_not_found()
    {
        $otherBrand = \App\Models\Brand::factory()->create();
        $otherPlace = \App\Models\Place::factory()->create(['brand_id' => $otherBrand->id]);
        $otherBranch = \App\Models\Branch::factory()->create(['place_id' => $otherPlace->id]);
        $otherReview = Review::factory()->create(['branch_id' => $otherBranch->id]);

        $response = $this->getJson(
            "/api/v1/vendor/reviews/{$otherReview->id}",
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(404);
    }

    /**
     * Test: Staff sees only their branch reviews
     */
    public function test_list_reviews_staff_branch_scope()
    {
        // Create reviews in multiple branches
        $branch2 = $this->place->branches()->create([
            'name_en' => 'Branch 2',
            'name_ar' => 'فرع 2',
            'address_en' => 'Address 2',
            'address_ar' => 'العنوان 2',
        ]);

        Review::factory(3)->create(['branch_id' => $this->branch->id]);
        Review::factory(2)->create(['branch_id' => $branch2->id]);

        // Staff only sees their assigned branch
        $response = $this->getJson(
            '/api/v1/vendor/reviews',
            $this->vendorStaffHeaders()
        );

        $response->assertStatus(200);
        $this->assertEquals(3, $response->json('meta.total'));
    }

    /**
     * Test: Admin sees all brand reviews (both branches)
     */
    public function test_list_reviews_admin_sees_all_branches()
    {
        $branch2 = $this->place->branches()->create([
            'name_en' => 'Branch 2',
            'name_ar' => 'فرع 2',
            'address_en' => 'Address 2',
            'address_ar' => 'العنوان 2',
        ]);

        Review::factory(3)->create(['branch_id' => $this->branch->id]);
        Review::factory(2)->create(['branch_id' => $branch2->id]);

        // Admin sees all
        $response = $this->getJson(
            '/api/v1/vendor/reviews',
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        $this->assertEquals(5, $response->json('meta.total'));
    }

    /**
     * Test: Pagination works correctly
     */
    public function test_reviews_pagination()
    {
        Review::factory(15)->create(['branch_id' => $this->branch->id]);

        $response = $this->getJson(
            '/api/v1/vendor/reviews?per_page=5&page=2',
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(200);
        $this->assertEquals(15, $response->json('meta.total'));
        $this->assertEquals(5, $response->json('meta.per_page'));
        $this->assertEquals(2, $response->json('meta.current_page'));
    }
}
