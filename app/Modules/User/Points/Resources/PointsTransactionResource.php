<?php

namespace App\Modules\User\Points\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PointsTransactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'points' => (int) $this->points,
            'title' => $this->meta['title'] ?? ($this->source_type ? ucfirst($this->source_type) : null),
            'meta' => $this->meta ?? null,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'created_at_human' => $this->created_at ? $this->created_at->diffForHumans() : null,
            'expires_at' => $this->expires_at ? $this->expires_at->toDateTimeString() : null,
        ];
    }
}
