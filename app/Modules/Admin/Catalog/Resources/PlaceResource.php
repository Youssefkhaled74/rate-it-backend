<?php

namespace App\Modules\Admin\Catalog\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'address_en' => $this->address_en,
            'address_ar' => $this->address_ar,
            'phone' => $this->phone,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'logo' => $this->logo,
            'is_active' => (bool) $this->is_active,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
