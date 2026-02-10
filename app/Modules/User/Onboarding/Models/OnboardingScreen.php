<?php

namespace App\Modules\User\Onboarding\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingScreen extends Model
{
    protected $table = 'onboarding_screens';

    protected $fillable = [
        'title',
        'body',
        'sort_order',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public $timestamps = true;
}
