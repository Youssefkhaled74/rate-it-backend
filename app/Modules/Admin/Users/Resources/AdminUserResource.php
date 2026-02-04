<?php

namespace App\Modules\Admin\Users\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;

class AdminUserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? ($this->first_name . ' ' . $this->last_name),
            'phone' => $this->phone,
            'email' => $this->email,
            'birth_date' => $this->birth_date ?? null,
            'gender' => $this->whenLoaded('gender', function(){ return ['id' => $this->gender->id ?? null, 'name' => $this->gender->name ?? null]; }),
            'nationality' => $this->whenLoaded('nationality', function(){ return ['id' => $this->nationality->id ?? null, 'name' => $this->nationality->name ?? null]; }),
            'is_phone_verified' => (bool) ($this->phone_verified_at ?? false),
            'is_blocked' => (bool) ($this->is_blocked ?? false),
            'blocked_reason' => $this->blocked_reason ?? null,
            'blocked_at' => $this->blocked_at ? new TimestampResource($this->blocked_at) : null,
            'points_balance' => isset($this->points_balance) ? $this->points_balance : null,
            'timestamps' => [
                'created_at' => new TimestampResource($this->created_at),
            ],
        ];
    }
}
