<?php

namespace App\Modules\Vendor\Branches\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;

class BranchDetailsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'place_id' => $this->place_id,
            'name' => $this->name,
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'address' => $this->address,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'qr_code_value' => $this->qr_code_value,
            'qr_generated_at' => $this->qr_generated_at?->toIso8601String(),
            'review_cooldown_days' => (int) $this->review_cooldown_days,
            'working_hours' => $this->working_hours,
            'place' => $this->place ? [
                'id' => $this->place->id,
                'name' => $this->place->name,
            ] : null,
            'timestamps' => [
                'created_at' => new TimestampResource($this->created_at),
                'updated_at' => new TimestampResource($this->updated_at),
            ],
        ];
    }
}
