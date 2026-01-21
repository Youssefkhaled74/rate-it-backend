<?php

namespace App\Modules\Admin\Catalog\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;

class RatingCriteriaChoiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'rating_criteria_id' => $this->criteria_id,
            'name_en' => $this->choice_en ?? $this->choice_text,
            'name_ar' => $this->choice_ar,
            'value' => $this->value,
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            'timestamps' => [
                'created_at' => new TimestampResource($this->created_at),
                'updated_at' => new TimestampResource($this->updated_at),
            ],
        ];
    }
}
