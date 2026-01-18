<?php

namespace App\Modules\User\Brands\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request): array
    {
        $user = null;
        try {
            $user = $this->user ?? null;
        } catch (\Throwable $e) {
            $user = null;
        }

        return [
            'id' => $this->id,
            'user' => $user ? ['id' => $user->id, 'name' => $user->name] : null,
            'rating' => $this->overall_rating,
            'comment' => $this->comment,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
        ];
    }
}
