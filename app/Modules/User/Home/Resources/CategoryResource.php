<?php

namespace App\Modules\User\Home\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $locale = app()->getLocale();
        $name = $locale === 'ar' && $this->name_ar ? $this->name_ar : $this->name_en;

        return [
            'id' => $this->id,
            'name' => $name,
            'logo_url' => $this->logo ? asset($this->logo) : null,
            'icon' => $this->icon ?? null,
            'icon_url' => $this->icon ? asset($this->icon) : null,
        ];
    }
}
