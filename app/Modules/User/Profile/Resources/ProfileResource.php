<?php

namespace App\Modules\User\Profile\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProfileResource extends JsonResource
{
    public function toArray($request): array
    {
        $user = $this->resource;

        return [
            'id' => $user->id,
            // prefer `full_name` if available, fallback to `name`
            'full_name' => $user->full_name ?? $user->name ?? null,
            'email' => $user->email ?? null,
            'phone' => $user->phone ?? null,
            'avatar_url' => ($user->avatar ?? null) ? Storage::disk('public')->url($user->avatar) : null,
            'phone_verified' => !is_null($user->phone_verified_at),
            'phone_verified_at' => $user->phone_verified_at ? $user->phone_verified_at->toDateTimeString() : null,
            'created_at' => $user->created_at ? $user->created_at->toDateTimeString() : null,
            'created_at_human' => $user->created_at ? $user->created_at->diffForHumans() : null,
        ];
    }
}
