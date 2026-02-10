<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLevel extends Model
{
    protected $table = 'user_levels';
    protected $guarded = [];

    protected $casts = [
        'benefits' => 'array',
        'bonus_percent' => 'float',
    ];
}
