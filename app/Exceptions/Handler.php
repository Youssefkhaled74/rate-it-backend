<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\JsonResponse;
use App\Support\Exceptions\ApiException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        // Keep default registration
    }

    public function render($request, Throwable $e)
    {
        $wantsJson = $request->expectsJson() || str_starts_with($request->getPathInfo(), '/api');

        if (! $wantsJson) {
            return parent::render($request, $e);
        }

        // ApiException
        if ($e instanceof ApiException) {
            $status = $e->getStatus() ?: 422;
            $message = __($e->getMessage());
            $data = $e->getData() ?? null;
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => $data,
                'meta' => null,
            ], $status);
        }

        // Validation errors
        if ($e instanceof ValidationException) {
            $errors = $e->errors();
            return response()->json([
                'success' => false,
                'message' => __('validation.failed'),
                'data' => $errors,
                'meta' => null,
            ], 422);
        }

        // Authentication
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'success' => false,
                'message' => __('auth.unauthenticated'),
                'data' => null,
                'meta' => null,
            ], 401);
        }

        // Authorization
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'success' => false,
                'message' => __('auth.forbidden'),
                'data' => null,
                'meta' => null,
            ], 403);
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'success' => false,
                'message' => __('route.not_found'),
                'data' => null,
                'meta' => null,
            ], 404);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'success' => false,
                'message' => __('route.method_not_allowed'),
                'data' => null,
                'meta' => null,
            ], 405);
        }

        if ($e instanceof QueryException) {
            Log::error('Database error: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('server.error'),
                'data' => null,
                'meta' => null,
            ], 500);
        }

        // Generic exceptions: do not leak trace in production
        Log::error('Unhandled exception: '.$e->getMessage(), ['exception' => $e]);
        $message = app()->environment('production') ? __('server.error') : $e->getMessage();

        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
        } else {
            $status = 500;
        }

        return response()->json([
            'success' => false,
            'message' => __($message),
            'data' => null,
            'meta' => null,
        ], $status);
    }
}
