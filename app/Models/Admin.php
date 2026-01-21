<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Model
{
    use HasApiTokens, SoftDeletes;

    protected $table = 'admins';

    protected $fillable = [
        'name', 'email', 'phone', 'password_hash', 'role', 'is_active'
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function verifyPassword(string $password): bool
    {
        return Hash::check($password, $this->password_hash);
    }
}
