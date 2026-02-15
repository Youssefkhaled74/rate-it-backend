<?php

namespace Tests\Feature\Vendor\Reviews;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Review;
use App\Models\ReviewPhoto;
use Carbon\Carbon;
use Tests\Feature\Vendor\Support\VendorTestCase;

class ReviewsTest extends VendorTestCase
{
    public function test_list_reviews_admin(): void
    {
        Review::factory(3)->create([
            'branch_id' => $this->branch->id,
            'overall_rating' => 5,
        ]);

        $response = $this->getJson('/api/v1/vendor/reviews', $this->vendorAdminHeaders());

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'overall_rating', 'comment', 'branch', 'created_at'],
            ],
            'meta' => ['page', 'limit', 'total', 'has_next', 'last_page'],
        ]);
    }

    public function test_list_reviews_branch_filter(): void
    {
        $branch2 = Branch::factory()->create(['brand_id' => $this->brand->id]);

        Review::factory(3)->create(['branch_id' => $this->branch->id]);
        Review::factory(2)->create(['branch_id' => $branch2->id]);

        $response = $this->getJson(
            "/api/v1/vendor/reviews?branch_id={$this->branch->id}",
            $this->vendorAdminHeaders()
        );

        $response->assertOk();
        $this->assertEquals(3, $response->json('meta.total'));
    }

    public function test_list_reviews_date_range_filter(): void
    {
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

        $response->assertOk();
        $this->assertEquals(2, $response->json('meta.total'));
    }

    public function test_list_reviews_rating_filter(): void
    {
        Review::factory()->create(['branch_id' => $this->branch->id, 'overall_rating' => 1]);
        Review::factory()->create(['branch_id' => $this->branch->id, 'overall_rating' => 3]);
        Review::factory()->create(['branch_id' => $this->branch->id, 'overall_rating' => 5]);

        $response = $this->getJson(
            '/api/v1/vendor/reviews?min_rating=4&max_rating=5',
            $this->vendorAdminHeaders()
        );

        $response->assertOk();
        $this->assertEquals(1, $response->json('meta.total'));
    }

    public function test_list_reviews_with_photos_filter(): void
    {
        $reviewWithPhoto = Review::factory()->create(['branch_id' => $this->branch->id]);
        Review::factory()->create(['branch_id' => $this->branch->id]);

        ReviewPhoto::query()->create([
            'review_id' => $reviewWithPhoto->id,
            'storage_path' => 'uploads/reviews/test.jpg',
            'encrypted' => false,
        ]);

        $response = $this->getJson('/api/v1/vendor/reviews?has_photos=1', $this->vendorAdminHeaders());

        $response->assertOk();
        $this->assertEquals(1, $response->json('meta.total'));
    }

    public function test_get_review_detail(): void
    {
        $review = Review::factory()->create(['branch_id' => $this->branch->id]);

        $response = $this->getJson("/api/v1/vendor/reviews/{$review->id}", $this->vendorAdminHeaders());

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'overall_rating',
                'comment',
                'branch',
                'photos',
                'answers',
                'created_at',
            ],
        ]);
    }

    public function test_get_review_detail_wrong_brand_not_found(): void
    {
        $otherBrand = Brand::factory()->create();
        $otherBranch = Branch::factory()->create(['brand_id' => $otherBrand->id]);
        $otherReview = Review::factory()->create(['branch_id' => $otherBranch->id]);

        $response = $this->getJson(
            "/api/v1/vendor/reviews/{$otherReview->id}",
            $this->vendorAdminHeaders()
        );

        $response->assertStatus(404);
    }
}

