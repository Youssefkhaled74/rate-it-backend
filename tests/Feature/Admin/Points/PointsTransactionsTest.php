<?php

namespace Tests\Feature\Admin\Points;

use Tests\Feature\Admin\Support\AdminTestCase;
use App\Models\PointsTransaction;

class PointsTransactionsTest extends AdminTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test transactions
        PointsTransaction::factory(10)->create();
    }

    /**
     * Test list points transactions
     */
    public function test_list_points_transactions()
    {
        $response = $this->getAsAdmin('/api/v1/admin/points');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'amount',
                    'type',
                    'description',
                    'created_at',
                ],
            ],
        ]);
    }

    /**
     * Test list transactions with pagination
     */
    public function test_list_transactions_with_pagination()
    {
        $response = $this->getAsAdmin('/api/v1/admin/points?page=1&limit=5');

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
     * Test filter transactions by type
     */
    public function test_filter_transactions_by_type()
    {
        $response = $this->getAsAdmin('/api/v1/admin/points?type=REVIEW');

        $this->assertSuccessJson($response);
        
        foreach ($response->json('data') as $transaction) {
            $this->assertEquals('REVIEW', $transaction['type']);
        }
    }

    /**
     * Test filter transactions by user_id
     */
    public function test_filter_transactions_by_user()
    {
        $transaction = PointsTransaction::first();

        $response = $this->getAsAdmin("/api/v1/admin/points?user_id={$transaction->user_id}");

        $this->assertSuccessJson($response);
        
        foreach ($response->json('data') as $item) {
            $this->assertEquals($transaction->user_id, $item['user_id']);
        }
    }

    /**
     * Test show transaction details
     */
    public function test_show_transaction_details()
    {
        $transaction = PointsTransaction::first();

        $response = $this->getAsAdmin("/api/v1/admin/points/{$transaction->id}");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'amount',
                'type',
                'description',
                'created_at',
            ],
        ]);
    }

    /**
     * Test list transactions without authentication fails
     */
    public function test_list_transactions_without_auth_fails()
    {
        $response = $this->getAsGuest('/api/v1/admin/points');

        $this->assertUnauthorizedJson($response);
    }

    /**
     * Test show non-existent transaction returns 404
     */
    public function test_show_non_existent_transaction_returns_404()
    {
        $response = $this->getAsAdmin('/api/v1/admin/points/99999');

        $this->assertNotFoundJson($response);
    }
}
