<?php

namespace App\Modules\User\Home\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeBannerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'image_url' => $this->image_url,
            'action' => [
                'type' => $this->action_type,
                'value' => (string) ($this->action_value ?? ''),
            ],
            'sort_order' => (int) $this->sort_order,
        ];
    }
}
