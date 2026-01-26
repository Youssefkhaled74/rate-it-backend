// Add this to config/auth.php in the 'passwords' array:

'admins' => [
    'provider' => 'admins',
    'table' => 'password_reset_tokens',
    'expire' => 60,
    'throttle' => 60,
],
