@extends('admin.layouts.app')

@section('title','User Profile')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

  {{-- LEFT: Profile --}}
  <div class="lg:col-span-4">
    <div class="bg-white rounded-[28px] shadow-soft border border-gray-100 p-6">
      <div class="flex items-start gap-4">
        {{-- Avatar --}}
        <div class="w-24 h-24 rounded-3xl bg-gray-100 overflow-hidden grid place-items-center shrink-0">
          @if(!empty($avatar))
            <img src="{{ $avatar }}" alt="Avatar" class="w-full h-full object-cover">
          @else
            <div class="w-full h-full grid place-items-center text-2xl font-bold text-gray-700">
              {{ strtoupper(mb_substr($user->full_name ?? $user->name ?? 'U',0,1)) }}
            </div>
          @endif
        </div>

        <div class="min-w-0">
          <div class="text-lg font-semibold text-gray-900 truncate">
            {{ $user->full_name ?? $user->name }}
          </div>
          <div class="text-sm text-gray-500 truncate">
            {{ $user->email }}
          </div>

          {{-- Pills --}}
          <div class="mt-4 flex gap-3">
            <div class="flex-1 rounded-2xl bg-amber-50 px-4 py-3 border border-amber-100">
              <div class="text-[11px] text-amber-700/80">Wallet</div>
              <div class="text-sm font-semibold text-amber-900">
                {{ $stats['wallet'] ?? '0' }} <span class="text-xs font-medium text-amber-700">EGP</span>
              </div>
            </div>

            <div class="flex-1 rounded-2xl bg-gray-50 px-4 py-3 border border-gray-100">
              <div class="text-[11px] text-gray-500">Points</div>
              <div class="text-sm font-semibold text-gray-900">
                {{ $stats['points'] ?? 0 }} <span class="text-xs font-medium text-gray-500">pts</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Personal Information --}}
      <div class="mt-6">
        <div class="text-sm font-semibold text-gray-900">Personal Information</div>

        <div class="mt-4 space-y-3">
          {{-- Phone --}}
          <div class="flex items-center gap-3 rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
            <span class="w-9 h-9 rounded-2xl bg-white border border-gray-100 grid place-items-center text-red-800">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.12.86.31 1.7.57 2.5a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.58-1.09a2 2 0 0 1 2.11-.45c.8.26 1.64.45 2.5.57A2 2 0 0 1 22 16.92z"/>
              </svg>
            </span>
            <div class="min-w-0">
              <div class="text-[11px] text-gray-500">Phone</div>
              <div class="text-sm font-semibold text-gray-900 truncate">{{ $user->phone ?? '-' }}</div>
            </div>
          </div>

          {{-- Gender --}}
          <div class="flex items-center gap-3 rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
            <span class="w-9 h-9 rounded-2xl bg-white border border-gray-100 grid place-items-center text-red-800">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 15a7 7 0 1 0-7-7 7 7 0 0 0 7 7z"/>
                <path d="M12 15v7"/>
                <path d="M9 19h6"/>
              </svg>
            </span>
            <div class="min-w-0">
              <div class="text-[11px] text-gray-500">Gender</div>
              <div class="text-sm font-semibold text-gray-900 truncate">
                {{ optional($user->gender)->name_en ?? optional($user->gender)->name ?? optional($user->gender)->code ?? ($user->gender ?? '-') }}
              </div>
            </div>
          </div>

          {{-- Nationality --}}
          <div class="flex items-center gap-3 rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
            <span class="w-9 h-9 rounded-2xl bg-white border border-gray-100 grid place-items-center text-red-800">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="10"/>
                <path d="M2 12h20"/>
                <path d="M12 2a15.3 15.3 0 0 1 0 20"/>
                <path d="M12 2a15.3 15.3 0 0 0 0 20"/>
              </svg>
            </span>
            <div class="min-w-0">
              <div class="text-[11px] text-gray-500">Nationality</div>
              <div class="text-sm font-semibold text-gray-900 truncate">
                {{ optional($user->nationality)->name_en ?? optional($user->nationality)->country_name ?? optional($user->nationality)->name ?? optional($user->nationality)->iso_code ?? ($user->nationality ?? '-') }}
              </div>
            </div>
          </div>

          {{-- City / Area --}}
          <div class="flex items-center gap-3 rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
            <span class="w-9 h-9 rounded-2xl bg-white border border-gray-100 grid place-items-center text-red-800">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
            </span>
            <div class="min-w-0">
              <div class="text-[11px] text-gray-500">City & Area</div>
              <div class="text-sm font-semibold text-gray-900 truncate">{{ $user->city ?? '-' }}</div>
            </div>
          </div>

          {{-- Date of birth (optional if you have it) --}}
          @if(!empty($user->birth_date))
            <div class="flex items-center gap-3 rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
              <span class="w-9 h-9 rounded-2xl bg-white border border-gray-100 grid place-items-center text-red-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="3" y="4" width="18" height="18" rx="2"/>
                  <path d="M16 2v4M8 2v4M3 10h18"/>
                </svg>
              </span>
              <div class="min-w-0">
                <div class="text-[11px] text-gray-500">Date Of Birth</div>
                <div class="text-sm font-semibold text-gray-900 truncate">{{ $user->birth_date }}</div>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- RIGHT: Stats + Reviews --}}
  <div class="lg:col-span-8">

    {{-- Top Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
      {{-- Total Reviews (red) --}}
      <div class="rounded-[22px] bg-red-900 text-white p-5 shadow-soft">
        <div class="text-xs opacity-80">Total Reviews</div>
        <div class="mt-2 text-2xl font-semibold">{{ (int)($stats['total'] ?? 0) }}</div>
      </div>

      <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
        <div class="text-xs text-gray-500">Good Reviews</div>
        <div class="mt-2 text-2xl font-semibold text-red-900">{{ (int)($stats['good'] ?? 0) }}</div>
      </div>

      <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
        <div class="text-xs text-gray-500">Bad Reviews</div>
        <div class="mt-2 text-2xl font-semibold text-red-900">{{ (int)($stats['bad'] ?? 0) }}</div>
      </div>
    </div>

    {{-- Reviews List --}}
    <div class="space-y-4">
      @forelse($recent as $r)

        @php
          // Adapt to your real review fields
          $placeName = data_get($r, 'place.name')
                       ?? data_get($r, 'place.title')
                       ?? data_get($r, 'place_name')
                       ?? 'Place';

          $ago = optional($r->created_at)->diffForHumans() ?? '';
          $text = $r->comment ?? $r->review_text ?? $r->text ?? '';
          $emoji = $r->sentiment_emoji ?? '😍'; // optional
          $quality = $r->quality_rating ?? null;
          $price = $r->price_rating ?? null;
          $thumb = data_get($r,'photo_url') ?? data_get($r,'cover') ?? null;
        @endphp

        <div class="bg-white border border-gray-100 rounded-[26px] p-5 shadow-soft">
          <div class="flex items-start gap-4">
            {{-- Place icon/logo --}}
            <div class="w-10 h-10 rounded-2xl bg-red-50 border border-red-100 grid place-items-center shrink-0">
              <span class="text-red-800 text-sm font-bold">
                {{ strtoupper(mb_substr($placeName,0,1)) }}
              </span>
            </div>

            <div class="min-w-0 flex-1">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="font-semibold text-gray-900 truncate">{{ $placeName }}</div>
                  <div class="text-xs text-gray-500 mt-0.5">{{ $ago }}</div>
                </div>

                {{-- Emoji / sentiment --}}
                <div class="w-9 h-9 rounded-2xl bg-gray-50 border border-gray-100 grid place-items-center shrink-0">
                  <span class="text-lg leading-none">{{ $emoji }}</span>
                </div>
              </div>

              {{-- Review text --}}
              @if(!empty($text))
                <p class="text-sm text-gray-600 mt-3 leading-relaxed">
                  {{ $text }}
                </p>
              @endif

              {{-- Chips row --}}
              <div class="mt-4 flex flex-wrap items-center gap-3">
                <div class="text-xs text-gray-500 flex items-center gap-2">
                  <span class="font-semibold text-gray-700">Quality:</span>
                  <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-50 border border-amber-100">😍</span>
                </div>

                <div class="text-xs text-gray-500 flex items-center gap-2">
                  <span class="font-semibold text-gray-700">Price:</span>
                  <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-50 border border-amber-100">😍</span>
                </div>

                <button type="button"
                  class="ml-auto inline-flex items-center gap-2 rounded-full bg-red-900 text-white px-4 py-2 text-xs font-semibold hover:bg-red-950 transition">
                  Questions
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 18l6-6-6-6"/>
                  </svg>
                </button>
              </div>

              {{-- Optional: questions block (static UI placeholder like screenshot) --}}
              <div class="mt-4 space-y-2">
                <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3 text-xs text-gray-600">
                  Are the prices reasonable compared to the quality?
                  <span class="float-right">😄</span>
                </div>
                <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3 text-xs text-gray-600">
                  How would you rate the value for money?
                  <span class="float-right">😄</span>
                </div>
                <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3 text-xs text-gray-600">
                  Would you recommend this place to friends and family?
                  <span class="float-right">😄</span>
                </div>
              </div>

            </div>

            {{-- Optional thumbnail (like pizza image) --}}
            @if(!empty($thumb))
              <div class="w-24 h-24 rounded-2xl overflow-hidden bg-gray-100 border border-gray-100 shrink-0">
                <img src="{{ $thumb }}" class="w-full h-full object-cover" alt="Photo">
              </div>
            @endif
          </div>
        </div>

      @empty
        <div class="bg-white border border-gray-100 rounded-2xl p-6 text-center text-gray-500">
          No reviews found for this user.
        </div>
      @endforelse
    </div>

  </div>
</div>
@endsection
