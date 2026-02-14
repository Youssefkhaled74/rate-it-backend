<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Support\Exceptions\ApiException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Append global middleware
        $middleware->append(\App\Http\Middleware\SetLocaleMiddleware::class);
        // Register admin guard alias for admin routes
        $middleware->alias([
            'admin.guard' => \App\Http\Middleware\UseAdminGuard::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ApiException $e, Request $request) {
            $wantsJson = $request->expectsJson()
                || $request->is('api/*')
                || $request->is('*/api/*')
                || $request->is('v1/*')
                || str_starts_with($request->getPathInfo(), '/api');

            if (! $wantsJson) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => __($e->getMessage()),
                'data' => null,
                'meta' => $e->getMeta(),
            ], $e->getStatusCode());
        });
    })->create();
