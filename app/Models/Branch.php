<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branches';
    
    /**
     * Mass-assignable attributes for the branches table.
     * Columns derived from database/migrations/2026_01_17_000060_create_branches_table.php
     */
    protected $fillable = [
        'name_en',
        'name_ar',
        'name',
        'logo',
        'cover_image',
        'address',
        'lat',
        'lng',
        'working_hours',
        'qr_code_value',
        'qr_generated_at',
        'review_cooldown_days',
        'is_active',
        'city_id',
        'area_id',
        'brand_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'working_hours' => 'array',
        'qr_generated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'lat' => 'float',
        'lng' => 'float',
        'review_cooldown_days' => 'integer',
        'is_active' => 'boolean',
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

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = $value;
        if (empty($this->attributes['name_en']) && !empty($value)) {
            $this->attributes['name_en'] = $value;
        }
    }

    public function setNameEnAttribute($value): void
    {
        $this->attributes['name_en'] = $value;
        if (empty($this->attributes['name']) && !empty($value)) {
            $this->attributes['name'] = $value;
        }
    }

    /**
     * Accessors for media URLs with fallbacks.
     */
    private function resolveMediaUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return '/' . ltrim($path, '/');
    }

    public function getLogoUrlAttribute(): ?string
    {
        $path = $this->logo
            ?: $this->brand?->logo;

        return $this->resolveMediaUrl($path);
    }

    public function getCoverUrlAttribute(): ?string
    {
        $path = $this->cover_image
            ?: $this->brand?->cover_image;

        return $this->resolveMediaUrl($path);
    }

    /**
     * Relations
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'branch_id');
    }

    public function vendorUsers()
    {
        return $this->hasMany(VendorUser::class, 'branch_id');
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'used_branch_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
}
