<?php

namespace App\Modules\User\Subscriptions\Services;

use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\SubscriptionTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function getPlans()
    {
        return SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
    }

    public function getPlan($idOrCode)
    {
        if (is_numeric($idOrCode)) {
            return SubscriptionPlan::find($idOrCode);
        }
        return SubscriptionPlan::where('code', $idOrCode)->first();
    }

    public function getUserSubscription($user)
    {
        return Subscription::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
    }

    public function checkout($user, $plan, $provider = 'manual')
    {
        // Prevent multiple active subscriptions
        $existing = Subscription::where('user_id', $user->id)
            ->whereIn('subscription_status', ['trialing','active','past_due'])
            ->first();
        if ($existing) {
            throw new \Exception('User already has an active subscription');
        }

        return DB::transaction(function () use ($user, $plan, $provider) {
            $now = Carbon::now();
            $trialEnds = null;
            $currentPeriodEnds = null;

            if ($plan->trial_days > 0) {
                $trialEnds = $now->copy()->addDays($plan->trial_days);
                if ($plan->interval === 'month') {
                    $currentPeriodEnds = $trialEnds->copy()->addMonths($plan->interval_count);
                } else {
                    $currentPeriodEnds = $trialEnds->copy()->addYears($plan->interval_count);
                }
                $status = 'trialing';
            } else {
                // No trial: start paid period now
                if ($plan->interval === 'month') {
                    $currentPeriodEnds = $now->copy()->addMonths($plan->interval_count);
                } else {
                    $currentPeriodEnds = $now->copy()->addYears($plan->interval_count);
                }
                $status = 'active';
            }

            $sub = Subscription::create([
                'user_id' => $user->id,
                'status' => 'ACTIVE', // legacy column
                'subscription_status' => $status,
                'started_at' => $now,
                'free_until' => $trialEnds,
                'paid_until' => $currentPeriodEnds,
                'subscription_plan_id' => $plan->id,
                'auto_renew' => true,
                'provider' => $provider,
                'meta' => null,
            ]);

            // Create a pending transaction placeholder when no trial
            $txn = SubscriptionTransaction::create([
                'user_id' => $user->id,
                'subscription_id' => $sub->id,
                'plan_id' => $plan->id,
                'amount_cents' => $plan->price_cents,
                'currency' => $plan->currency,
                'status' => $plan->trial_days > 0 ? 'pending' : 'pending',
                'provider' => $provider,
                'meta' => null,
            ]);

            return ['subscription' => $sub, 'transaction' => $txn];
        });
    }

    public function cancelAutoRenew($user)
    {
        $sub = $this->getUserSubscription($user);
        if (!$sub) return null;
        $sub->auto_renew = false;
        $sub->canceled_at = Carbon::now();
        $sub->save();
        return $sub;
    }

    public function resumeAutoRenew($user)
    {
        $sub = $this->getUserSubscription($user);
        if (!$sub) return null;
        if ($sub->subscription_status === 'expired') {
            throw new \Exception('Cannot resume an expired subscription');
        }
        $sub->auto_renew = true;
        $sub->canceled_at = null;
        $sub->save();
        return $sub;
    }

    public function history($user)
    {
        return SubscriptionTransaction::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
    }
}
