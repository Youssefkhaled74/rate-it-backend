<?php

namespace App\Modules\User\Categories\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $nameCol = 'name_' . $locale;

        return [
            'id' => $this->id,
            'name' => $this->{$nameCol},
            'logo_url' => $this->logo ? asset($this->logo) : null,
        ];
    }
}
