<?php

namespace App\Modules\Admin\Catalog\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;

class BranchResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'place_id' => $this->place_id,
            'name' => $this->name,
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'address_en' => $this->address_en,
            'address_ar' => $this->address_ar,
            'phone' => $this->phone,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'qr_code' => $this->qr_code,
            'is_active' => (bool) $this->is_active,
            'timestamps' => [
                'created_at' => new TimestampResource($this->created_at),
                'updated_at' => new TimestampResource($this->updated_at),
            ],
        ];
    }
}
