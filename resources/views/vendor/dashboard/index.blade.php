@extends('vendor.layouts.app')

@section('title', __('vendor.dashboard'))

@section('content')
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="vendor-card">
      <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.total_branches') }}</div>
      <div class="text-2xl font-semibold mt-1">{{ $summary['total_branches'] ?? 0 }}</div>
    </div>
    <div class="vendor-card">
      <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.reviews_last_7') }}</div>
      <div class="text-2xl font-semibold mt-1">{{ $summary['reviews_count']['last_7_days'] ?? 0 }}</div>
    </div>
    <div class="vendor-card">
      <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.reviews_last_30') }}</div>
      <div class="text-2xl font-semibold mt-1">{{ $summary['reviews_count']['last_30_days'] ?? 0 }}</div>
    </div>
    <div class="vendor-card">
      <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.avg_rating') }}</div>
      <div class="text-2xl font-semibold mt-1">{{ $summary['average_rating_brand'] ?? 0 }}</div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
    <div class="vendor-card flex flex-col justify-between">
      <div class="text-sm font-semibold mb-4">{{ __('vendor.vouchers_used') }}</div>
      <div class="flex gap-6">
        <div>
          <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.last_7_days') }}</div>
          <div class="text-xl font-semibold mt-1">{{ $summary['vouchers_used']['last_7_days'] ?? 0 }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.last_30_days') }}</div>
          <div class="text-xl font-semibold mt-1">{{ $summary['vouchers_used']['last_30_days'] ?? 0 }}</div>
        </div>
      </div>
    </div>

    <div class="vendor-card">
      <div class="text-sm font-semibold mb-4">{{ __('vendor.top_branches') }}</div>
      <div class="space-y-2">
        @forelse(($summary['top_branches_by_rating'] ?? []) as $branch)
          <div class="flex items-center justify-between text-sm">
            <div>
              <div class="font-semibold">{{ $branch['name'] ?? '-' }}</div>
              <div class="text-xs text-gray-500 dark:text-gray-400">{{ $branch['place_name'] ?? '-' }}</div>
            </div>
            <div class="font-semibold">{{ $branch['average_rating'] ?? 0 }}</div>
          </div>
        @empty
          <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('vendor.no_data') }}</div>
        @endforelse
      </div>
    </div>
  </div>
@endsection


