<?php

namespace App\Modules\User\Notifications\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserNotificationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body ?? $this->message ?? null,
            'type' => $this->type ?? null,
            'data' => $this->data ?? null,
            'is_read' => isset($this->is_read) ? (bool) $this->is_read : ($this->read_at ? true : false),
            'read_at' => $this->read_at ? $this->read_at->toDateTimeString() : null,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'created_at_human' => $this->created_at ? $this->created_at->diffForHumans() : null,
        ];
    }
}
