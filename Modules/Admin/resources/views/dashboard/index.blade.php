@extends('admin::layouts.app')

@section('title', __('admin.dashboard'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 md:p-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ __('admin.dashboard') }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Welcome back, {{ $admin->name }}
        </p>
    </div>

    <!-- KPIs Section -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8" id="kpis-container">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 animate-pulse">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-4"></div>
            <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 animate-pulse">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-4"></div>
            <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 animate-pulse">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-4"></div>
            <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 animate-pulse">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-4"></div>
            <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Reviews Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Reviews Trend (Last 30 Days)
            </h2>
            <div id="reviews-chart-container" class="h-64 bg-gray-100 dark:bg-gray-700 rounded animate-pulse flex items-center justify-center">
                <p class="text-gray-500 dark:text-gray-400">Loading chart...</p>
            </div>
        </div>

        <!-- Top Places -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Top Places
            </h2>
            <div id="top-places-container" class="space-y-3">
                @for($i = 0; $i < 5; $i++)
                <div class="h-12 bg-gray-100 dark:bg-gray-700 rounded animate-pulse"></div>
                @endfor
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
/**
 * Dashboard AJAX Example
 * 
 * Shows how to:
 * 1. Call the API endpoints using route() helpers
 * 2. Include CSRF token in fetch requests
 * 3. Handle JSON responses
 * 4. Display data in the dashboard
 */

document.addEventListener('DOMContentLoaded', async function() {
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // API URLs from the controller
    const apiKpisUrl = '{{ $apiKpisUrl }}';
    const apiChartUrl = '{{ $apiChartUrl }}';
    const apiTopPlacesUrl = '{{ $apiTopPlacesUrl }}';

    // ========================================================================
    // 1. FETCH KPIs
    // ========================================================================
    async function fetchKpis() {
        try {
            const response = await fetch(apiKpisUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': csrfToken,
                },
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Failed to fetch KPIs');
            }

            renderKpis(result.data);
        } catch (error) {
            console.error('KPIs Error:', error);
            document.getElementById('kpis-container').innerHTML = `
                <div class="col-span-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 p-4 rounded">
                    Error loading KPIs: ${error.message}
                </div>
            `;
        }
    }

    function renderKpis(data) {
        const container = document.getElementById('kpis-container');
        container.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">{{ __('admin.total_reviews') }}</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">${data.reviews_count}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">{{ __('admin.avg_rating') }}</p>
                <p class="text-2xl font-bold text-yellow-500 mt-2">${data.avg_rating} ⭐</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">{{ __('admin.total_users') }}</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">${data.users_count}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">{{ __('admin.total_places') }}</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-2">${data.places_count}</p>
            </div>
        `;
    }

    // ========================================================================
    // 2. FETCH REVIEWS CHART
    // ========================================================================
    async function fetchReviewsChart() {
        try {
            const from = new Date();
            from.setDate(from.getDate() - 30);
            const fromStr = from.toISOString().split('T')[0];
            const toStr = new Date().toISOString().split('T')[0];

            const url = `${apiChartUrl}?from=${fromStr}&to=${toStr}&interval=day`;

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': csrfToken,
                },
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Failed to fetch chart data');
            }

            renderChart(result.data);
        } catch (error) {
            console.error('Chart Error:', error);
            document.getElementById('reviews-chart-container').innerHTML = `
                <p class="text-red-600 dark:text-red-400">Error loading chart: ${error.message}</p>
            `;
        }
    }

    function renderChart(data) {
        const container = document.getElementById('reviews-chart-container');
        
        // Simple ASCII-style chart (in production, use Chart.js or similar)
        const maxCount = Math.max(...data.series.map(s => s.reviews_count), 1);
        const chartHtml = data.series
            .slice(-14) // Last 14 days
            .map(point => {
                const barHeight = (point.reviews_count / maxCount) * 100;
                return `
                    <div class="flex flex-col items-center" title="${point.date}">
                        <div class="w-8 bg-blue-500 rounded-t" style="height: ${barHeight}px; min-height: 5px;"></div>
                        <span class="text-xs text-gray-600 dark:text-gray-400 mt-2">${point.date.slice(-5)}</span>
                    </div>
                `;
            })
            .join('');

        container.innerHTML = `
            <div class="flex gap-2 items-end h-64">
                ${chartHtml}
            </div>
            <div class="text-center mt-4 text-sm text-gray-600 dark:text-gray-400">
                Reviews per day (last 14 days)
            </div>
        `;
    }

    // ========================================================================
    // 3. FETCH TOP PLACES
    // ========================================================================
    async function fetchTopPlaces() {
        try {
            const response = await fetch(`${apiTopPlacesUrl}?limit=5`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': csrfToken,
                },
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Failed to fetch top places');
            }

            renderTopPlaces(result.data);
        } catch (error) {
            console.error('Top Places Error:', error);
            document.getElementById('top-places-container').innerHTML = `
                <p class="text-red-600 dark:text-red-400">Error loading top places: ${error.message}</p>
            `;
        }
    }

    function renderTopPlaces(places) {
        const container = document.getElementById('top-places-container');
        
        if (places.length === 0) {
            container.innerHTML = '<p class="text-gray-500 dark:text-gray-400">No places found</p>';
            return;
        }

        const html = places.map((place, index) => `
            <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm">
                    ${index + 1}
                </div>
                <div class="flex-grow">
                    <h3 class="font-medium text-gray-900 dark:text-white">${place.name}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        ${place.reviews_count} reviews • ${place.avg_rating}⭐
                    </p>
                </div>
                <a href="/admin/places/${place.id}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                    View →
                </a>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    // ========================================================================
    // INITIALIZE ALL DASHBOARDS
    // ========================================================================
    await Promise.all([
        fetchKpis(),
        fetchReviewsChart(),
        fetchTopPlaces(),
    ]);
});
</script>
@endsection
