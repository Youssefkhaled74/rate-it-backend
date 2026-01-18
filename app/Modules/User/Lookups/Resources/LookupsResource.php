<?php

namespace App\Modules\User\Lookups\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LookupsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'genders' => $this->resource['genders'] ?? [],
            'nationalities' => $this->resource['nationalities'] ?? [],
        ];
    }
}
