<?php

namespace App\Support\Traits\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponseTrait
{
    public function success($data = null, ?string $message = null, $meta = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ], $status);
    }

    public function error(?string $message = null, $data = null, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
            'meta' => null,
        ], $status);
    }

    public function created($data = null, ?string $message = 'Created', $meta = null): JsonResponse
    {
        return $this->success($data, $message, $meta, 201);
    }

    /**
     * noContent returns a unified JSON wrapper with HTTP 200 and no data.
     * We prefer consistent JSON responses across the API, so this returns
     * the standard wrapper (success=true) with null data and a 200 status.
     * Use created()/error()/success() for other status codes.
     */
    public function noContent(?string $message = null): JsonResponse
    {
        return $this->success(null, $message, null, 200);
    }

    public function paginated(LengthAwarePaginator $paginator, ?string $message = null): JsonResponse
    {
        $meta = [
            'page' => $paginator->currentPage(),
            'limit' => $paginator->perPage(),
            'total' => $paginator->total(),
            'has_next' => $paginator->hasMorePages(),
            'last_page' => $paginator->lastPage(),
        ];

        return $this->success($paginator->items(), $message, $meta);
    }
}
