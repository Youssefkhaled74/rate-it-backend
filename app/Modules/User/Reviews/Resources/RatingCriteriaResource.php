<?php

namespace App\Modules\User\Reviews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RatingCriteriaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'question_text' => $this->question_text,
            'type' => $this->type,
            'is_required' => (bool) $this->is_required,
            'sort_order' => $this->sort_order,
            'choices' => $this->when($this->type === 'MULTIPLE_CHOICE', RatingCriteriaChoiceResource::collection($this->choices)),
        ];
    }
}
