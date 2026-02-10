<?php

namespace App\Modules\User\Onboarding\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OnboardingScreenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->subtitle ?? null,
            'subtitle' => $this->subtitle ?? null,
            'sort_order' => $this->sort_order ?? null,
            'image_url' => $this->image ? asset($this->image) : null,
        ];
    }
}
