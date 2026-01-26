<?php

namespace Modules\Admin\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAdminLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session, default to 'ar' (Arabic)
        $locale = session('admin_locale', 'ar');

        // Validate locale is supported
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }

        // Set application locale
        app()->setLocale($locale);

        // Store in session for next request
        session(['admin_locale' => $locale]);

        return $next($request);
    }
}
