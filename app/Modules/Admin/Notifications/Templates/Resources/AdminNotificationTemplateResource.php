<?php

namespace App\Modules\Admin\Notifications\Templates\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;

class AdminNotificationTemplateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'key' => $this->key ?? $this->type,
            'type' => $this->type,
            'title_en' => $this->title_en ?? $this->title_tpl,
            'title_ar' => $this->title_ar,
            'body_en' => $this->body_en ?? $this->body_tpl,
            'body_ar' => $this->body_ar,
            'variables_schema' => $this->variables_schema,
            'channel' => $this->channel,
            'is_active' => (bool) $this->is_active,
            'timestamps' => [
                'created_at' => new TimestampResource($this->created_at),
                'updated_at' => new TimestampResource($this->updated_at),
            ],
        ];
    }
}
