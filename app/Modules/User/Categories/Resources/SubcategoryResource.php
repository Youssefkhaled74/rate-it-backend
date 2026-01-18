<?php

namespace App\Modules\User\Categories\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubcategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $nameCol = 'name_' . $locale;

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->{$nameCol},
            'image_url' => $this->image ? asset($this->image) : null,
        ];
    }
}
