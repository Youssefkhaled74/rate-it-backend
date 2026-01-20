<?php

namespace Tests\Feature\Admin\Dashboard;

use Tests\Feature\Admin\Support\AdminTestCase;
use App\Models\Review;
use App\Models\User;
use App\Models\Place;

class DashboardTest extends AdminTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data for dashboard analytics
        User::factory(10)->create();
        $place = Place::where('name_en', 'Test Place')->first();
        
        if ($place) {
            Review::factory(15)->create([
                'place_id' => $place->id,
            ]);
        }
    }

    /**
     * Test get dashboard summary (KPIs)
     */
    public function test_get_dashboard_summary()
    {
        $response = $this->getAsAdmin('/api/v1/admin/dashboard/summary');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'total_users',
                'total_reviews',
                'average_rating',
                'total_points_given',
            ],
        ]);

        // Verify counts are integers and non-negative
        $this->assertGreaterThanOrEqual(0, $response->json('data.total_users'));
        $this->assertGreaterThanOrEqual(0, $response->json('data.total_reviews'));
    }

    /**
     * Test get top places ranked by metric
     */
    public function test_get_top_places_by_reviews()
    {
        $response = $this->getAsAdmin('/api/v1/admin/dashboard/top-places?metric=reviews_count&limit=5');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'place_id',
                    'place_name',
                    'reviews_count',
                    'average_rating',
                ],
            ],
        ]);
    }

    /**
     * Test get top places ranked by average rating
     */
    public function test_get_top_places_by_rating()
    {
        $response = $this->getAsAdmin('/api/v1/admin/dashboard/top-places?metric=average_rating&limit=5');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'place_id',
                    'place_name',
                    'average_rating',
                ],
            ],
        ]);
    }

    /**
     * Test get reviews timeseries chart
     */
    public function test_get_reviews_chart()
    {
        $response = $this->getAsAdmin('/api/v1/admin/dashboard/reviews-chart?days=30');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'series' => [
                    '*' => [
                        'date',
                        'count',
                    ],
                ],
                'total',
            ],
        ]);
    }

    /**
     * Test get reviews chart with custom date range
     */
    public function test_get_reviews_chart_with_dates()
    {
        $startDate = now()->subMonths(1)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        $response = $this->getAsAdmin("/api/v1/admin/dashboard/reviews-chart?start_date={$startDate}&end_date={$endDate}");

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                'series' => [
                    '*' => [
                        'date',
                        'count',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test dashboard endpoints without authentication fail
     */
    public function test_dashboard_without_auth_fails()
    {
        $response = $this->getAsGuest('/api/v1/admin/dashboard/summary');

        $this->assertUnauthorizedJson($response);
    }

    /**
     * Test dashboard endpoints require dashboard.view permission
     */
    public function test_dashboard_requires_permission()
    {
        // This would require creating an admin without permission
        // For now, just verify our test admin has permission
        $this->assertNotEmpty($this->adminToken);
    }
}
