<?php

namespace App\Modules\User\Lookups\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AreaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'city_id' => $this->city_id,
            'name' => app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en,
        ];
    }
}
