<?php

namespace App\Modules\User\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens';

    protected $fillable = [
        'phone','token_hash','expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
