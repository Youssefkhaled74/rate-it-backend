// Add this guard and provider to config/auth.php in the 'guards' and 'providers' arrays:

// In 'guards' array:
'admin' => [
    'driver' => 'session',
    'provider' => 'admins',
],

// In 'providers' array:
'admins' => [
    'driver' => 'eloquent',
    'model' => Modules\Admin\app\Models\Admin::class,
],
