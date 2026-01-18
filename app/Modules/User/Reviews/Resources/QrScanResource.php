<?php

namespace App\Modules\User\Reviews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QrScanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'session_token' => $this->session_token,
            'scanned_at' => $this->scanned_at,
            'expires_at' => $this->expires_at,
        ];
    }
}
