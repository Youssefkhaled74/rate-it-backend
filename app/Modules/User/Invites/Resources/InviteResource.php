<?php

namespace App\Modules\User\Invites\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InviteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'phone' => $this->invited_phone,
            'status' => $this->status,
            'reward_points' => (int) $this->reward_points,
            'invited_user' => $this->when($this->invitedUser, function(){ return ['id'=>$this->invitedUser->id,'name'=>$this->invitedUser->name] ;}),
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'rewarded_at' => optional($this->rewarded_at)->toDateTimeString(),
        ];
    }
}
