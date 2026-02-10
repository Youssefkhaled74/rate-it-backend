@extends('vendor.layouts.app')

@section('title', __('vendor.branch_settings'))

@section('content')
  <div class="flex items-center justify-between mb-4">
    <div class="text-lg font-semibold">{{ __('vendor.branch_settings') }}</div>
  </div>

  @if(session('success'))
    <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm">{{ session('success') }}</div>
  @endif

  <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-xs text-gray-500 dark:text-gray-400">
          <tr class="border-b">
            <th class="text-left py-2">{{ __('vendor.branch') }}</th>
            <th class="text-left py-2">{{ __('vendor.place') }}</th>
            <th class="text-left py-2">{{ __('vendor.cooldown_days') }}</th>
            <th class="text-left py-2"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($branches as $branch)
            <tr class="border-b">
              <td class="py-3">{{ $branch->name }}</td>
              <td class="py-3">{{ $branch->place?->name ?? '-' }}</td>
              <td class="py-3">
                <form method="POST" action="{{ route('vendor.branches.cooldown.update', $branch->id) }}" class="flex items-center gap-2">
                  @csrf
                  <input type="number" name="review_cooldown_days" value="{{ $branch->review_cooldown_days ?? 0 }}" class="w-24 border rounded-lg px-2 py-1" min="0" max="365">
                  <button class="px-3 py-1 rounded-lg bg-red-700 text-white text-xs font-semibold">{{ __('vendor.save') }}</button>
                </form>
                @error('review_cooldown_days')
                  <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                @enderror
              </td>
              <td class="py-3 text-right text-xs text-gray-400">#{{ $branch->id }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="py-6 text-center text-gray-500 dark:text-gray-400">{{ __('vendor.no_data') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection


