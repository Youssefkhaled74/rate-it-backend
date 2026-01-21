<?php

namespace App\Modules\Admin\Reviews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\User\Reviews\Resources\ReviewPhotoResource;
use App\Modules\User\Reviews\Resources\ReviewAnswerResource;
use App\Support\Resources\TimestampResource;

class AdminReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'overall_rating' => $this->overall_rating,
            'review_score' => $this->review_score,
            'comment' => $this->comment,

            // moderation fields
            'is_hidden' => $this->is_hidden ?? false,
            'hidden_reason' => $this->hidden_reason ?? null,
            'hidden_at' => $this->hidden_at ? new TimestampResource($this->hidden_at) : null,

            'is_featured' => $this->is_featured ?? false,
            'featured_at' => $this->featured_at ? new TimestampResource($this->featured_at) : null,

            'admin_reply_text' => $this->admin_reply_text ?? null,
            'replied_at' => $this->replied_at ? new TimestampResource($this->replied_at) : null,

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

            'photos' => ReviewPhotoResource::collection($this->whenLoaded('photos')),
            'answers' => ReviewAnswerResource::collection($this->whenLoaded('answers')),
            'timestamps' => [
                'created_at' => new TimestampResource($this->created_at),
            ],
        ];
    }
}
