<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subcategory;

class Place extends Model
{
    use HasFactory;

    protected $table = 'places';
    
    protected $guarded = [];
    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * Unified display name for the place.
     * Tries locale-specific columns then sensible fallbacks.
     */
    public function getDisplayNameAttribute(): string
    {
        $locale = app()->getLocale();

        $candidates = [];
        // locale-specific preferred columns
        if ($locale === 'ar') {
            $candidates = array_merge($candidates, ['name_ar', 'title_ar']);
        } else {
            $candidates = array_merge($candidates, ['name_en', 'title_en']);
        }

        // common fallbacks
        $candidates = array_merge($candidates, ['name', 'title']);

        foreach ($candidates as $col) {
            if (array_key_exists($col, $this->attributes) && ! empty($this->attributes[$col])) {
                return (string) $this->attributes[$col];
            }
        }

        // last resort: first non-empty attribute
        foreach ($this->attributes as $val) {
            if (! empty($val) && is_string($val)) return $val;
        }

        return '-';
    }
}
