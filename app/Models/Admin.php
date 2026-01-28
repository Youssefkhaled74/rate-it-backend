<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class Admin extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password_hash',
        'photo_path',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password_hash',
    ];

    // Return public URL for admin photo or null
    public function getPhotoUrlAttribute(): ?string
    {
        if (empty($this->photo_path)) {
            return null;
        }

        // If the file exists in the storage 'public' disk (old behavior), prefer direct public file if available
        try {
            if (Storage::disk('public')->exists($this->photo_path)) {
                // if public symlink exists and public file is reachable, return its storage URL
                $publicFile = public_path($this->photo_path);
                if (file_exists($publicFile)) {
                    return asset($this->photo_path);
                }

                // otherwise return a proxy URL that serves from the storage disk
                return route('storage.proxy', ['path' => $this->photo_path]);
            }
        } catch (\Throwable $e) {
            // ignore and try public path
        }

        // If the file was stored directly under public/, return asset() URL
        $publicPath = public_path($this->photo_path);
        if (file_exists($publicPath)) {
            return asset($this->photo_path);
        }

        return null;
    }

    /**
     * 🔑 أهم حتة
     * نعرّف Laravel إن ده هو الباسورد
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function verifyPassword(string $password): bool
    {
        return Hash::check($password, $this->password_hash);
    }

    // Roles / Permissions (تمام زي ما هي)
    public function roles()
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles');
    }

    public function permissions()
    {
        return $this->roles()
            ->with('permissions')
            ->get()
            ->flatMap(fn ($role) => $role->permissions)
            ->unique('id');
    }
}
