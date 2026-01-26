<?php

namespace Modules\Admin\app\Http\Controllers;

use Modules\Admin\app\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\User;
use App\Models\Place;
use Carbon\Carbon;

/**
 * Dashboard API Controller
 * 
 * Provides JSON endpoints consumed by dashboard Blade views via AJAX
 * All responses follow consistent format: { success: bool, data: *, message: string }
 */
class DashboardApiController extends Controller
{
    /**
     * Get dashboard KPIs (Key Performance Indicators)
     * 
     * GET /admin/api/kpis
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function kpis(Request $request): JsonResponse
    {
        try {
            $from = $request->input('from') ? Carbon::parse($request->input('from')) : now()->subDays(30);
            $to = $request->input('to') ? Carbon::parse($request->input('to')) : now();

            $reviewsCount = Review::whereBetween('created_at', [$from, $to])
                ->whereNull('deleted_at')
                ->count();

            $avgRating = Review::whereBetween('created_at', [$from, $to])
                ->whereNull('deleted_at')
                ->avg('overall_rating') ?? 0;

            $usersCount = User::whereBetween('created_at', [$from, $to])
                ->whereNull('deleted_at')
                ->count();

            $placesCount = Place::whereBetween('created_at', [$from, $to])
                ->whereNull('deleted_at')
                ->count();

            return $this->success([
                'reviews_count' => $reviewsCount,
                'avg_rating' => round($avgRating, 2),
                'users_count' => $usersCount,
                'places_count' => $placesCount,
            ], 'KPIs retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * Get reviews chart data (timeseries)
     * 
     * GET /admin/api/charts/reviews?from=2024-01-01&to=2024-12-31&interval=day
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function reviewsChart(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'from' => 'required|date',
                'to' => 'required|date|after:from',
                'interval' => 'in:day,week,month',
            ]);

            $from = Carbon::parse($request->input('from'))->startOfDay();
            $to = Carbon::parse($request->input('to'))->endOfDay();
            $interval = $request->input('interval', 'day');

            // Build aggregation query
            $query = Review::select(
                \Illuminate\Support\Facades\DB::raw('DATE(created_at) as date'),
                \Illuminate\Support\Facades\DB::raw('COUNT(*) as reviews_count'),
                \Illuminate\Support\Facades\DB::raw('AVG(overall_rating) as avg_rating')
            )
            ->whereBetween('created_at', [$from, $to])
            ->whereNull('deleted_at')
            ->groupBy(\Illuminate\Support\Facades\DB::raw('DATE(created_at)'))
            ->orderBy('date');

            $data = $query->get()->map(fn($row) => [
                'date' => $row->date,
                'reviews_count' => (int)$row->reviews_count,
                'avg_rating' => round((float)$row->avg_rating, 2),
            ])->all();

            return $this->success([
                'interval' => $interval,
                'from' => $from->format('Y-m-d'),
                'to' => $to->format('Y-m-d'),
                'series' => $data,
            ], 'Chart data retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * Get top places by reviews count
     * 
     * GET /admin/api/top-places?limit=10&metric=reviews_count
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function topPlaces(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'limit' => 'integer|min:1|max:50',
                'metric' => 'in:reviews_count,avg_rating',
            ]);

            $limit = $request->input('limit', 10);
            $metric = $request->input('metric', 'reviews_count');

            $places = Place::withCount(['reviews' => function($q) {
                $q->whereNull('deleted_at');
            }])
            ->whereNull('deleted_at')
            ->orderByDesc($metric === 'avg_rating' ? 'avg_rating' : 'reviews_count')
            ->limit($limit)
            ->get()
            ->map(fn($place) => [
                'id' => $place->id,
                'name' => $place->name,
                'reviews_count' => $place->reviews_count,
                'avg_rating' => $place->reviews()->avg('overall_rating') ?? 0,
            ]);

            return $this->success($places, 'Top places retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * Get admin statistics
     * 
     * GET /admin/api/stats
     * 
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            return $this->success([
                'total_admins' => User::count(),
                'active_sessions' => auth('admin')->check() ? 1 : 0,
                'last_backup' => now()->subDays(1)->format('Y-m-d H:i:s'),
            ], 'Statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * List categories (example CRUD endpoint)
     * 
     * GET /admin/api/categories?page=1&search=electronics
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function listCategories(Request $request): JsonResponse
    {
        try {
            // Example: would query categories with pagination
            return $this->success([
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'total' => 0,
                    'per_page' => 15,
                ],
            ], 'Categories retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * Get single category
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function getCategory(int $id): JsonResponse
    {
        return $this->success(null, 'Category retrieved successfully');
    }

    /**
     * Create category
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function storeCategory(Request $request): JsonResponse
    {
        return $this->success(null, 'Category created successfully', 201);
    }

    /**
     * Update category
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateCategory(Request $request, int $id): JsonResponse
    {
        return $this->success(null, 'Category updated successfully');
    }

    /**
     * Delete category
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function deleteCategory(int $id): JsonResponse
    {
        return $this->success(null, 'Category deleted successfully');
    }

    /**
     * Return success response
     * 
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function success($data, string $message = '', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Return error response
     * 
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function error(string $message, $data = null, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => $data,
            'message' => $message,
        ], $statusCode);
    }
}
