<?php

namespace App\Modules\Admin\Points\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminPointsTransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => $this->whenLoaded('user', function(){ return ['id' => $this->user->id ?? null, 'full_name' => $this->user->full_name ?? null, 'phone' => $this->user->phone ?? null]; }),
            'type' => $this->type,
            'points' => (int) $this->points,
            'meta' => $this->meta ?? null,
            'expires_at' => $this->expires_at ?? null,
            'created_at' => $this->created_at,
        ];
    }
}
