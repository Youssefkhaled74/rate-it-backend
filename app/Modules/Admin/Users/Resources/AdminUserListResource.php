<?php

namespace App\Modules\Admin\Users\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name ?? ($this->first_name . ' ' . $this->last_name),
            'phone' => $this->phone,
            'email' => $this->email,
            'is_phone_verified' => (bool) ($this->phone_verified_at ?? false),
            'is_blocked' => (bool) ($this->is_blocked ?? false),
            'reviews_count' => $this->reviews_count ?? null,
            'points_balance' => null,
            'created_at' => $this->created_at,
        ];
    }
}
