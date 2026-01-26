# Auth & Admin Profile Module - Setup & Deployment Guide

## ✅ Module Completed

This guide covers the complete **Auth & Admin Profile** module for the Rate It Admin Dashboard.

---

## 📋 WHAT'S INCLUDED

### Backend Code
- ✅ Admin Model with scopes and methods
- ✅ Migrations (create_admins_table, add_last_login_to_admins_table)
- ✅ Seeder (AdminSeeder with default super admin)
- ✅ Factory (AdminFactory for testing)
- ✅ 6 Controllers (Auth, Profile, Admins, Locale)
- ✅ 7 Form Requests (with validation)
- ✅ 3 Services (AdminService, ProfileService, LocaleService)
- ✅ 1 Policy (AdminPolicy for authorization)
- ✅ 2 Middleware (SetAdminLocale, EnsureAdminGuard)

### Frontend Code
- ✅ 2 Layouts (app.blade.php, auth.blade.php)
- ✅ 3 Auth pages (login, forgot-password, reset-password)
- ✅ 3 Profile pages (show, edit, password)
- ✅ 3 Admin management pages (index, create, edit)
- ✅ Full Tailwind + Alpine.js styling

### Bilingual Support
- ✅ Arabic (ar) as default
- ✅ English (en) as toggle
- ✅ RTL/LTR support
- ✅ Language switch route
- ✅ Complete translation files (ar/admin.php, en/admin.php)

### Features
- ✅ Session-based authentication with "admin" guard
- ✅ Password reset with email token
- ✅ Profile management
- ✅ Admin CRUD with authorization
- ✅ Last login tracking
- ✅ Dark/Light mode (persistent localStorage)
- ✅ Status toggle (activate/deactivate)
- ✅ Remember me on login

---

## 🚀 SETUP INSTRUCTIONS

### Step 1: Register Admin Module Service Provider

In your `config/app.php` (or bootstrap if using provider discovery):

```php
'providers' => [
    // ...
    Modules\Admin\AdminModuleServiceProvider::class,
],
```

Or if using `bootstrap/providers.php`:

```php
return [
    // ...
    Modules\Admin\AdminModuleServiceProvider::class,
];
```

### Step 2: Update `config/auth.php`

Add the admin guard and admins provider:

```php
'guards' => [
    // ...
    'admin' => [
        'driver' => 'session',
        'provider' => 'admins',
    ],
],

'providers' => [
    // ...
    'admins' => [
        'driver' => 'eloquent',
        'model' => Modules\Admin\app\Models\Admin::class,
    ],
],

'passwords' => [
    // ...
    'admins' => [
        'provider' => 'admins',
        'table' => 'password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
    ],
],
```

### Step 3: Register Middleware in `app/Http/Kernel.php`

In the `$routeMiddleware` array:

```php
protected $routeMiddleware = [
    // ...
    'admin.locale' => \Modules\Admin\app\Http\Middleware\SetAdminLocale::class,
    'admin.guard' => \Modules\Admin\app\Http\Middleware\EnsureAdminGuard::class,
];
```

### Step 4: Run Migrations

```bash
php artisan migrate
```

This will:
- Create the `admins` table with all necessary columns
- Add `last_login_at` column

### Step 5: Seed Default Super Admin

Create a `.env` section for admin defaults:

```env
ADMIN_EMAIL=admin@rateit.com
ADMIN_PASSWORD=password123
ADMIN_NAME=Super Admin
ADMIN_PHONE=
```

Run the seeder:

```bash
php artisan db:seed --class=Modules\\Admin\\database\\seeders\\AdminSeeder
```

Or add to `database/seeders/DatabaseSeeder.php`:

```php
public function run(): void
{
    $this->call([
        // ...
        \Modules\Admin\database\seeders\AdminSeeder::class,
    ]);
}
```

Then run:

```bash
php artisan db:seed
```

### Step 6: Create Routes File

The routes file is located at: `Modules/Admin/routes/web.php`

This is automatically loaded by the service provider.

### Step 7: Register Policy Authorization

The policy is automatically registered in the service provider. If you need manual registration, add to `AuthServiceProvider`:

```php
use Modules\Admin\app\Models\Admin;
use Modules\Admin\app\Policies\AdminPolicy;

protected $policies = [
    Admin::class => AdminPolicy::class,
];
```

---

## 🔌 INTEGRATION CHECKLIST

- [ ] Service provider registered in config/app.php
- [ ] Auth config updated with admin guard and provider
- [ ] Password broker added to config/auth.php
- [ ] Middleware registered in app/Http/Kernel.php
- [ ] Migrations run (php artisan migrate)
- [ ] Seeder run with admin credentials (php artisan db:seed)
- [ ] Routes loaded (automatic via service provider)
- [ ] Assets built/served via Vite
- [ ] CSS theme file accessible at public/css/admin-theme.css

---

## 🔑 AUTHENTICATION FLOW

### Login
1. User visits `/admin/login`
2. Submits credentials (email, password)
3. LoginController validates and authenticates via admin guard
4. On success: updates `last_login_at`, redirects to `/admin/dashboard`
5. Session created with `admin_locale` = 'ar' by default

### Forgot Password
1. User visits `/admin/password/forgot`
2. Enters email
3. Laravel Password broker sends reset token to email
4. User clicks link from email → `/admin/password/reset/{token}`
5. Enters new password
6. Token validated, password updated
7. Redirected to login

### Protected Routes
All authenticated routes use middleware chain:
```
auth:admin -> admin.locale -> admin.guard
```

This ensures:
- User is logged in via admin guard
- Locale is set from session (default: ar)
- User is active (checked in admin.guard middleware)

---

## 🌍 BILINGUAL SUPPORT

### Language Toggle
Click language button in topbar → `/admin/locale/{locale}`

This route:
1. Validates locale (ar/en only)
2. Stores in session: `session('admin_locale')`
3. Sets app locale: `app()->setLocale(locale)`
4. Redirects back

### RTL/LTR
Automatically handled by locale:
- `ar` → `dir="rtl"`
- `en` → `dir="ltr"`

Set in layout:
```blade
dir="{{ session('admin_locale', 'ar') === 'ar' ? 'rtl' : 'ltr' }}"
```

### Translation Files
Located in: `Modules/Admin/resources/lang/{locale}/admin.php`

Usage in views:
```blade
{{ __('admin.key_name') }}
```

---

## 🔐 AUTHORIZATION

### Policy Methods
- `viewAny()` - List all admins (super admin or has permission)
- `view()` - View single admin (super admin or own profile)
- `create()` - Create admin (super admin only)
- `update()` - Update admin (super admin and not self)
- `delete()` - Delete admin (super admin, not super, not self)
- `deactivate()` - Deactivate admin (super admin and not self)

### Usage in Controllers
```php
$this->authorize('create', Admin::class);
$this->authorize('update', $admin);
$this->authorize('deactivate', $admin);
```

### Usage in Blade
```blade
@can('create', \Modules\Admin\app\Models\Admin::class)
    <a href="{{ route('admin.admins.create') }}">{{ __('admin.add_admin') }}</a>
@endcan
```

---

## 🎨 CUSTOMIZATION

### Change Brand Color
Edit `resources/css/admin-theme.css`:

```css
:root {
    --brand: #dc2626; /* Change this red */
    /* ... other colors ... */
}
```

### Add More Admin Roles/Permissions
1. Add columns to migration if needed
2. Update Admin model with `$fillable`, `$casts`
3. Expand Policy methods
4. Update form requests validation

### Custom Validation Rules
Edit form requests in `Modules/Admin/app/Http/Requests/`

Example adding custom rule:
```php
'email' => [
    'required',
    'email',
    Rule::unique('admins', 'email')->ignore($adminId),
    new CustomEmailRule(),
],
```

---

## 📧 EMAIL CONFIGURATION

For password reset emails, ensure `MAIL_*` variables are set in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@rateit.com
MAIL_FROM_NAME="Rate It Admin"
```

Create a password notification (Laravel default works, but customize if needed):
```php
// The framework uses Illuminate\Auth\Notifications\ResetPassword
```

---

## 🧪 TESTING

### Create Test Admin
```php
// In test
$admin = Admin::factory()->create([
    'email' => 'test@example.com',
    'password' => bcrypt('password123'),
]);

$this->actingAs($admin, 'admin')
    ->get(route('admin.dashboard'))
    ->assertOk();
```

### Test Login
```php
$admin = Admin::factory()->create();

$this->post(route('admin.login'), [
    'email' => $admin->email,
    'password' => 'password123',
])->assertRedirect(route('admin.dashboard'));
```

---

## 🐛 TROUBLESHOOTING

### "Admin guard not found"
**Solution**: Check that `config/auth.php` has the admin guard and provider registered.

### "View not found"
**Solution**: Ensure service provider is registered and view namespace is `admin::`.

### "Translation not found"
**Solution**: Check `resources/lang/ar/admin.php` and `resources/lang/en/admin.php` files exist.

### "Locale not changing"
**Solution**: 
1. Verify `SetAdminLocale` middleware is applied
2. Check session is not being cleared
3. Verify route `admin.locale.switch` exists

### "Password reset email not sending"
**Solution**:
1. Check `MAIL_*` env variables
2. Verify password_reset_tokens table exists
3. Ensure email is in admins table

### "Last login not updating"
**Solution**: Check that `recordLogin()` is called after successful authentication. It's in LoginController.

---

## 📁 FILE STRUCTURE SUMMARY

```
Modules/Admin/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── ForgotPasswordController.php
│   │   │   │   └── ResetPasswordController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── AdminsController.php
│   │   │   ├── LocaleController.php
│   │   │   └── Controller.php
│   │   ├── Middleware/
│   │   │   ├── SetAdminLocale.php
│   │   │   └── EnsureAdminGuard.php
│   │   └── Requests/
│   │       ├── Auth/
│   │       │   ├── LoginRequest.php
│   │       │   ├── ForgotPasswordRequest.php
│   │       │   └── ResetPasswordRequest.php
│   │       ├── ProfileUpdateRequest.php
│   │       ├── PasswordUpdateRequest.php
│   │       ├── AdminStoreRequest.php
│   │       └── AdminUpdateRequest.php
│   ├── Models/
│   │   └── Admin.php
│   ├── Policies/
│   │   └── AdminPolicy.php
│   └── Services/
│       ├── AdminService.php
│       ├── ProfileService.php
│       └── LocaleService.php
├── database/
│   ├── migrations/
│   │   ├── create_admins_table.php
│   │   └── add_last_login_to_admins_table.php
│   ├── seeders/
│   │   └── AdminSeeder.php
│   └── factories/
│       └── AdminFactory.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   └── auth.blade.php
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   ├── forgot-password.blade.php
│   │   │   └── reset-password.blade.php
│   │   ├── profile/
│   │   │   ├── show.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── password.blade.php
│   │   └── admins/
│   │       ├── index.blade.php
│   │       ├── create.blade.php
│   │       └── edit.blade.php
│   └── lang/
│       ├── ar/
│       │   └── admin.php
│       └── en/
│           └── admin.php
├── routes/
│   └── web.php
└── AdminModuleServiceProvider.php
```

---

## ✨ QUICK START

After setup, you should have:

1. **Login Portal**: `/admin/login`
   - Email: admin@rateit.com
   - Password: password123 (or from .env)

2. **Protected Dashboard**: `/admin/dashboard`
   - Only accessible when logged in
   - Shows profile, locale toggle, theme toggle

3. **Admin Management**: `/admin/admins`
   - Create, read, update, deactivate admins
   - Super admin only

4. **Profile Pages**: 
   - View: `/admin/profile`
   - Edit: `/admin/profile/edit`
   - Password: `/admin/profile/password`

5. **Language Toggle**: Click AR/EN in topbar
   - Switches locale and direction

---

## 📞 SUPPORT

For issues, check:
1. Service provider registration
2. Migration status (`php artisan migrate:status`)
3. Config files (auth.php, app.php)
4. Middleware registration
5. Translation file keys

All code is production-ready and fully documented. Happy deploying! 🚀
