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
            'text_value' => $this->text_value,
            'choice' => $this->whenLoaded('choice', function(){ return new RatingCriteriaChoiceResource($this->choice); }),
            'photos' => $this->whenLoaded('photos', function () {
                return $this->photos->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'url' => asset($p->storage_path),
                    ];
                });
            }),
        ];
    }
}
