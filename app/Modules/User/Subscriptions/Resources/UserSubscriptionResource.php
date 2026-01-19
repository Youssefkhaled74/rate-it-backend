<?php

namespace App\Modules\User\Subscriptions\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UserSubscriptionResource extends JsonResource
{
    public function toArray($request)
    {
        $plan = $this->plan;
        $lang = $request->header('X-Lang', 'en');
        $name = $plan ? ($lang === 'ar' ? ($plan->name_ar ?: $plan->name_en) : $plan->name_en) : null;

        $trialEnds = $this->free_until ? Carbon::parse($this->free_until)->toIso8601String() : null;
        $periodEnds = $this->paid_until ? Carbon::parse($this->paid_until)->toIso8601String() : null;
        $daysLeft = null;
        if ($periodEnds) {
            $daysLeft = Carbon::now()->diffInDays(Carbon::parse($periodEnds), false);
            if ($daysLeft < 0) $daysLeft = 0;
        }

        return [
            'id' => $this->id,
            'status' => $this->subscription_status ?? $this->status,
            'auto_renew' => (bool) ($this->auto_renew ?? true),
            'plan' => $plan ? [
                'id' => $plan->id,
                'code' => $plan->code,
                'name' => $name,
                'price' => ['amount' => $plan->price_cents/100, 'currency' => $plan->currency],
                'interval' => $plan->interval,
            ] : null,
            'trial_ends_at' => $trialEnds,
            'current_period_ends_at' => $periodEnds,
            'days_left' => $daysLeft,
        ];
    }
}
