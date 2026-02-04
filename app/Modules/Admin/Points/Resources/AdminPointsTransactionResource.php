<?php

namespace App\Modules\Admin\Points\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;

class AdminPointsTransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => $this->whenLoaded('user', function(){ return ['id' => $this->user->id ?? null, 'name' => $this->user->name ?? null, 'phone' => $this->user->phone ?? null]; }),
            'type' => $this->type,
            'points' => (int) $this->points,
            'meta' => $this->meta ?? null,
            'expires_at' => $this->expires_at ? new TimestampResource($this->expires_at) : null,
            'timestamps' => [
                'created_at' => new TimestampResource($this->created_at),
            ],
        ];
    }
}
