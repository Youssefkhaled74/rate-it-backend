<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name_en',
        'name_ar',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ratingCriteria()
    {
        return $this->hasMany(RatingCriteria::class, 'subcategory_id');
    }

    public function brands()
    {
        return $this->hasMany(Brand::class, 'subcategory_id');
    }

    /**
     * Determine if this subcategory is ready to be used by branches/places.
     * Rules:
     * - Must have at least one RatingCriteria
     * - MULTIPLE_CHOICE criteria must have at least 2 choices
     * - YES_NO criteria must not have custom choices
     */
    public function isReadyForUse(): bool
    {
        $criteria = $this->ratingCriteria()->with('choices')->get();
        if ($criteria->isEmpty()) return false;
        foreach ($criteria as $c) {
            $type = strtoupper($c->type ?? '');
            if ($type === 'MULTIPLE_CHOICE') {
                if ($c->choices()->count() < 2) return false;
            }
            if ($type === 'YES_NO') {
                if ($c->choices()->count() > 0) return false;
            }
        }
        return true;
    }
}
