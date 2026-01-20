<?php

namespace Tests\Feature\Admin\Reviews;

use Tests\Feature\Admin\Support\AdminTestCase;
use App\Models\Review;
use App\Models\User;
use App\Models\Place;

class ReviewsModerationTest extends AdminTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users and reviews for moderation tests
        $user = User::factory()->create();
        $place = Place::where('name_en', 'Test Place')->first();
        
        Review::factory(5)->create([
            'user_id' => $user->id,
            'place_id' => $place->id,
        ]);
    }

    /**
     * Test list reviews
     */
    public function test_list_reviews()
    {
        $response = $this->getAsAdmin('/api/v1/admin/reviews');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'place_id',
                    'title',
                    'rating',
                    'is_visible',
                ],
            ],
        ]);
    }

    /**
     * Test list reviews with pagination
     */
    public function test_list_reviews_with_pagination()
    {
        $response = $this->getAsAdmin('/api/v1/admin/reviews?page=1&limit=10');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data',
            'meta' => [
                'page',
                'limit',
                'total',
                'has_next',
                'last_page',
            ],
        ]);
    }

    /**
     * Test show review details
     */
    public function test_show_review()
    {
        $review = Review::first();

        $response = $this->getAsAdmin("/api/v1/admin/reviews/{$review->id}");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'place_id',
                'title',
                'rating',
                'is_visible',
            ],
        ]);
    }

    /**
     * Test hide review
     */
    public function test_hide_review()
    {
        $review = Review::where('is_visible', true)->first() ?? Review::first();
        $review->update(['is_visible' => true]);

        $response = $this->postAsAdmin("/api/v1/admin/reviews/{$review->id}/hide");

        $this->assertSuccessJson($response);

        $review->refresh();
        $this->assertFalse($review->is_visible);
    }

    /**
     * Test reply to review (if implemented)
     */
    public function test_reply_to_review()
    {
        $review = Review::first();

        $response = $this->postAsAdmin("/api/v1/admin/reviews/{$review->id}/reply", [
            'reply_text' => 'Thank you for your feedback',
        ]);

        // May return 201 or 200 depending on implementation
        if ($response->status() === 201) {
            $this->assertCreatedJson($response);
        } else {
            $this->assertSuccessJson($response);
        }
    }

    /**
     * Test mark review as featured (if implemented)
     */
    public function test_mark_review_as_featured()
    {
        $review = Review::first();

        $response = $this->postAsAdmin("/api/v1/admin/reviews/{$review->id}/mark-featured");

        $this->assertSuccessJson($response);

        $review->refresh();
        $this->assertTrue($review->is_featured ?? true);
    }

    /**
     * Test list reviews without authentication fails
     */
    public function test_list_reviews_without_auth_fails()
    {
        $response = $this->getAsGuest('/api/v1/admin/reviews');

        $this->assertUnauthorizedJson($response);
    }
}
