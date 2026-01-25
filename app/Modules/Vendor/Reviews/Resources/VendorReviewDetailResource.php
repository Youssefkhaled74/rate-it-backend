<?php

namespace App\Modules\Vendor\Reviews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\User\Reviews\Resources\ReviewPhotoResource;
use App\Modules\User\Reviews\Resources\ReviewAnswerResource;
use App\Support\Resources\TimestampResource;

class VendorReviewDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'overall_rating' => $this->overall_rating,
            'review_score' => $this->review_score,
            'comment' => $this->comment,
            'user' => [
                'nickname' => $this->user->nickname ?? 'Anonymous',
                'phone' => $this->user->phone ?? null,
            ],
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
            'created_at' => new TimestampResource($this->created_at),
        ];
    }
}
