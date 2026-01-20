<?php

namespace App\Modules\Admin\Catalog\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Admin\Catalog\Resources\BrandResource;
use App\Modules\Admin\Catalog\Resources\SubcategoryResource;

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
            'brand' => $this->when($this->brand, function () { return new BrandResource($this->brand); }),
            'subcategory' => $this->when($this->subcategory, function () { return new SubcategoryResource($this->subcategory); }),
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
