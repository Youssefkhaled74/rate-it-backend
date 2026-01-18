<?php

namespace App\Modules\User\Auth\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name ?? $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'birth_date' => $this->birth_date,
            'gender' => $this->when($this->gender, function () use ($request) {
                return [
                    'id' => $this->gender->id,
                    'name' => app()->getLocale() === 'ar' ? $this->gender->name_ar : $this->gender->name_en,
                ];
            }),
            'nationality' => $this->when($this->nationality, function () use ($request) {
                return [
                    'id' => $this->nationality->id,
                    'name' => app()->getLocale() === 'ar' ? $this->nationality->name_ar : $this->nationality->name_en,
                ];
            }),
            'governorate' => $this->governorate,
            'area' => $this->area,
            'created_at' => $this->created_at,
        ];
    }
}
