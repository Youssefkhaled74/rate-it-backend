<?php

namespace App\Modules\User\Lookups\Models;

use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
{
    protected $table = 'nationalities';
    protected $fillable = ['country_code', 'flag_style', 'flag_size', 'name_en', 'name_ar', 'is_active'];

    protected $casts = [
        'flag_size' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getFlagUrlAttribute(): string
    {
        $code = strtoupper($this->country_code ?? '');
        $style = $this->flag_style ?? 'shiny';
        $size = $this->flag_size ?? 64;

        return "https://flagsapi.com/{$code}/{$style}/{$size}.png";
    }

    public function setCountryCodeAttribute($value)
    {
        $this->attributes['country_code'] = $value ? strtoupper($value) : null;
    }
}
