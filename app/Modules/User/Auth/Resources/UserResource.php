<?php

namespace App\Modules\User\Auth\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'gender' => $this->gender,
            'nationality' => $this->nationality,
            'birth_date' => $this->birth_date,
            'governorate' => $this->governorate,
            'area' => $this->area,
            'created_at' => $this->created_at,
        ];
    }
}
