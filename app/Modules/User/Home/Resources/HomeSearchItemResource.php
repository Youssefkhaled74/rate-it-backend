<?php

namespace App\Modules\User\Home\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeSearchItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => $this['type'] ?? null,
            'id' => $this['id'] ?? null,
            'name' => $this['name'] ?? null,
            'logo_url' => $this['logo_url'] ?? null,
            'meta' => $this['meta'] ?? [],
        ];
    }
}
