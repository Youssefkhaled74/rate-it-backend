<?php

namespace App\Modules\Admin\Catalog\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;
use App\Modules\User\Reviews\Resources\ReviewAnswerResource;

class RatingCriteriaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name_en' => $this->question_text,
            'name_ar' => null,
            'type' => $this->type,
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            'is_required' => (bool) $this->is_required,
            'subcategory' => $this->whenLoaded('subcategory', function() {
                return new SubcategoryResource($this->subcategory);
            }),
            'choices' => $this->whenLoaded('choices', function() {
                return RatingCriteriaChoiceResource::collection($this->choices);
            }),
            'review_answers' => $this->whenLoaded('reviewAnswers', function() {
                return ReviewAnswerResource::collection($this->reviewAnswers);
            }),
            'timestamps' => [
                'created_at' => new TimestampResource($this->created_at),
                'updated_at' => new TimestampResource($this->updated_at),
            ],
        ];
    }
}
