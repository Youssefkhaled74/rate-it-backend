<?php

namespace App\Modules\User\Reviews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'overall_rating' => $this->overall_rating,
            'review_score' => $this->review_score,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'branch' => [
                'id' => $this->branch->id ?? null,
                'name' => $this->branch->name ?? null,
                'address' => $this->branch->address ?? null,
            ],
            'place' => [
                'id' => $this->place->id ?? null,
                'name' => optional($this->place)->name ?? null,
                'logo_url' => optional($this->place)->logo ? asset(optional($this->place)->logo) : null,
            ],
            'photos' => ReviewPhotoResource::collection($this->whenLoaded('photos')),
            'answers' => ReviewAnswerResource::collection($this->whenLoaded('answers')),
        ];
    }
}
