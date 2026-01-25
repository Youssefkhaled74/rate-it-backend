<?php

namespace App\Modules\Vendor\Reviews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;

class VendorReviewListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'overall_rating' => $this->overall_rating,
            'review_score' => $this->review_score,
            'comment' => $this->comment,
            'photos_count' => $this->photos_count ?? 0,
            'user' => [
                'nickname' => $this->user->nickname ?? 'Anonymous',
                'phone' => $this->user->phone ?? null,
            ],
            'branch' => [
                'id' => $this->branch->id ?? null,
                'name' => $this->branch->name ?? null,
            ],
            'place' => [
                'id' => $this->place->id ?? null,
                'name' => optional($this->place)->name ?? null,
            ],
            'created_at' => new TimestampResource($this->created_at),
        ];
    }
}
