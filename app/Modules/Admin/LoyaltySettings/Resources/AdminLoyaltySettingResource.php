<?php

namespace App\Modules\Admin\LoyaltySettings\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminLoyaltySettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'version' => $this->version,
            'points_per_review' => (int) $this->points_per_review,
            'point_value_money' => isset($this->point_value_money) ? (float) $this->point_value_money : null,
            'currency' => $this->currency,
            'is_active' => (bool) $this->is_active,
            'created_by_admin_id' => $this->created_by_admin_id ?? null,
            'activated_by_admin_id' => $this->activated_by_admin_id ?? null,
            'activated_at' => $this->activated_at ?? null,
            'created_at' => $this->created_at,
        ];
    }
}
