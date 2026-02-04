<?php

namespace App\Modules\Admin\Users\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;

class AdminUserListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? ($this->first_name . ' ' . $this->last_name),
            'phone' => $this->phone,
            'email' => $this->email,
            'is_phone_verified' => (bool) ($this->phone_verified_at ?? false),
            'is_blocked' => (bool) ($this->is_blocked ?? false),
            'reviews_count' => $this->reviews_count ?? null,
            'points_balance' => null,
            'timestamps' => [
                'created_at' => new TimestampResource($this->created_at),
            ],
        ];
    }
}
