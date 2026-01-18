<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';
    
    protected $fillable = [
        'user_id',
        'place_id',
        'branch_id',
        'overall_rating',
        'comment',
        'status',
        'review_score',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'overall_rating' => 'float',
        'review_score' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /** Relations */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function answers()
    {
        return $this->hasMany(ReviewAnswer::class, 'review_id');
    }

    public function photos()
    {
        return $this->hasMany(ReviewPhoto::class, 'review_id');
    }
}
