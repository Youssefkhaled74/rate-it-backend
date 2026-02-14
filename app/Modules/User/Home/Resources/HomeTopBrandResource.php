<?php

namespace App\Modules\User\Home\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeTopBrandResource extends JsonResource
{
    public function toArray($request): array
    {
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $nameColumn = 'name_' . $locale;
        $subcategoryColumn = 'subcategory_name_' . $locale;

        return [
            'id' => $this->id,
            'name' => $this->{$nameColumn} ?? $this->name,
            'logo_url' => $this->logo ? asset($this->logo) : ($this->logo_url ?? null),
            'subcategory_name' => $this->{$subcategoryColumn} ?? null,
            'rating' => [
                'overall_avg' => isset($this->overall_avg_rating) ? round((float) $this->overall_avg_rating, 1) : null,
                'review_score_avg' => isset($this->avg_review_score) ? round((float) $this->avg_review_score, 1) : null,
                'reviews_count' => (int) ($this->reviews_count ?? 0),
            ],
        ];
    }
}
