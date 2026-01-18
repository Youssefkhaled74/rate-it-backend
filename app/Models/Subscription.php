<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
    protected $casts = [
        'started_at' => 'datetime',
        'free_until' => 'datetime',
        'paid_until' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
