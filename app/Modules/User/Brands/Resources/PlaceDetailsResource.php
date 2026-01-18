<?php

namespace App\Modules\User\Brands\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlaceDetailsResource extends JsonResource
{
    public function toArray($request): array
    {
        $payload = $this->resource;
        $place = $payload['place'] ?? $this->resource;
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $nameCol = 'name_' . $locale;
        $descCol = 'description_' . $locale;

        $primaryBranch = $this->resource['primary_branch'] ?? null;
        return [
            'id' => $place->id,
            'name' => $place->{$nameCol} ?? $place->name,
            'logo_url' => $place->logo ? asset($place->logo) : null,
            'cover_image_url' => $place->cover_image ? asset($place->cover_image) : null,
            'description' => $place->{$descCol} ?? $place->description ?? null,
            'primary_address' => $primaryBranch ? $primaryBranch->address : null,
            'rating' => [
                'avg_rating' => $payload['avg_rating'] ?? null,
                'reviews_count' => $payload['reviews_count'] ?? 0,
            ],
            'reviews' => \App\Modules\User\Brands\Resources\ReviewResource::collection(collect($payload['reviews'] ?? [])),
            'can_write_review' => false,
        ];
    }
}
