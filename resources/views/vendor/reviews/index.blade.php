@extends('vendor.layouts.app')

@section('title', __('vendor.reviews'))

@section('content')
  <div class="flex items-center justify-between mb-4">
    <div class="text-lg font-semibold">{{ __('vendor.reviews') }}</div>
    <div class="flex items-center gap-2">
      <a href="{{ route('vendor.reviews.export.csv', request()->query()) }}" class="px-3 py-2 text-xs font-semibold rounded-lg border bg-white dark:bg-slate-900">
        {{ __('vendor.export_csv') }}
      </a>
      <a href="{{ route('vendor.reviews.export.xlsx', request()->query()) }}" class="px-3 py-2 text-xs font-semibold rounded-lg border bg-white dark:bg-slate-900 text-gray-400" title="{{ __('vendor.excel_requires_package') }}">
        {{ __('vendor.export_excel') }}
      </a>
    </div>
  </div>

  <form method="GET" class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow mb-4">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
      <div>
        <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.branch') }}</label>
        <select name="branch_id" class="w-full border rounded-lg px-3 py-2">
          <option value="">{{ __('vendor.all') }}</option>
          @foreach($branches as $b)
            <option value="{{ $b->id }}" @selected(($filters['branch_id'] ?? '') == $b->id)>{{ $b->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.date_from') }}</label>
        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full border rounded-lg px-3 py-2">
      </div>
      <div>
        <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.date_to') }}</label>
        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full border rounded-lg px-3 py-2">
      </div>
      <div>
        <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.keyword') }}</label>
        <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" class="w-full border rounded-lg px-3 py-2" placeholder="{{ __('vendor.search') }}">
      </div>
      <div>
        <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.min_rating') }}</label>
        <input type="number" step="0.1" name="min_rating" value="{{ $filters['min_rating'] ?? '' }}" class="w-full border rounded-lg px-3 py-2">
      </div>
      <div>
        <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.max_rating') }}</label>
        <input type="number" step="0.1" name="max_rating" value="{{ $filters['max_rating'] ?? '' }}" class="w-full border rounded-lg px-3 py-2">
      </div>
      <div class="flex items-center gap-2 mt-6">
        <input type="checkbox" id="has_photos" name="has_photos" value="1" @checked(!empty($filters['has_photos']))>
        <label for="has_photos" class="text-sm">{{ __('vendor.has_photos') }}</label>
      </div>
      <div class="mt-5">
        <button class="px-4 py-2 rounded-lg bg-red-700 text-white text-sm font-semibold">{{ __('vendor.filter') }}</button>
      </div>
    </div>
  </form>

  <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-xs text-gray-500 dark:text-gray-400">
          <tr class="border-b">
            <th class="text-left py-2">#</th>
            <th class="text-left py-2">{{ __('vendor.date') }}</th>
            <th class="text-left py-2">{{ __('vendor.user') }}</th>
            <th class="text-left py-2">{{ __('vendor.phone') }}</th>
            <th class="text-left py-2">{{ __('vendor.branch') }}</th>
            <th class="text-left py-2">{{ __('vendor.rating') }}</th>
            <th class="text-left py-2">{{ __('vendor.photos') }}</th>
            <th class="text-left py-2"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($reviews as $review)
            <tr class="border-b">
              <td class="py-2">{{ $review->id }}</td>
              <td class="py-2">{{ $review->created_at?->format('Y-m-d') }}</td>
              <td class="py-2">{{ $review->user?->nickname ?? $review->user?->name ?? '-' }}</td>
              <td class="py-2">{{ $review->user?->phone ?? '-' }}</td>
              <td class="py-2">{{ $review->branch?->name ?? '-' }}</td>
              <td class="py-2">{{ $review->overall_rating ?? '-' }}</td>
              <td class="py-2">{{ $review->photos_count ?? 0 }}</td>
              <td class="py-2 text-right">
                <a href="{{ route('vendor.reviews.show', $review->id) }}" class="text-red-700 text-xs font-semibold">{{ __('vendor.view') }}</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="py-6 text-center text-gray-500 dark:text-gray-400">{{ __('vendor.no_data') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $reviews->appends(request()->query())->links() }}
    </div>
  </div>
@endsection


