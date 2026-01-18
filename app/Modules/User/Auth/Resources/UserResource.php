<?php

namespace App\Modules\User\Auth\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\User\Lookups\Resources\GenderResource;
use App\Modules\User\Lookups\Resources\NationalityResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name ?? $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'birth_date' => $this->birth_date ? $this->birth_date->toDateString() : null,
            'gender' => $this->when($this->gender, function () use ($request) {
                return new GenderResource($this->gender);
            }),
            'nationality' => $this->when($this->nationality, function () use ($request) {
                return new NationalityResource($this->nationality);
            }),
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'created_at_human' => $this->created_at ? $this->created_at->diffForHumans() : null,
            'is_phone_verified' => ! is_null($this->phone_verified_at),
            'phone_verified_at' => $this->phone_verified_at ? $this->phone_verified_at->toDateTimeString() : null,
        ];
    }
}
