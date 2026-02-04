@extends('admin.layouts.app')

@section('page_title', __('admin.dashboard'))
@section('title', __('admin.dashboard_title'))

@section('content')
  {{-- Welcome + Stats --}}
  <div class="bg-white rounded-3xl shadow-soft p-6">
    <div class="flex items-start justify-between gap-6">
      <div>
        <h2 class="text-xl font-semibold">Welcome Back, {{ $welcomeName ?? 'Admin' }}!</h2>
        <p class="text-sm text-gray-500 mt-1">Here's what's happening with your platform today</p>
      </div>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
      <x-admin.stat-card title="Average Rating" value="{{ $stats['average_rating'] ?? '0.0' }}"
        sub="{{ $stats['average_delta_percent'] !== null ? ($stats['average_delta_percent'] . ' %') : '' }}"
        accent="{{ ($stats['average_delta_percent'] ?? null) === null ? 'text-gray-500' : (($stats['average_delta_percent'] > 0) ? 'text-green-600' : 'text-red-600') }}" />

      <x-admin.stat-card title="Total Review" value="{{ number_format($stats['total_reviews'] ?? 0) }}" 
        sub="{{ $stats['total_delta_percent'] !== null ? ($stats['total_delta_percent'] . ' %') : '' }}" />

      <x-admin.stat-card title="New (7 days)" value="{{ $stats['new_7_days'] ?? 0 }}" 
        sub="{{ $stats['new_7_days'] ? ('+' . $stats['new_7_days']) : '' }}" />

      <x-admin.stat-card title="Pending Reply" value="{{ $stats['pending_reply'] ?? 0 }}" sub="" accent="text-red-600"/>
    </div>
  </div>

  {{-- Recent Reviews --}}
  <div class="mt-6">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-4">
        <h3 class="text-lg font-semibold">Recent Reviews</h3>
        <div class="flex items-center gap-2 text-sm text-gray-500">
          <a href="?status=all" class="px-3 py-1 rounded-full bg-gray-200 text-gray-700">All ({{ $counts['all'] ?? 0 }})</a>
          <a href="?status=urgent" class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-600"></span>Urgent ({{ $counts['urgent'] ?? 0 }})</a>
          <a href="?status=high" class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-yellow-500"></span>High ({{ $counts['high'] ?? 0 }})</a>
          <a href="?status=normal" class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-500"></span>Normal ({{ $counts['normal'] ?? 0 }})</a>
        </div>
      </div>
      <a href="#" class="text-sm text-red-700 font-semibold hover:underline">View all (80)</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      @forelse($reviews as $review)
        <x-admin.review-card :status="$review['status']" :text="$review['text']" :name="$review['name']" :meta="$review['meta']" />
      @empty
        <div class="text-sm text-gray-500">No recent reviews found.</div>
      @endforelse
    </div>
  </div>
@endsection

