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
        'place_id',
        'name',
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

    /**
     * Relations
     */
    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

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
