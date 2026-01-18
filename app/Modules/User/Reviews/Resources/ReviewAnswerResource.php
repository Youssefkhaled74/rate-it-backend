<?php

namespace App\Modules\User\Reviews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewAnswerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'criteria_id' => $this->criteria_id,
            'rating_value' => $this->rating_value,
            'yes_no_value' => $this->yes_no_value,
            'choice' => $this->whenLoaded('choice', function(){ return new RatingCriteriaChoiceResource($this->choice); }),
        ];
    }
}
