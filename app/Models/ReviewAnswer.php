<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewAnswer extends Model
{
    use HasFactory;
    protected $table = 'review_answers';

    protected $fillable = [
        'review_id',
        'criteria_id',
        'rating_value',
        'yes_no_value',
        'choice_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'rating_value' => 'integer',
        'yes_no_value' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id');
    }

    public function criteria()
    {
        return $this->belongsTo(RatingCriteria::class, 'criteria_id');
    }

    public function choice()
    {
        return $this->belongsTo(RatingCriteriaChoice::class, 'choice_id');
    }
}
