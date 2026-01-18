<?php

namespace App\Modules\User\Lookups\Models;

use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
{
    protected $table = 'nationalities';
    protected $fillable = ['iso_code','name_en','name_ar','is_active'];

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }
}
