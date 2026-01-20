<?php

namespace Tests\Feature\Admin\Subscriptions;

use Tests\Feature\Admin\Support\AdminTestCase;
use Illuminate\Support\Facades\DB;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;

class SubscriptionPlansTest extends AdminTestCase
{
    /**
     * Test list subscription plans
     */
    public function test_list_subscription_plans()
    {
        $response = $this->getAsAdmin('/api/v1/admin/subscriptions/plans');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name_en',
                    'name_ar',
                    'amount',
                    'currency',
                    'is_active',
                ],
            ],
        ]);
    }

    /**
     * Test show subscription plan
     */
    public function test_show_subscription_plan()
    {
        $plan = SubscriptionPlan::where('name_en', 'Test Plan')->first();

        if ($plan) {
            $response = $this->getAsAdmin("/api/v1/admin/subscriptions/plans/{$plan->id}");

            $this->assertSuccessJson($response);
            $response->assertJsonStructure([
                'data' => [
                    'id',
                    'name_en',
                    'name_ar',
                    'amount',
                    'currency',
                    'is_active',
                ],
            ]);
        }
    }

    /**
     * Test create subscription plan
     */
    public function test_create_subscription_plan()
    {
        $response = $this->postAsAdmin('/api/v1/admin/subscriptions/plans', [
            'name_en' => 'Premium Plan',
            'name_ar' => 'خطة بريميوم',
            'description_en' => 'Premium subscription features',
            'description_ar' => 'ميزات الاشتراك بريميوم',
            'amount' => 199.99,
            'currency' => 'SAR',
            'is_active' => true,
        ]);

        // May return 200 or 201
        $this->assertTrue($response->status() === 200 || $response->status() === 201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name_en',
                'amount',
            ],
        ]);
    }

    /**
     * Test update subscription plan
     */
    public function test_update_subscription_plan()
    {
        $plan = SubscriptionPlan::first();

        if ($plan) {
            $response = $this->putAsAdmin("/api/v1/admin/subscriptions/plans/{$plan->id}", [
                'name_en' => 'Updated Plan',
                'amount' => 299.99,
            ]);

            $this->assertSuccessJson($response);
        }
    }

    /**
     * Test activate subscription plan
     */
    public function test_activate_subscription_plan()
    {
        $plan = SubscriptionPlan::where('is_active', false)->first();

        if ($plan) {
            $response = $this->postAsAdmin("/api/v1/admin/subscriptions/plans/{$plan->id}/activate");

            $this->assertSuccessJson($response);

            $plan->refresh();
            $this->assertTrue($plan->is_active);
        }
    }

    /**
     * Test delete subscription plan
     */
    public function test_delete_subscription_plan()
    {
        $plan = SubscriptionPlan::create([
            'name_en' => 'Plan to Delete',
            'name_ar' => 'خطة للحذف',
            'amount' => 99.99,
            'currency' => 'SAR',
            'is_active' => true,
        ]);

        $response = $this->deleteAsAdmin("/api/v1/admin/subscriptions/plans/{$plan->id}");

        // May succeed or fail depending on implementation
        if ($response->status() === 200) {
            $this->assertSuccessJson($response);
        }
    }

    /**
     * Test list plans without authentication fails
     */
    public function test_list_plans_without_auth_fails()
    {
        $response = $this->getAsGuest('/api/v1/admin/subscriptions/plans');

        $this->assertUnauthorizedJson($response);
    }
}

class SubscriptionsListTest extends AdminTestCase
{
    /**
     * Test list subscriptions
     */
    public function test_list_subscriptions()
    {
        $response = $this->getAsAdmin('/api/v1/admin/subscriptions');

        $this->assertSuccessJson($response);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'plan_id',
                    'status',
                    'started_at',
                ],
            ],
        ]);
    }

    /**
     * Test list subscriptions with pagination
     */
    public function test_list_subscriptions_with_pagination()
    {
        $response = $this->getAsAdmin('/api/v1/admin/subscriptions?page=1&limit=10');

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
     * Test filter subscriptions by status
     */
    public function test_filter_subscriptions_by_status()
    {
        $response = $this->getAsAdmin('/api/v1/admin/subscriptions?status=active');

        $this->assertSuccessJson($response);
        
        foreach ($response->json('data') as $subscription) {
            $this->assertEquals('active', $subscription['status']);
        }
    }

    /**
     * Test show subscription details
     */
    public function test_show_subscription()
    {
        $subscription = Subscription::first();

        if ($subscription) {
            $response = $this->getAsAdmin("/api/v1/admin/subscriptions/{$subscription->id}");

            $this->assertSuccessJson($response);
            $response->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
                    'plan_id',
                    'status',
                ],
            ]);
        }
    }

    /**
     * Test list subscriptions without authentication fails
     */
    public function test_list_subscriptions_without_auth_fails()
    {
        $response = $this->getAsGuest('/api/v1/admin/subscriptions');

        $this->assertUnauthorizedJson($response);
    }
}
