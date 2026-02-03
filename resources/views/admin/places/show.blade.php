@extends('admin.layouts.app')

@section('title','Place Details')

@section('content')
@php
  $cover = $place->cover_image ? asset($place->cover_image) : ($place->logo ? asset($place->logo) : asset('assets/images/category-placeholder.png'));
  $logo = $place->logo ? asset($place->logo) : asset('assets/images/category-icon-placeholder.png');
@endphp

<div class="max-w-4xl space-y-6">
  <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">
    <div class="relative h-48 bg-gray-100">
      <img src="{{ $cover }}" alt="{{ $place->display_name }}" class="w-full h-full object-cover">
      <div class="absolute top-4 right-4">
        <a href="{{ route('admin.places.edit', $place) }}"
           class="rounded-2xl bg-white/90 border border-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-white transition">
          Edit
        </a>
      </div>
    </div>

    <div class="p-6">
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 rounded-full bg-gray-50 overflow-hidden border border-gray-100 grid place-items-center">
          <img src="{{ $logo }}" class="w-16 h-16 object-cover" alt="logo">
        </div>
        <div>
          <div class="text-xl font-semibold text-gray-900">{{ $place->display_name }}</div>
          <div class="text-sm text-gray-500">{{ $place->brand?->name_en ?: '-' }}</div>
        </div>
      </div>

      <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
          <div class="text-xs text-gray-500">Status</div>
          <div class="text-sm font-semibold text-gray-900 mt-1">
            {{ $place->is_active ? 'Active' : 'Inactive' }}
          </div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
          <div class="text-xs text-gray-500">Branches</div>
          <div class="text-sm font-semibold text-gray-900 mt-1">{{ $place->branches_count ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
          <div class="text-xs text-gray-500">Subcategory</div>
          <div class="text-sm font-semibold text-gray-900 mt-1">{{ $place->subcategory?->name_en ?: '-' }}</div>
        </div>
      </div>

      <div class="mt-6">
        <div class="text-sm font-semibold text-gray-800">Description (EN)</div>
        <div class="text-sm text-gray-600 mt-2">{{ $place->description_en ?: '-' }}</div>
      </div>

      <div class="mt-4">
        <div class="text-sm font-semibold text-gray-800">Description (AR)</div>
        <div class="text-sm text-gray-600 mt-2">{{ $place->description_ar ?: '-' }}</div>
      </div>
    </div>
  </div>

  <div>
    <a href="{{ route('admin.places.index') }}"
       class="rounded-2xl bg-white border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
      Back to Places
    </a>
  </div>
</div>
@endsection
