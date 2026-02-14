<?php

namespace App\Modules\User\Auth\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\User\Lookups\Resources\GenderResource;
use App\Modules\User\Lookups\Resources\NationalityResource;
use App\Modules\User\Reviews\Resources\ReviewResource as UserReviewResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'birth_date' => $this->birth_date ? $this->birth_date->toDateString() : null,
            'gender' => $this->when($this->gender, function () use ($request) {
                return new GenderResource($this->gender);
            }),
            'nationality' => $this->when($this->nationality, function () use ($request) {
                return new NationalityResource($this->nationality);
            }),
            'city' => $this->when($this->city, function () {
                return [
                    'id' => $this->city->id,
                    'name_en' => $this->city->name_en ?? null,
                    'name_ar' => $this->city->name_ar ?? null,
                    'name' => app()->getLocale() === 'ar'
                        ? ($this->city->name_ar ?? $this->city->name_en)
                        : ($this->city->name_en ?? $this->city->name_ar),
                ];
            }),
            'area' => $this->when($this->area, function () {
                return [
                    'id' => $this->area->id,
                    'city_id' => $this->area->city_id,
                    'name_en' => $this->area->name_en ?? null,
                    'name_ar' => $this->area->name_ar ?? null,
                    'name' => app()->getLocale() === 'ar'
                        ? ($this->area->name_ar ?? $this->area->name_en)
                        : ($this->area->name_en ?? $this->area->name_ar),
                    'city' => $this->when($this->area->city, function () {
                        return [
                            'id' => $this->area->city->id,
                            'name_en' => $this->area->city->name_en ?? null,
                            'name_ar' => $this->area->city->name_ar ?? null,
                        ];
                    }),
                ];
            }),
            'notifications_count' => $this->whenLoaded('notifications', fn() => $this->notifications->count()),
            'notifications' => $this->whenLoaded('notifications', function () {
                return $this->notifications->map(function ($n) {
                    return [
                        'id' => $n->id,
                        'title' => $n->title ?? null,
                        'body' => $n->body ?? null,
                        'data' => $n->data ?? [],
                        'is_read' => isset($n->is_read) ? (bool) $n->is_read : null,
                        'sent_at' => $n->sent_at?->toDateTimeString(),
                        'created_at' => $n->created_at?->toDateTimeString(),
                    ];
                })->values();
            }),
            'reviews_count' => $this->whenLoaded('reviews', fn() => $this->reviews->count()),
            'reviews' => $this->whenLoaded('reviews', fn() => UserReviewResource::collection($this->reviews)),
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'created_at_human' => $this->created_at ? $this->created_at->diffForHumans() : null,
            'is_phone_verified' => ! is_null($this->phone_verified_at),
            'phone_verified_at' => $this->phone_verified_at ? $this->phone_verified_at->toDateTimeString() : null,
        ];
    }
}
