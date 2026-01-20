<?php

namespace App\Modules\Admin\Subscriptions\Subscriptions\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminSubscriptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => $this->user ? ['id'=>$this->user->id,'name'=>$this->user->full_name ?? $this->user->name,'phone'=>$this->user->phone] : null,
            'plan' => $this->plan ? ['id'=>$this->plan->id,'code'=>$this->plan->code,'name_en'=>$this->plan->name_en,'name_ar'=>$this->plan->name_ar] : null,
            'subscription_status' => $this->subscription_status ?? $this->status ?? null,
            'started_at' => $this->started_at,
            'paid_until' => $this->paid_until,
            'canceled_at' => $this->canceled_at ?? null,
            'auto_renew' => $this->auto_renew ?? null,
            'provider' => $this->provider ?? null,
            'meta' => $this->meta ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
