// Add to app/Http/Kernel.php in the 'routeMiddleware' array:

'admin.locale' => \Modules\Admin\app\Http\Middleware\SetAdminLocale::class,
'admin.guard' => \Modules\Admin\app\Http\Middleware\EnsureAdminGuard::class,
