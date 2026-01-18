<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewPhoto extends Model
{
    use HasFactory;

    protected $table = 'review_photos';

    protected $fillable = [
        'review_id',
        'storage_path',
        'encrypted',
    ];

    protected $casts = [
        'encrypted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id');
    }
}
