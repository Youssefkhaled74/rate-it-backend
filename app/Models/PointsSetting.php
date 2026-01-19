<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\Exceptions\ApiException;

class PointsSetting extends Model
{
    protected $table = 'points_settings';
    protected $guarded = ['id'];

    /**
     * Prevent updates or deletes via application - immutable pattern.
     */
    protected static function booted()
    {
        static::updating(function ($model) {
            throw new ApiException('points.settings_immutable', 403);
        });

        static::deleting(function ($model) {
            throw new ApiException('points.settings_immutable', 403);
        });
    }
}
