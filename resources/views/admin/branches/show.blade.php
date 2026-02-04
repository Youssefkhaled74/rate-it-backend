@extends('admin.layouts.app')

@section('title','Branch Details')

@section('content')
@php
  $placeName = $branch->place?->display_name ?: 'Place';
  $brandName = $branch->place?->brand?->name_en ?: '-';
  $coverUrl = $branch->cover_url;
  $logoUrl = $branch->logo_url;
@endphp

<div class="max-w-6xl space-y-6">
  <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">
    <div class="relative h-40 {{ $coverUrl ? '' : 'bg-gradient-to-br from-red-50 to-white' }}">
      @if($coverUrl)
        <img src="{{ $coverUrl }}" alt="Cover" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/15"></div>
      @endif
      <div class="absolute inset-0 flex items-center justify-center">
        <div class="w-16 h-16 rounded-2xl bg-white border border-gray-100 grid place-items-center text-red-900 font-semibold shadow-sm overflow-hidden">
          @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="Logo" class="w-full h-full object-cover">
          @else
            {{ strtoupper(mb_substr($placeName, 0, 1)) }}
          @endif
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
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">
      <div class="p-6">
        <div class="text-sm text-gray-500">Branch Name</div>
        <div class="text-xl font-semibold text-gray-900 mt-1">{{ $branch->name ?: $placeName }}</div>
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
          <div class="text-sm font-semibold text-gray-800">Address</div>
          <div class="text-sm text-gray-600 mt-2">{{ $branch->address }}</div>
        </div>

        <div class="mt-4">
          <div class="text-sm font-semibold text-gray-800">Working Hours</div>
          @php
            $wh = $branch->working_hours ?? [];
            $dayLabels = [
              'sat' => 'Saturday',
              'sun' => 'Sunday',
              'mon' => 'Monday',
              'tue' => 'Tuesday',
              'wed' => 'Wednesday',
              'thu' => 'Thursday',
              'fri' => 'Friday',
            ];
          @endphp
          @if(!empty($wh) && is_array($wh))
            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
              @foreach($dayLabels as $key => $label)
                @php
                  $open = $wh[$key]['open'] ?? null;
                  $close = $wh[$key]['close'] ?? null;
                  $isClosed = empty($open) && empty($close);
                @endphp
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4 flex items-center justify-between">
                  <div class="text-sm font-semibold text-gray-900">{{ $label }}</div>
                  <div class="text-xs {{ $isClosed ? 'text-gray-400' : 'text-gray-700 font-semibold' }}">
                    {{ $isClosed ? 'Closed' : ($open . ' - ' . $close) }}
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="mt-2 text-sm text-gray-500">No working hours set.</div>
          @endif
        </div>
      </div>
    </div>

    <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden p-6 h-full">
      <div class="text-lg font-semibold text-gray-900">Branch QR Code</div>
      <div class="text-sm text-gray-500 mt-1">Print or share the QR code for this branch.</div>

      <div class="mt-5 flex flex-col items-center">
        <div class="w-56 h-56 rounded-3xl border border-gray-100 bg-white grid place-items-center">
          <img src="{{ route('admin.branches.qr', $branch) }}"
               alt="Branch QR"
               class="w-48 h-48 object-contain">
        </div>
        <div class="text-sm text-gray-600 mt-3 font-semibold">{{ $branch->name ?: $placeName }}</div>
      </div>

      <div class="mt-6 flex items-center gap-3">
        <a href="{{ route('admin.branches.qr', $branch) }}"
           target="_blank"
           class="flex-1 text-center rounded-2xl bg-white border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
          Open QR
        </a>
        <a href="{{ route('admin.branches.qr.pdf', $branch) }}"
           target="_blank"
           class="flex-1 text-center rounded-2xl bg-red-900 text-white px-4 py-2.5 text-sm font-semibold hover:bg-red-800 transition">
          Print PDF
        </a>
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
