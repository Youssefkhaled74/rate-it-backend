<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect($this->redirectTo($guard));
            }
        }

        return $next($request);
    }

    private function redirectTo(?string $guard): string
    {
        return match ($guard) {
            'admin_web' => $this->safeRoute('admin.dashboard', '/'),
            'vendor_web' => $this->safeRoute('vendor.home', '/'),
            default => '/',
        };
    }

    private function safeRoute(string $name, string $fallback): string
    {
        try {
            return route($name);
        } catch (\Throwable $e) {
            return $fallback;
        }
    }
}
