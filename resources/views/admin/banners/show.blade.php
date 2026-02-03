@extends('admin.layouts.app')

@section('title','Banner Details')

@section('content')
@php
  $img = $banner->image ? asset($banner->image) : asset('assets/images/category-placeholder.png');
@endphp

<div class="max-w-4xl space-y-6">
  <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">
    <div class="relative h-48 bg-gray-100">
      <img src="{{ $img }}" alt="{{ $banner->offer_name }}" class="w-full h-full object-cover">
      <div class="absolute top-4 right-4">
        <a href="{{ route('admin.banners.edit', $banner) }}"
           class="rounded-2xl bg-white/90 border border-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-white transition">
          Edit
        </a>
      </div>
    </div>

    <div class="p-6">
      <div class="text-xl font-semibold text-gray-900">{{ $banner->offer_name }}</div>
      <div class="text-sm text-gray-500 mt-1">{{ $banner->brand?->name_en ?: 'Vendor' }}</div>

      <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
          <div class="text-xs text-gray-500">Status</div>
          <div class="text-sm font-semibold text-gray-900 mt-1">{{ $banner->is_active ? 'Active' : 'Inactive' }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
          <div class="text-xs text-gray-500">Start Date</div>
          <div class="text-sm font-semibold text-gray-900 mt-1">{{ $banner->start_date?->format('Y-m-d') ?: '-' }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
          <div class="text-xs text-gray-500">End Date</div>
          <div class="text-sm font-semibold text-gray-900 mt-1">{{ $banner->end_date?->format('Y-m-d') ?: '-' }}</div>
        </div>
      </div>
    </div>
  </div>

  <div>
    <a href="{{ route('admin.banners.index') }}"
       class="rounded-2xl bg-white border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
      Back to Banners
    </a>
  </div>
</div>
@endsection
