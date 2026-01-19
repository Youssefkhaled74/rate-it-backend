<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PhoneChangeRequest extends Model
{
    protected $table = 'phone_change_requests';
    protected $guarded = ['id'];

    protected $dates = ['expires_at','verified_at','created_at','updated_at'];

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->lt(Carbon::now());
    }
}
