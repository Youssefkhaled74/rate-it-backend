@extends('admin.layouts.app')

@section('page_title', __('admin.reviews'))
@section('title', __('admin.review_details'))

@section('content')
  <div class="mb-4">
    <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-red-700">{{ __('admin.back_to_dashboard') }}</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
      <div class="flex items-center justify-between">
        <div class="text-sm font-semibold text-gray-900">{{ __('admin.review') }} #{{ $review->id }}</div>
        <div class="text-xs text-gray-500">{{ __('admin.created_at') }}: {{ $review->created_at?->format('Y-m-d H:i') ?? '-' }}</div>
      </div>

      <div class="mt-4 grid grid-cols-2 gap-4">
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
          <div class="text-xs text-gray-500">{{ __('admin.rating') }}</div>
          <div class="text-2xl font-semibold text-gray-900">{{ $review->overall_rating ?? '-' }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
          <div class="text-xs text-gray-500">{{ __('admin.status') }}</div>
          <div class="text-sm font-semibold text-gray-900">{{ $review->status ?? '-' }}</div>
        </div>
      </div>

      <div class="mt-5">
        <div class="text-sm font-semibold text-gray-900">{{ __('admin.comment') }}</div>
        <div class="mt-2 text-sm text-gray-700 leading-relaxed">
          {{ $review->comment ?: '-' }}
        </div>
      </div>

      <div class="mt-5">
        <div class="text-sm font-semibold text-gray-900">{{ __('admin.admin_reply') }}</div>
        <div class="mt-2 text-sm text-gray-700 leading-relaxed">
          {{ $review->admin_reply_text ?: '-' }}
        </div>
        @if($review->replied_at)
          <div class="mt-2 text-xs text-gray-400">{{ $review->replied_at?->format('Y-m-d H:i') }}</div>
        @endif
      </div>

      @if($review->photos && $review->photos->count())
        <div class="mt-5">
          <div class="text-sm font-semibold text-gray-900">{{ __('admin.photos') }}</div>
          <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach($review->photos as $p)
              <div class="w-full h-24 rounded-xl overflow-hidden border border-gray-100 bg-gray-50">
                <img src="{{ asset($p->storage_path ?? $p->path ?? '') }}" alt="Photo" class="w-full h-full object-cover">
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>

    <div class="bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
      <div class="text-sm font-semibold text-gray-900">{{ __('admin.user') }}</div>
      <div class="mt-3">
        <div class="text-sm font-semibold text-gray-900">{{ $review->user?->name ?? '-' }}</div>
        <div class="text-xs text-gray-500">{{ $review->user?->phone ?? '-' }}</div>
        @if($review->user)
          <div class="mt-2">
            <a class="text-xs font-semibold text-red-700" href="{{ route('admin.users.show', $review->user) }}">{{ __('admin.view') }}</a>
          </div>
        @endif
      </div>

      <div class="mt-6 text-sm font-semibold text-gray-900">{{ __('admin.place') }}</div>
      <div class="mt-2 text-sm text-gray-700">{{ $review->place?->display_name ?? $review->place?->name ?? '-' }}</div>

      <div class="mt-6 text-sm font-semibold text-gray-900">{{ __('admin.branch') }}</div>
      <div class="mt-2 text-sm text-gray-700">{{ $review->branch?->name ?? '-' }}</div>
    </div>
  </div>
@endsection
