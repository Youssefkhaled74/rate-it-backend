@extends('admin.layouts.app')

@section('page_title', __('admin.featured_places'))
@section('title', __('admin.featured_places'))

@section('content')
  <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <div>
        <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.featured_places') }}</h2>
        <div class="text-sm text-gray-500 mt-1">{{ __('admin.featured_places_hint') }}</div>
      </div>
    </div>

    @if(session('success'))
      <div class="mt-4 rounded-2xl bg-green-50 border border-green-100 text-green-700 text-sm px-4 py-3">
        {{ session('success') }}
      </div>
    @endif

    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-3">
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.places_total') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['total'] ?? 0 }}</div>
      </div>
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.places_featured') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['featured'] ?? 0 }}</div>
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
      <select name="featured" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        <option value="">{{ __('admin.all') }}</option>
        <option value="1" {{ (string)$featured === '1' ? 'selected' : '' }}>{{ __('admin.featured') }}</option>
        <option value="0" {{ (string)$featured === '0' ? 'selected' : '' }}>{{ __('admin.not_featured') }}</option>
      </select>
      <div class="flex items-center gap-2">
        <button type="submit" class="rounded-full bg-red-800 text-white px-6 py-2 text-sm font-semibold hover:bg-red-900 transition">
          {{ __('admin.filter') }}
        </button>
        <a href="{{ route('admin.featured-places.index') }}" class="text-sm text-gray-500">{{ __('admin.reset') }}</a>
      </div>
    </form>
  </div>

  <div class="mt-5 grid grid-cols-1 gap-4">
    @forelse($places as $p)
      <div class="bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
          <div class="min-w-0">
            <div class="text-sm text-gray-500">{{ $p->brand?->name_en ?? $p->brand?->name_ar ?? '-' }}</div>
            <div class="text-lg font-semibold text-gray-900 truncate">{{ $p->display_name ?? $p->name_en ?? $p->name_ar ?? '-' }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $p->city ?? '-' }} · {{ $p->area ?? '-' }}</div>
          </div>
          <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('admin.featured-places.toggle', $p) }}">
              @csrf
              @method('PATCH')
              <button type="submit"
                class="px-3 py-2 rounded-full border {{ $p->is_featured ? 'border-yellow-200 text-yellow-800' : 'border-gray-200 text-gray-600' }} text-xs font-semibold hover:bg-yellow-50 transition">
                {{ $p->is_featured ? __('admin.unfeature') : __('admin.feature') }}
              </button>
            </form>
            <a href="{{ route('admin.places.show', $p) }}"
               class="px-3 py-2 rounded-full border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition">
              {{ __('admin.view') }}
            </a>
          </div>
        </div>
      </div>
    @empty
      <div class="bg-white border border-gray-100 rounded-[24px] p-10 text-center text-gray-500 shadow-soft">
        {{ __('admin.no_places_found') }}
      </div>
    @endforelse
  </div>

  <div class="mt-6">
    {{ $places->links() }}
  </div>
@endsection
