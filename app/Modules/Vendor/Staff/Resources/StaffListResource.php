<?php

namespace App\Modules\Vendor\Staff\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;

class StaffListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'is_active' => $this->is_active ?? true,
            'branch' => [
                'id' => $this->branch->id ?? null,
                'name' => $this->branch->name ?? null,
            ],
            'created_at' => new TimestampResource($this->created_at),
        ];
    }
}
