<?php

namespace App\Modules\Admin\Invites\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Resources\TimestampResource;

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
            'channel' => $this->channel ?? null,
            'meta' => null,
            'rewarded_at' => $this->rewarded_at ? new TimestampResource($this->rewarded_at) : null,
            'timestamps' => [
                'sent_at' => new TimestampResource($this->created_at),
                'accepted_at' => $this->updated_at ? new TimestampResource($this->updated_at) : null,
                'created_at' => new TimestampResource($this->created_at),
                'updated_at' => $this->updated_at ? new TimestampResource($this->updated_at) : null,
            ],
        ];
    }
}
