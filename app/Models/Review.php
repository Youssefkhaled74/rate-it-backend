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
        'is_hidden',
        'hidden_reason',
        'hidden_at',
        'hidden_by_admin_id',
        'admin_reply_text',
        'replied_at',
        'replied_by_admin_id',
        'is_featured',
        'featured_at',
        'featured_by_admin_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'overall_rating' => 'float',
        'review_score' => 'float',
        'is_hidden' => 'boolean',
        'is_featured' => 'boolean',
        'hidden_at' => 'datetime',
        'replied_at' => 'datetime',
        'featured_at' => 'datetime',
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

    public function hiddenByAdmin()
    {
        return $this->belongsTo(Admin::class, 'hidden_by_admin_id');
    }

    public function repliedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'replied_by_admin_id');
    }

    public function featuredByAdmin()
    {
        return $this->belongsTo(Admin::class, 'featured_by_admin_id');
    }
}
