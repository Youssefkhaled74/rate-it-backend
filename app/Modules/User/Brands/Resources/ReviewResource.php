<?php

namespace App\Modules\User\Brands\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ReviewResource extends JsonResource
{
    public function toArray($request): array
    {
        $user = null;
        try {
            $user = $this->user ?? null;
        } catch (\Throwable $e) {
            $user = null;
        }

        $branch = $this->branch ?? null;
        $place = $this->place ?? null;

        // Normalize photos
        $photos = [];
        if ($this->relationLoaded('photos')) {
            foreach ($this->photos as $p) {
                $photos[] = [
                    'id' => $p->id,
                    'url' => isset($p->storage_path) ? asset($p->storage_path) : null,
                    'file_name' => $p->file_name ?? null,
                    'created_at' => $p->created_at ? $p->created_at->toDateTimeString() : null,
                ];
            }
        }

        // Normalize answers
        $answers = [];
        if ($this->relationLoaded('answers')) {
            foreach ($this->answers as $a) {
                $choice = null;
                if (isset($a->choice) && $a->choice) {
                    $choice = [
                        'id' => $a->choice->id,
                        'choice_text' => $a->choice->choice_text ?? null,
                        'value' => $a->choice->value ?? null,
                        'sort_order' => $a->choice->sort_order ?? null,
                    ];
                }

                $answers[] = [
                    'criteria_id' => (int) $a->criteria_id,
                    'rating_value' => $a->rating_value !== null ? (int) $a->rating_value : null,
                    'yes_no_value' => $a->yes_no_value !== null ? (bool) $a->yes_no_value : null,
                    'choice' => $choice,
                ];
            }
        }

        $createdAt = $this->created_at ? $this->created_at->copy()->setTimezone('UTC')->format('Y-m-d\TH:i:s.u\Z') : null;

        return [
            'id' => $this->id,
            'overall_rating' => $this->overall_rating !== null ? (int) $this->overall_rating : null,
            'review_score' => $this->review_score !== null ? $this->review_score : null,
            'comment' => $this->comment,
            'created_at' => $createdAt,
            'branch' => $branch ? [
                'id' => $branch->id,
                'name' => $branch->name,
                'address' => $branch->address,
            ] : null,
            'place' => $place ? [
                'id' => $place->id,
                'name' => $place->name ?? null,
                'logo_url' => isset($place->logo_path) ? asset($place->logo_path) : ($place->logo_url ?? null),
            ] : null,
            'photos' => $photos,
            'answers' => $answers,
        ];
    }
}
