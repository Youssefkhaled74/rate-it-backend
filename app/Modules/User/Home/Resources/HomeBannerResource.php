<?php

namespace App\Modules\User\Home\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeBannerResource extends JsonResource
{
    public function toArray($request): array
    {
        $imageUrl = $this->image_url ?? ($this->image ? asset($this->image) : null);
        $brandResource = null;

        if (($this->action_type ?? null) === 'brand' && $this->relationLoaded('brand') && $this->brand) {
            $brandResource = new HomeBannerBrandResource($this->brand);
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'image_url' => $imageUrl,
            'action' => [
                'type' => $this->action_type,
                'value' => (string) ($this->action_value ?? ''),
            ],
            'brand' => $brandResource,
            'sort_order' => (int) $this->sort_order,
        ];
    }
}
