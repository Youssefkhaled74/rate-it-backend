<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewAnswerPhoto extends Model
{
    use HasFactory;

    protected $table = 'review_answer_photos';

    protected $fillable = [
        'review_answer_id',
        'storage_path',
        'encrypted',
    ];

    protected $casts = [
        'encrypted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function answer()
    {
        return $this->belongsTo(ReviewAnswer::class, 'review_answer_id');
    }
}
