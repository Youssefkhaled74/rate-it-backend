<?php

namespace App\Modules\User\Reviews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewPhotoResource extends JsonResource
{
    public function toArray($request): array
    {
        $path = $this->storage_path ?? $this->path ?? null;

        return [
            'id' => $this->id,
            'url' => $path ? asset($path) : null,
        ];
    }
}
