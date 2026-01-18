<?php

namespace App\Modules\User\Lookups\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GenderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code ?? null,
            'name' => app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en,
        ];
    }
}
