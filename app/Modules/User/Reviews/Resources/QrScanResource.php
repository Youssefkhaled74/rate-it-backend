<?php

namespace App\Modules\User\Reviews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QrScanResource extends JsonResource
{
    public function toArray($request)
    {
        $now = \Carbon\Carbon::now();
        $expiresIn = null;
        if ($this->expires_at) {
            $expiresIn = $this->expires_at->diffInSeconds($now, false);
            if ($expiresIn < 0) {
                $expiresIn = 0;
            }
        }

        return [
            'id' => $this->id,
            'session_token' => $this->session_token,
            'scanned_at' => $this->scanned_at,
            'expires_at' => $this->expires_at,
            'expires_in_seconds' => $expiresIn,
        ];
    }
}
