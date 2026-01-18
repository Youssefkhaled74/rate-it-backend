<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchQrSession extends Model
{
    use HasFactory;

    protected $table = 'branch_qr_sessions';

    protected $fillable = [
        'user_id',
        'branch_id',
        'qr_code_value',
        'session_token',
        'scanned_at',
        'expires_at',
        'consumed_at',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
