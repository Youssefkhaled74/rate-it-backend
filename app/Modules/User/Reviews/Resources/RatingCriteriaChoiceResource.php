<?php

namespace App\Modules\User\Reviews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RatingCriteriaChoiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'choice_text' => $this->choice_text,
            'value' => $this->value,
            'sort_order' => $this->sort_order,
        ];
    }
}
