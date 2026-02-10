<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingCriteriaChoice extends Model
{
    use HasFactory;

    protected $table = 'rating_criteria_choices';

    protected $fillable = [
        'criteria_id',
        'choice_text',
        'choice_en',
        'choice_ar',
        'value',
        'weight',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'value' => 'integer',
        'weight' => 'float',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function criteria()
    {
        return $this->belongsTo(RatingCriteria::class, 'criteria_id');
    }

    public function reviewAnswers()
    {
        return $this->hasMany(ReviewAnswer::class, 'choice_id');
    }
}
