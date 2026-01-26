<?php

namespace Modules\Admin\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Admin\database\factories\AdminFactory;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_super',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_super' => 'boolean',
        ];
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admins';

    /**
     * The guard name.
     *
     * @var string
     */
    protected $guard = 'admin';

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return AdminFactory::new();
    }

    /**
     * Scopes
     */

    /**
     * Scope: Filter active admins.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Filter inactive admins.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope: Filter super admins.
     */
    public function scopeSuperAdmins($query)
    {
        return $query->where('is_super', true);
    }

    /**
     * Methods
     */

    /**
     * Check if admin is super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_super;
    }

    /**
     * Check if admin is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if admin has permission (placeholder for future ACL).
     */
    public function hasPermission(string $permission): bool
    {
        // Placeholder: implement permission checking logic
        // For now, only super admins have all permissions
        return $this->is_super;
    }

    /**
     * Update last login timestamp.
     */
    public function recordLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Activate admin.
     */
    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Deactivate admin.
     */
    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);
    }
}
