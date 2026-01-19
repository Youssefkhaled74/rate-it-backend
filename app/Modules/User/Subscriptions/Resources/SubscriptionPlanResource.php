<?php

namespace App\Modules\User\Subscriptions\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
{
    public function toArray($request)
    {
        $lang = $request->header('X-Lang', 'en');
        $name = $lang === 'ar' ? ($this->name_ar ?: $this->name_en) : $this->name_en;
        $desc = $lang === 'ar' ? ($this->description_ar ?: $this->description_en) : $this->description_en;

        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $name,
            'description' => $desc,
            'price' => [
                'amount' => $this->price_cents / 100,
                'currency' => $this->currency,
                'formatted' => ($this->currency === 'USD' ? '$' : '') . ($this->price_cents / 100),
            ],
            'interval' => $this->interval,
            'interval_count' => $this->interval_count,
            'trial_days' => (int) $this->trial_days,
            'is_best_value' => (bool) $this->is_best_value,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
