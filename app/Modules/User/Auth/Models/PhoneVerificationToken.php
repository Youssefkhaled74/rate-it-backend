<?php

namespace App\Modules\User\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneVerificationToken extends Model
{
    protected $table = 'phone_verification_tokens';

    protected $fillable = [
        'phone', 'otp_hash', 'expires_at', 'consumed_at', 'attempt_count'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];
}
