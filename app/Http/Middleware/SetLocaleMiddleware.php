<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $lang = null;

        if ($request->query('lang')) {
            $lang = $request->query('lang');
            if ($request->hasSession()) {
                $request->session()->put('lang', $lang);
            }
        } elseif ($request->hasSession() && $request->session()->has('lang')) {
            $lang = $request->session()->get('lang');
        } elseif ($request->cookies->has('lang')) {
            $lang = $request->cookie('lang');
        } elseif ($request->headers->has('X-Lang')) {
            $lang = $request->header('X-Lang');
        } elseif ($request->headers->has('Accept-Language')) {
            $al = $request->header('Accept-Language');
            $lang = substr($al, 0, 2);
        }

        $allowed = ['en', 'ar'];
        if (!in_array($lang, $allowed, true)) {
            $lang = 'en';
        }

        app()->setLocale($lang);

        $response = $next($request);
        $response->headers->set('Content-Language', $lang);

        return $response;
    }
}
