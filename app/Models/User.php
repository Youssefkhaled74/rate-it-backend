<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Modules\User\Lookups\Models\Gender;
use App\Modules\User\Lookups\Models\Nationality;

/**
 * @method \Laravel\Sanctum\PersonalAccessToken|null currentAccessToken()
 * @method \Illuminate\Database\Eloquent\Relations\MorphMany tokens()
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'full_name',
        'email',
        'phone',
        'birth_date',
        'gender_id',
        'nationality_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
    ];

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality_id');
    }

    public function notifications()
    {
        return $this->hasMany(\App\Models\UserNotification::class);
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class, 'user_id');
    }

    public function city()
    {
        return $this->belongsTo(\App\Models\City::class, 'city_id');
    }

    public function area()
    {
        return $this->belongsTo(\App\Models\Area::class, 'area_id');
    }
}
