<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionSetting extends Model
{
    protected $table = 'subscription_settings';

    protected $fillable = [
        'free_trial_days',
        'is_active',
        'created_by_admin_id',
        'activated_at',
    ];

    protected $casts = [
        'free_trial_days' => 'integer',
        'is_active' => 'boolean',
        'activated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function active(): ?self
    {
        return self::query()->where('is_active', true)->orderByDesc('id')->first();
    }

    public static function getFreeTrialDays(): int
    {
        return (int) (self::active()?->free_trial_days ?? 180);
    }
}
