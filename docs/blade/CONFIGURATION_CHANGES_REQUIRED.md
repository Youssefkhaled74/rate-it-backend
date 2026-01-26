# Configuration Changes Required

This document lists EXACTLY what needs to be changed in existing config files.

---

## 📝 FILE: `config/auth.php`

### Add to `'guards'` array:

```php
'admin' => [
    'driver' => 'session',
    'provider' => 'admins',
],
```

### Add to `'providers'` array:

```php
'admins' => [
    'driver' => 'eloquent',
    'model' => Modules\Admin\app\Models\Admin::class,
],
```

### Add to `'passwords'` array:

```php
'admins' => [
    'provider' => 'admins',
    'table' => 'password_reset_tokens',
    'expire' => 60,
    'throttle' => 60,
],
```

### Complete Example Section:

```php
<?php

return [
    // ... existing config ...

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        
        // ADD THIS:
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        
        // ADD THIS:
        'admins' => [
            'driver' => 'eloquent',
            'model' => Modules\Admin\app\Models\Admin::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        
        // ADD THIS:
        'admins' => [
            'provider' => 'admins',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    // ... rest of config ...
];
```

---

## 📝 FILE: `app/Http/Kernel.php`

### Add to `$routeMiddleware` array:

```php
'admin.locale' => \Modules\Admin\app\Http\Middleware\SetAdminLocale::class,
'admin.guard' => \Modules\Admin\app\Http\Middleware\EnsureAdminGuard::class,
```

### Complete Example:

```php
<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ... existing middleware ...

    protected $routeMiddleware = [
        // ... existing middleware ...
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        
        // ADD THESE TWO LINES:
        'admin.locale' => \Modules\Admin\app\Http\Middleware\SetAdminLocale::class,
        'admin.guard' => \Modules\Admin\app\Http\Middleware\EnsureAdminGuard::class,
    ];
}
```

---

## 📝 FILE: `config/app.php`

### Register Service Provider:

In the `'providers'` array, add:

```php
Modules\Admin\AdminModuleServiceProvider::class,
```

### Complete Example:

```php
<?php

return [
    // ... existing config ...

    'providers' => [
        // ... other providers ...
        
        // Add this line:
        Modules\Admin\AdminModuleServiceProvider::class,
        
        // ... rest of providers ...
    ],
];
```

---

## 📝 FILE: `.env` (Optional but Recommended)

Add for admin seeding:

```env
ADMIN_EMAIL=admin@rateit.com
ADMIN_PASSWORD=YourSecurePassword123!
ADMIN_NAME=Super Admin
ADMIN_PHONE=
```

Change these values in production!

---

## 📝 FILE: `.env` (Mail Configuration)

For password reset emails to work, ensure you have:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@rateit.com
MAIL_FROM_NAME="Rate It Admin"
```

---

## 🔍 VERIFICATION CHECKLIST

After making changes:

- [ ] `config/auth.php` has admin guard
- [ ] `config/auth.php` has admins provider
- [ ] `config/auth.php` has admins password broker
- [ ] `app/Http/Kernel.php` has admin.locale middleware
- [ ] `app/Http/Kernel.php` has admin.guard middleware
- [ ] `config/app.php` has AdminModuleServiceProvider
- [ ] `.env` has ADMIN_EMAIL, ADMIN_PASSWORD
- [ ] `.env` has MAIL_* configuration

---

## ✅ THEN RUN:

```bash
# Clear config cache (important!)
php artisan config:clear

# Run migrations
php artisan migrate

# Seed admin
php artisan db:seed --class=Modules\\Admin\\database\\seeders\\AdminSeeder
```

---

## 🧪 TEST:

```bash
# Visit login page
http://localhost/admin/login

# Login with
Email: admin@rateit.com (or your ADMIN_EMAIL)
Password: (your ADMIN_PASSWORD from .env)
```

---

## ⚠️ IMPORTANT

1. **Do NOT modify** module files directly - they're complete
2. **DO modify** only the config files listed above
3. **DO backup** .env before making changes
4. **DO test** after migrations
5. **DO change** admin password in production

---

Done! That's all the configuration needed. 🎉
