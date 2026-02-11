<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';
    
    protected $guarded = [];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getDisplayNameAttribute(): ?string
    {
        $locale = app()->getLocale();
        $legacy = $this->attributes['name'] ?? null;
        $en = $this->attributes['name_en'] ?? null;
        $ar = $this->attributes['name_ar'] ?? null;

        if ($locale === 'ar') {
            return $ar ?: $en ?: $legacy;
        }

        return $en ?: $legacy ?: $ar;
    }

    public function getNameAttribute($value): ?string
    {
        $locale = app()->getLocale();
        $en = $this->attributes['name_en'] ?? null;
        $ar = $this->attributes['name_ar'] ?? null;

        if ($locale === 'ar') {
            return $ar ?: $en ?: $value;
        }

        return $en ?: $value ?: $ar;
    }

    public function scopeActiveForUser($query)
    {
        $today = now()->toDateString();
        return $query->where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhereDate('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
            });
    }

    public function places()
    {
        return $this->hasMany(Place::class, 'brand_id');
    }

    public function branches()
    {
        return $this->hasMany(Branch::class, 'brand_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }
}
