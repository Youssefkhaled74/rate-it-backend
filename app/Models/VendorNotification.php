<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorNotification extends Model
{
    use HasFactory;

    protected $table = 'vendor_notifications';
    
    protected $guarded = [];
    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
