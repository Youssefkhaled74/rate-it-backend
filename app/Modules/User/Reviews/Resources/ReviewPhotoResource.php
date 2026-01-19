<?php

namespace App\Modules\User\Reviews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewPhotoResource extends JsonResource
{
    public function toArray($request): array
    {
        $path = $this->storage_path ?? $this->path ?? null;
        $fileName = null;
        if ($path) {
            $fileName = basename($path);
        }

        return [
            'id' => $this->id,
            'url' => $path ? asset($path) : null,
            'file_name' => $fileName,
            'created_at' => $this->created_at?->toDateTimeString(),
            'created_at_human' => $this->created_at?->diffForHumans(),
        ];
    }
}
