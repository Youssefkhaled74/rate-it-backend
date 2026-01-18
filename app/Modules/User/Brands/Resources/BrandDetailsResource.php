<?php

namespace App\Modules\User\Brands\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandDetailsResource extends JsonResource
{
    public function toArray($request): array
    {
        $payload = $this->resource;
        $brand = $payload['brand'];
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $nameCol = 'name_' . $locale;
        $descCol = 'description_' . $locale;

        return [
            'id' => $brand->id,
            'name' => $brand->{$nameCol} ?? $brand->name,
            'logo_url' => $brand->logo ? asset($brand->logo) : ($brand->logo_url ?? null),
            'cover_image_url' => $brand->cover_image ? asset($brand->cover_image) : null,
            'description' => $brand->{$descCol} ?? null,
            'rating' => [
                'avg_rating' => $payload['avg_rating'],
                'reviews_count' => $payload['reviews_count'],
            ],
        ];
    }
}
