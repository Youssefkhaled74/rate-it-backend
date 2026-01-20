<?php

namespace App\Modules\Admin\Invites\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminInviteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'inviter' => $this->inviter ? ['id'=>$this->inviter->id,'full_name'=>$this->inviter->full_name ?? $this->inviter->name,'phone'=>$this->inviter->phone] : null,
            'invitee_phone' => $this->invited_phone,
            'invitee_user' => $this->invitedUser ? ['id'=>$this->invitedUser->id,'full_name'=>$this->invitedUser->full_name ?? $this->invitedUser->name,'phone'=>$this->invitedUser->phone] : null,
            'status' => $this->status,
            'reward_points' => $this->reward_points,
            'sent_at' => $this->created_at,
            'accepted_at' => $this->updated_at ?? null,
            'registered_at' => $this->created_at ?? null,
            'rewarded_at' => $this->rewarded_at,
            'channel' => $this->channel ?? null,
            'meta' => null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
