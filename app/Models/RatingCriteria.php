<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingCriteria extends Model
{
    use HasFactory;

    protected $table = 'rating_criteria';

    protected $fillable = [
        'subcategory_id',
        'question_text',
        'question_en',
        'question_ar',
        'type',
        'weight',
        'points',
        'yes_value',
        'no_value',
        'yes_weight',
        'no_weight',
        'is_required',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'sort_order' => 'integer',
        'weight' => 'float',
        'points' => 'integer',
        'yes_value' => 'integer',
        'no_value' => 'integer',
        'yes_weight' => 'float',
        'no_weight' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    public function choices()
    {
        return $this->hasMany(RatingCriteriaChoice::class, 'criteria_id');
    }

    public function reviewAnswers()
    {
        return $this->hasMany(ReviewAnswer::class, 'criteria_id');
    }
}
