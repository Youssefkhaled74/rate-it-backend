<?php

namespace App\Modules\User\Subscriptions\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\User\Subscriptions\Services\SubscriptionService;
use App\Modules\User\Subscriptions\Resources\SubscriptionPlanResource;
use App\Modules\User\Subscriptions\Resources\UserSubscriptionResource;
use App\Modules\User\Subscriptions\Requests\CheckoutRequest;

class SubscriptionsController extends Controller
{
    protected SubscriptionService $service;

    public function __construct(SubscriptionService $service)
    {
        $this->service = $service;
    }

    public function plans(Request $request)
    {
        $lang = $request->header('X-Lang', 'en');
        $plans = $this->service->getPlans();
        return response()->json(['success' => true, 'message' => 'Plans retrieved', 'data' => SubscriptionPlanResource::collection($plans)->resolve(), 'meta' => null], 200);
    }

    public function me(Request $request)
    {
        $sub = $this->service->getUserSubscription($request->user());
        if (!$sub) {
            return response()->json(['success' => true, 'message' => 'No subscription', 'data' => null, 'meta' => null], 200);
        }
        return response()->json(['success' => true, 'message' => 'Subscription retrieved', 'data' => new UserSubscriptionResource($sub), 'meta' => null], 200);
    }

    public function checkout(CheckoutRequest $request)
    {
        $user = $request->user();
        $planId = $request->input('plan_id') ?? $request->input('plan_code');
        $provider = $request->input('provider', 'manual');

        // Resolve plan by id or code
        $plan = $this->service->getPlan($planId);
        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Plan not found', 'data' => null, 'meta' => null], 404);
        }

        try {
            $result = $this->service->checkout($user, $plan, $provider);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null, 'meta' => null], 409);
        }

        // Simple manual checkout response
        $payload = ['type' => 'manual', 'payload' => ['subscription_id' => $result['subscription']->id, 'transaction_id' => $result['transaction']->id]];
        return response()->json(['success' => true, 'message' => 'Checkout created', 'data' => ['checkout' => $payload], 'meta' => null], 200);
    }

    public function cancelAutoRenew(Request $request)
    {
        $sub = $this->service->cancelAutoRenew($request->user());
        if (!$sub) return response()->json(['success' => false, 'message' => 'No subscription found', 'data'=>null,'meta'=>null], 404);
        return response()->json(['success' => true, 'message' => 'Auto-renew cancelled', 'data' => new UserSubscriptionResource($sub), 'meta' => null], 200);
    }

    public function resumeAutoRenew(Request $request)
    {
        try {
            $sub = $this->service->resumeAutoRenew($request->user());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null, 'meta' => null], 400);
        }
        return response()->json(['success' => true, 'message' => 'Auto-renew resumed', 'data' => new UserSubscriptionResource($sub), 'meta' => null], 200);
    }

    public function history(Request $request)
    {
        $items = $this->service->history($request->user());
        return response()->json(['success' => true, 'message' => 'History retrieved', 'data' => $items, 'meta' => null], 200);
    }
}
