<?php

namespace App\Modules\User\Categories\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategorySearchItemResource extends JsonResource
{
    public function toArray($request): array
    {
        if ($this->resource['type'] === 'category') {
            return [
                'type' => 'category',
                'id' => $this->resource['id'],
                'name' => $this->resource['name'],
                'logo_url' => $this->resource['logo'] ? asset($this->resource['logo']) : null,
            ];
        }

        return [
            'type' => 'subcategory',
            'id' => $this->resource['id'],
            'category_id' => $this->resource['category_id'],
            'name' => $this->resource['name'],
            'image_url' => $this->resource['image'] ? asset($this->resource['image']) : null,
        ];
    }
}
