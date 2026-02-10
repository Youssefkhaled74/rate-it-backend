@extends('vendor.layouts.app')

@section('title', __('vendor.branch_users'))

@section('content')
  <div class="flex items-center justify-between mb-4">
    <div class="text-lg font-semibold">{{ __('vendor.branch_users') }}</div>
    <a href="{{ route('vendor.staff.create') }}" class="px-3 py-2 text-xs font-semibold rounded-lg bg-red-700 text-white">{{ __('vendor.add_user') }}</a>
  </div>

  @if(session('success'))
    <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm">{{ session('success') }}</div>
  @endif
  @if(session('temporary_password'))
    <div class="mb-4 p-3 rounded-lg bg-yellow-50 text-yellow-800 text-sm">
      {{ __('vendor.temporary_password') }}: <span class="font-semibold">{{ session('temporary_password') }}</span>
    </div>
  @endif

  <form method="GET" class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow mb-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
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
        <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.status') }}</label>
        <select name="is_active" class="w-full border rounded-lg px-3 py-2">
          <option value="">{{ __('vendor.all') }}</option>
          <option value="1" @selected(($filters['is_active'] ?? '') === '1')>{{ __('vendor.active') }}</option>
          <option value="0" @selected(($filters['is_active'] ?? '') === '0')>{{ __('vendor.inactive') }}</option>
        </select>
      </div>
      <div>
        <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.search') }}</label>
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="w-full border rounded-lg px-3 py-2" placeholder="{{ __('vendor.search') }}">
      </div>
    </div>
    <div class="mt-3">
      <button class="px-4 py-2 rounded-lg bg-red-700 text-white text-sm font-semibold">{{ __('vendor.filter') }}</button>
    </div>
  </form>

  <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-xs text-gray-500 dark:text-gray-400">
          <tr class="border-b">
            <th class="text-left py-2">{{ __('vendor.name') }}</th>
            <th class="text-left py-2">{{ __('vendor.phone') }}</th>
            <th class="text-left py-2">{{ __('vendor.branch') }}</th>
            <th class="text-left py-2">{{ __('vendor.status') }}</th>
            <th class="text-left py-2"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($staff as $item)
            <tr class="border-b">
              <td class="py-2">{{ $item->name }}</td>
              <td class="py-2">{{ $item->phone }}</td>
              <td class="py-2">{{ $item->branch?->name ?? '-' }}</td>
              <td class="py-2">
                <span class="text-xs px-2 py-1 rounded-full {{ $item->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-300' }}">
                  {{ $item->is_active ? __('vendor.active') : __('vendor.inactive') }}
                </span>
              </td>
              <td class="py-2 text-right">
                <a href="{{ route('vendor.staff.edit', $item->id) }}" class="text-red-700 text-xs font-semibold">{{ __('vendor.edit') }}</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-6 text-center text-gray-500 dark:text-gray-400">{{ __('vendor.no_data') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $staff->appends(request()->query())->links() }}
    </div>
  </div>
@endsection


