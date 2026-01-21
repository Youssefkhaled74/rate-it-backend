<?php

namespace App\Modules\Admin\Reviews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;

class AdminReviewListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'overall_rating' => $this->overall_rating,
            'review_score' => $this->review_score,
            'comment' => $this->comment,
            'is_hidden' => $this->is_hidden ?? false,
            'is_featured' => $this->is_featured ?? false,
            'user' => [
                'id' => $this->user->id ?? null,
                'full_name' => $this->user->full_name ?? null,
                'phone' => $this->user->phone ?? null,
            ],
            'place' => [
                'id' => $this->place->id ?? null,
                'name' => optional($this->place)->name ?? null,
                'logo_url' => optional($this->place)->logo ? asset(optional($this->place)->logo) : null,
            ],
            'branch' => [
                'id' => $this->branch->id ?? null,
                'name' => $this->branch->name ?? null,
            ],
            'timestamps' => [
                'created_at' => new TimestampResource($this->created_at),
            ],
        ];
    }
}
