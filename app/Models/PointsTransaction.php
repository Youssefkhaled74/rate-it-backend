<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointsTransaction extends Model
{
    use HasFactory;

    protected $table = 'points_transactions';
    
    protected $guarded = [];
    protected $casts = [
        'meta' => 'array',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The user who owns this points transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The related brand (optional)
     */
    public function brand()
    {
        return $this->belongsTo(\App\Models\Brand::class);
    }

    /**
     * Polymorphic reference to the related model (e.g. Review, Voucher)
     * Uses `reference_type` and `reference_id` columns.
     */
    public function reference()
    {
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    }

    /**
     * Scope: only not-yet-expired transactions
     */
    public function scopeUnexpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Helper: is this transaction expired?
     */
    public function isExpired(): bool
    {
        return $this->expires_at ? $this->expires_at->isPast() : false;
    }
}
