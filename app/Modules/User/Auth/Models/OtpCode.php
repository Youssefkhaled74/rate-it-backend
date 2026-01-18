<?php

namespace App\Modules\User\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $table = 'otp_codes';

    protected $fillable = [
        'phone','purpose','code_hash','expires_at','attempts','last_sent_at','verified_at','meta'
    ];

    protected $casts = [
        'meta' => 'array',
        'expires_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'verified_at' => 'datetime',
    ];
}
