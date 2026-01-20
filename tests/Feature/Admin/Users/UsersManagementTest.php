<?php

namespace Tests\Feature\Admin\Users;

use Tests\Feature\Admin\Support\AdminTestCase;
use App\Models\User;
use App\Models\Review;
use App\Models\PointsTransaction;
use App\Models\Place;

class UsersManagementTest extends AdminTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users with data for testing
        User::factory(5)->create();
    }

    /**
     * Test list users
     */
    public function test_list_users()
    {
        $response = $this->getAsAdmin('/api/v1/admin/users');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'is_blocked',
                ],
            ],
        ]);
    }

    /**
     * Test list users with pagination
     */
    public function test_list_users_with_pagination()
    {
        $response = $this->getAsAdmin('/api/v1/admin/users?page=1&limit=10');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data',
            'meta' => [
                'page',
                'limit',
                'total',
            ],
        ]);
    }

    /**
     * Test search users by name
     */
    public function test_search_users_by_name()
    {
        $user = User::first();

        $response = $this->getAsAdmin("/api/v1/admin/users?search={$user->name}");

        $this->assertSuccessJson($response);
    }

    /**
     * Test show user details
     */
    public function test_show_user()
    {
        $user = User::first();

        $response = $this->getAsAdmin("/api/v1/admin/users/{$user->id}");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'is_blocked',
                'phone',
                'created_at',
            ],
        ]);
    }

    /**
     * Test block user
     */
    public function test_block_user()
    {
        $user = User::where('is_blocked', false)->first() ?? User::first();
        $user->update(['is_blocked' => false]);

        $response = $this->postAsAdmin("/api/v1/admin/users/{$user->id}/block");

        $this->assertSuccessJson($response);

        $user->refresh();
        $this->assertTrue($user->is_blocked);
    }

    /**
     * Test unblock user
     */
    public function test_unblock_user()
    {
        $user = User::where('is_blocked', true)->first();
        if (!$user) {
            $user = User::first();
            $user->update(['is_blocked' => true]);
        }

        $response = $this->postAsAdmin("/api/v1/admin/users/{$user->id}/unblock");

        $this->assertSuccessJson($response);

        $user->refresh();
        $this->assertFalse($user->is_blocked);
    }

    /**
     * Test get user reviews
     */
    public function test_get_user_reviews()
    {
        $user = User::first();
        $place = Place::where('name_en', 'Test Place')->first();
        
        Review::factory(3)->create([
            'user_id' => $user->id,
            'place_id' => $place->id,
        ]);

        $response = $this->getAsAdmin("/api/v1/admin/users/{$user->id}/reviews");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'rating',
                    'created_at',
                ],
            ],
        ]);
    }

    /**
     * Test get user points balance and history
     */
    public function test_get_user_points_balance()
    {
        $user = User::first();
        
        // Create test points transactions
        PointsTransaction::factory(5)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->getAsAdmin("/api/v1/admin/users/{$user->id}/points");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'balance',
                'transactions' => [
                    '*' => [
                        'id',
                        'amount',
                        'type',
                        'created_at',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test list users without authentication fails
     */
    public function test_list_users_without_auth_fails()
    {
        $response = $this->getAsGuest('/api/v1/admin/users');

        $this->assertUnauthorizedJson($response);
    }

    /**
     * Test show non-existent user returns 404
     */
    public function test_show_non_existent_user_returns_404()
    {
        $response = $this->getAsAdmin('/api/v1/admin/users/99999');

        $this->assertNotFoundJson($response);
    }
}
