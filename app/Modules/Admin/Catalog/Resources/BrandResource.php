<?php

namespace App\Modules\Admin\Catalog\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;

class BrandResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'logo' => $this->logo,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            'timestamps' => [
                'created_at' => new TimestampResource($this->created_at),
                'updated_at' => new TimestampResource($this->updated_at),
            ],
        ];
    }
}
