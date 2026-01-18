<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\JsonResponse;
use App\Support\Exceptions\ApiException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        // Keep default registration
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof ApiException) {
            $status = $e->getStatus() ?: 422;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
                'meta' => null,
            ], $status);
        }

        if ($e instanceof ValidationException) {
            $errors = $e->errors();
            return response()->json([
                'success' => false,
                'message' => __('api.errors.validation'),
                'data' => $errors,
                'meta' => null,
            ], 422);
        }

        if ($e instanceof QueryException) {
            Log::error('Database error: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('api.errors.database'),
                'data' => null,
                'meta' => null,
            ], 400);
        }

        return parent::render($request, $e);
    }
}
