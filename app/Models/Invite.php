<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Invite extends Model
{
    protected $table = 'invites';
    protected $guarded = ['id'];

    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_user_id');
    }

    public function invitedUser()
    {
        return $this->belongsTo(User::class, 'invited_user_id');
    }
}
