<?php

namespace App\Modules\User\Profile\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProfileResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->resource;
        return [
            'id' => $user->id,
            'full_name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'avatar_url' => $user->avatar ? Storage::disk('public')->url($user->avatar) : null,
            'phone_verified' => (bool) $user->phone_verified_at,
            'phone_verified_at' => $user->phone_verified_at ? $user->phone_verified_at->toDateTimeString() : null,
            'created_at' => $user->created_at ? $user->created_at->toDateTimeString() : null,
            'created_at_human' => $user->created_at ? $user->created_at->diffForHumans() : null,
        ];
    }
}
