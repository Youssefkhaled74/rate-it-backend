<?php

namespace App\Modules\Admin\Catalog\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Admin\Catalog\Resources\RatingCriteriaChoiceResource;

class SubcategoryCriteriaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'question_text' => $this->question_text,
            'type' => $this->type,
            'is_required' => (bool) $this->is_required,
            'sort_order' => $this->sort_order,
            'choices' => $this->when($this->type === 'MULTIPLE_CHOICE', RatingCriteriaChoiceResource::collection($this->whenLoaded('choices') ? $this->choices : $this->choices)),
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
