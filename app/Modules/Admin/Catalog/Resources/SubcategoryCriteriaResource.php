<?php

namespace App\Modules\Admin\Catalog\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Admin\Catalog\Resources\RatingCriteriaChoiceResource;
use App\Support\Resources\TimestampResource;

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
            'timestamps' => [
                'created_at' => new TimestampResource($this->created_at),
                'updated_at' => new TimestampResource($this->updated_at),
            ],
        ];
    }
}
