<?php

namespace App\Modules\User\Brands\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlaceCardResource extends JsonResource
{
    public function toArray($request): array
    {
        $place = $this->resource['place'] ?? $this->resource;
        $name = $this->resource['name'] ?? ($place->name_en ?? $place->name);

        return [
            'id' => $place->id,
            'name' => $name,
            'logo_url' => $place->logo ? asset($place->logo) : null,
            'avg_rating' => $this->resource['avg_rating'] ?? null,
            'reviews_count' => $this->resource['reviews_count'] ?? 0,
        ];
    }
}
