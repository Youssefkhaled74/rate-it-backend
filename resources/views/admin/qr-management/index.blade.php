@extends('admin.layouts.app')

@section('page_title', __('admin.qr_management'))
@section('title', __('admin.qr_management'))

@section('content')
  <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <div>
        <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.qr_management') }}</h2>
        <div class="text-sm text-gray-500 mt-1">{{ __('admin.qr_management_hint') }}</div>
      </div>
      <a href="{{ route('admin.qr-management.export.csv', request()->query()) }}"
         class="rounded-full border border-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
        {{ __('admin.export_csv') }}
      </a>
    </div>

    <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-3">
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.total') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['total'] ?? 0 }}</div>
      </div>
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.active') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['active'] ?? 0 }}</div>
      </div>
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.inactive') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['inactive'] ?? 0 }}</div>
      </div>
    </div>

    <form method="GET" class="mt-5 grid grid-cols-1 md:grid-cols-4 gap-3">
      <input name="q" value="{{ $q }}" placeholder="{{ __('admin.search') }}"
             class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
      <select name="brand_id" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        <option value="">{{ __('admin.all_brands') }}</option>
        @foreach($brands as $b)
          <option value="{{ $b->id }}" {{ (int)$brandId === (int)$b->id ? 'selected' : '' }}>
            {{ $b->name_en ?? $b->name_ar ?? ('#'.$b->id) }}
          </option>
        @endforeach
      </select>
      <select name="status" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        <option value="">{{ __('admin.all') }}</option>
        <option value="1" {{ (string)$status === '1' ? 'selected' : '' }}>{{ __('admin.active') }}</option>
        <option value="0" {{ (string)$status === '0' ? 'selected' : '' }}>{{ __('admin.inactive') }}</option>
      </select>
      <div class="flex items-center gap-2">
        <button type="submit" class="rounded-full bg-red-800 text-white px-6 py-2 text-sm font-semibold hover:bg-red-900 transition">
          {{ __('admin.filter') }}
        </button>
        <a href="{{ route('admin.qr-management.index') }}" class="text-sm text-gray-500">{{ __('admin.reset') }}</a>
      </div>
    </form>
  </div>

  <div class="mt-5 bg-white border border-gray-100 rounded-[24px] p-4 shadow-soft">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-xs uppercase text-gray-400 border-b">
          <tr>
            <th class="text-left py-3 px-2">{{ __('admin.branch') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.place') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.brand') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.qr_code') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.qr_generated_at') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($branches as $b)
            <tr>
              <td class="py-3 px-2 text-gray-900 font-semibold">{{ $b->name ?? '-' }}</td>
              <td class="py-3 px-2 text-gray-700">{{ $b->place?->display_name ?? $b->place?->name_en ?? $b->place?->name_ar ?? '-' }}</td>
              <td class="py-3 px-2 text-gray-700">{{ $b->place?->brand?->name_en ?? $b->place?->brand?->name_ar ?? '-' }}</td>
              <td class="py-3 px-2 text-gray-700">{{ $b->qr_code_value ?? '-' }}</td>
              <td class="py-3 px-2 text-gray-600">{{ $b->qr_generated_at?->format('Y-m-d H:i') ?? '-' }}</td>
              <td class="py-3 px-2">
                <div class="flex items-center gap-2">
                  <a href="{{ route('admin.branches.qr', $b) }}" target="_blank"
                     class="px-3 py-1 rounded-full border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition">
                    {{ __('admin.view_qr') }}
                  </a>
                  <a href="{{ route('admin.branches.qr.pdf', $b) }}" target="_blank"
                     class="px-3 py-1 rounded-full border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition">
                    {{ __('admin.download_pdf') }}
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="py-6 text-center text-gray-500">{{ __('admin.no_branches_found') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-6">
    {{ $branches->links() }}
  </div>
@endsection
