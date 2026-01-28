@extends('admin.layouts.app')

@section('title','User Profile')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  {{-- Left profile card --}}
  <div class="lg:col-span-1">
    <div class="bg-white rounded-3xl shadow-soft p-6 border border-gray-100">
      <div class="flex items-center gap-4">
        @if(!empty($avatar))
          <div class="w-24 h-24 rounded-xl overflow-hidden">
            <img src="{{ $avatar }}" class="w-full h-full object-cover">
          </div>
        @else
          <div class="w-24 h-24 rounded-xl bg-gray-200 grid place-items-center text-2xl font-bold text-gray-700">{{ strtoupper(mb_substr($user->full_name ?? $user->name ?? 'U',0,1)) }}</div>
        @endif

        <div>
          <div class="text-lg font-semibold">{{ $user->full_name ?? $user->name }}</div>
          <div class="text-sm text-gray-500">{{ $user->email }}</div>
        </div>
      </div>

      <div class="mt-4 flex gap-3">
        <div class="px-4 py-2 rounded-2xl bg-gray-50 text-sm">
          <div class="text-xs text-gray-500">Wallet</div>
          <div class="font-semibold">{{ $stats['wallet'] ?? '0' }}</div>
        </div>
        <div class="px-4 py-2 rounded-2xl bg-gray-50 text-sm">
          <div class="text-xs text-gray-500">Points</div>
          <div class="font-semibold">{{ $stats['points'] ?? 0 }} pts</div>
        </div>
      </div>

      <div class="mt-6">
        <h3 class="text-sm font-semibold mb-2">Personal Information</h3>
        <ul class="space-y-2 text-sm text-gray-700">
          <li><strong class="text-gray-600">Phone:</strong> {{ $user->phone ?? '-' }}</li>
          <li><strong class="text-gray-600">Gender:</strong> {{ optional($user->gender)->name ?? '-' }}</li>
          <li><strong class="text-gray-600">Nationality:</strong> {{ optional($user->nationality)->name ?? '-' }}</li>
          <li><strong class="text-gray-600">City / Area:</strong> {{ $user->city ?? '-' }}</li>
        </ul>
      </div>
    </div>
  </div>

  {{-- Right: stats + reviews --}}
  <div class="lg:col-span-2">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
      <x-admin.stat-mini-card label="Total Reviews" :value="$stats['total']" />
      <x-admin.stat-mini-card label="Good Reviews" :value="$stats['good']" />
      <x-admin.stat-mini-card label="Bad Reviews" :value="$stats['bad']" />
    </div>

    <div class="space-y-3">
      @foreach($recent as $r)
        <x-admin.review-row-card :review="$r" />
      @endforeach
      @if($recent->isEmpty())
        <div class="bg-white border border-gray-100 rounded-xl p-4 text-center text-gray-500">No recent reviews.</div>
      @endif
    </div>
  </div>
</div>

@endsection
