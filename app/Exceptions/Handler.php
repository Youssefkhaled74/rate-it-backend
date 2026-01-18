<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\JsonResponse;
use App\Support\Exceptions\ApiException;

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

        return parent::render($request, $e);
    }
}
