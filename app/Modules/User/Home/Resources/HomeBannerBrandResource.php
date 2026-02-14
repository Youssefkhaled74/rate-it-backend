<?php

namespace App\Modules\User\Home\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeBannerBrandResource extends JsonResource
{
    public function toArray($request): array
    {
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $nameColumn = 'name_' . $locale;

        return [
            'id' => $this->id,
            'name' => $this->{$nameColumn} ?? $this->name,
            'logo_url' => $this->logo ? asset($this->logo) : null,
        ];
    }
}
