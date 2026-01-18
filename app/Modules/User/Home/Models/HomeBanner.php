<?php

namespace App\Modules\User\Home\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HomeBanner extends Model
{
    use HasFactory;

    protected $table = 'home_banners';

    protected $fillable = [
        'title_en', 'title_ar', 'body_en', 'body_ar', 'image', 'action_type', 'action_value', 'sort_order', 'starts_at', 'ends_at', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function getImageUrlAttribute(): string
    {
        return asset($this->image);
    }

    public function getTitleAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getBodyAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? $this->body_ar : $this->body_en;
    }

    public function scopeActiveNow($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc');
    }
}
