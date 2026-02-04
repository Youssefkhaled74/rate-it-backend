@extends('admin.layouts.app')

@section('title', __('admin.lookups'))

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.lookups') }}</h2>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <a href="{{ route('admin.lookups.genders.index', ['lang' => request('lang')]) }}"
       class="group rounded-[22px] bg-white border border-gray-100 shadow-soft p-6 hover:border-gray-200 transition">
      <div class="w-12 h-12 rounded-2xl bg-red-50 border border-red-100 grid place-items-center text-red-900">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M12 15a7 7 0 1 0-7-7 7 7 0 0 0 7 7z" />
          <path d="M12 15v7" />
          <path d="M9 19h6" />
        </svg>
      </div>
      <div class="mt-4 text-sm font-semibold text-gray-900">{{ __('admin.genders') }}</div>
      <div class="text-xs text-gray-500 mt-1">{{ __('admin.manage_genders') }}</div>
    </a>

    <a href="{{ route('admin.lookups.nationalities.index', ['lang' => request('lang')]) }}"
       class="group rounded-[22px] bg-white border border-gray-100 shadow-soft p-6 hover:border-gray-200 transition">
      <div class="w-12 h-12 rounded-2xl bg-red-50 border border-red-100 grid place-items-center text-red-900">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="12" cy="12" r="10" />
          <path d="M2 12h20" />
          <path d="M12 2a15.3 15.3 0 0 1 0 20" />
          <path d="M12 2a15.3 15.3 0 0 0 0 20" />
        </svg>
      </div>
      <div class="mt-4 text-sm font-semibold text-gray-900">{{ __('admin.nationalities') }}</div>
      <div class="text-xs text-gray-500 mt-1">{{ __('admin.manage_nationalities') }}</div>
    </a>

    <a href="{{ route('admin.lookups.cities.index', ['lang' => request('lang')]) }}"
       class="group rounded-[22px] bg-white border border-gray-100 shadow-soft p-6 hover:border-gray-200 transition">
      <div class="w-12 h-12 rounded-2xl bg-red-50 border border-red-100 grid place-items-center text-red-900">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M3 21h18" />
          <path d="M4 21V8l8-5 8 5v13" />
          <path d="M9 21v-6h6v6" />
        </svg>
      </div>
      <div class="mt-4 text-sm font-semibold text-gray-900">{{ __('admin.cities') }}</div>
      <div class="text-xs text-gray-500 mt-1">{{ __('admin.manage_cities') }}</div>
    </a>

    <a href="{{ route('admin.lookups.areas.index', ['lang' => request('lang')]) }}"
       class="group rounded-[22px] bg-white border border-gray-100 shadow-soft p-6 hover:border-gray-200 transition">
      <div class="w-12 h-12 rounded-2xl bg-red-50 border border-red-100 grid place-items-center text-red-900">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M12 21s-7-4.6-7-11a7 7 0 0 1 14 0c0 6.4-7 11-7 11z" />
          <circle cx="12" cy="10" r="3" />
        </svg>
      </div>
      <div class="mt-4 text-sm font-semibold text-gray-900">{{ __('admin.areas') }}</div>
      <div class="text-xs text-gray-500 mt-1">{{ __('admin.manage_areas') }}</div>
    </a>
  </div>
</div>
@endsection
