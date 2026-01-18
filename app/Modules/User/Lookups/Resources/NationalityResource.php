<?php

namespace App\Modules\User\Lookups\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NationalityResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en,
            'country_code' => strtoupper($this->country_code ?? ''),
            'flag_url' => $this->flag_url,
        ];
    }
}
