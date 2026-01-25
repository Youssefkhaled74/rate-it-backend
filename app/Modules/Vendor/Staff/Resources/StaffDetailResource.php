<?php

namespace App\Modules\Vendor\Staff\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;

class StaffDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'is_active' => $this->is_active ?? true,
            'role' => $this->role,
            'branch' => [
                'id' => $this->branch->id ?? null,
                'name' => $this->branch->name ?? null,
                'address' => $this->branch->address ?? null,
            ],
            'brand' => [
                'id' => $this->brand->id ?? null,
                'name' => $this->brand->name ?? null,
            ],
            'created_at' => new TimestampResource($this->created_at),
            'updated_at' => new TimestampResource($this->updated_at),
        ];
    }
}
