<?php

namespace App\Modules\Admin\Subscriptions\Plans\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminSubscriptionPlanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'description_en' => $this->description_en,
            'description_ar' => $this->description_ar,
            'price_cents' => $this->price_cents,
            'currency' => $this->currency,
            'interval' => $this->interval,
            'interval_count' => $this->interval_count,
            'trial_days' => $this->trial_days,
            'is_active' => (bool)$this->is_active,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
