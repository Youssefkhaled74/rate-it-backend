@extends('admin.layouts.app')

@section('title','Branch Details')

@section('content')
@php
  $placeName = $branch->place?->display_name ?: 'Place';
  $brandName = $branch->place?->brand?->name_en ?: '-';
@endphp

<div class="max-w-4xl space-y-6">
  <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">
    <div class="relative h-40 bg-gradient-to-br from-red-50 to-white">
      <div class="absolute inset-0 flex items-center justify-center">
        <div class="w-16 h-16 rounded-2xl bg-white border border-gray-100 grid place-items-center text-red-900 font-semibold shadow-sm">
          {{ strtoupper(mb_substr($placeName, 0, 1)) }}
        </div>
      </div>
      <div class="absolute top-4 right-4">
        <a href="{{ route('admin.branches.edit', $branch) }}"
           class="rounded-2xl bg-white/90 border border-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-white transition">
          Edit
        </a>
      </div>
    </div>

    <div class="p-6">
      <div class="text-xl font-semibold text-gray-900">{{ $placeName }}</div>
      <div class="text-sm text-gray-500 mt-1">{{ $brandName }}</div>

      <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
          <div class="text-xs text-gray-500">Status</div>
          <div class="text-sm font-semibold text-gray-900 mt-1">{{ $branch->is_active ? 'Active' : 'Inactive' }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
          <div class="text-xs text-gray-500">Cooldown</div>
          <div class="text-sm font-semibold text-gray-900 mt-1">{{ $branch->review_cooldown_days ?? 0 }} days</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
          <div class="text-xs text-gray-500">Coordinates</div>
          <div class="text-sm font-semibold text-gray-900 mt-1">{{ $branch->lat ?? '-' }}, {{ $branch->lng ?? '-' }}</div>
        </div>
      </div>

      <div class="mt-6">
        <div class="text-sm font-semibold text-gray-800">Branch Name</div>
        <div class="text-sm text-gray-600 mt-2">{{ $branch->name ?: '-' }}</div>
      </div>

      <div class="mt-4">
        <div class="text-sm font-semibold text-gray-800">Address</div>
        <div class="text-sm text-gray-600 mt-2">{{ $branch->address }}</div>
      </div>

      <div class="mt-4">
        <div class="text-sm font-semibold text-gray-800">Working Hours</div>
        <pre class="mt-2 text-xs bg-gray-50 border border-gray-100 rounded-2xl p-4 overflow-auto">{{ $branch->working_hours ? json_encode($branch->working_hours, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '-' }}</pre>
      </div>
    </div>
  </div>

  <div>
    <a href="{{ route('admin.branches.index') }}"
       class="rounded-2xl bg-white border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
      Back to Branches
    </a>
  </div>
</div>
@endsection
