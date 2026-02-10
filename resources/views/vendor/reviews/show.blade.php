@extends('vendor.layouts.app')

@section('title', __('vendor.review_details'))

@section('content')
  <div class="mb-4">
    <a href="{{ route('vendor.reviews.index') }}" class="text-xs font-semibold text-red-700">{{ __('vendor.back_to_reviews') }}</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[24px] p-5 shadow">
      <div class="flex items-center justify-between">
        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('vendor.review') }} #{{ $review->id }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.created_at') }}: {{ $review->created_at?->format('Y-m-d H:i') ?? '-' }}</div>
      </div>

      <div class="mt-4 grid grid-cols-2 gap-4">
        <div class="rounded-2xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-800 p-4">
          <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.rating') }}</div>
          <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $review->overall_rating ?? '-' }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-800 p-4">
          <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.status') }}</div>
          <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $review->status ?? '-' }}</div>
        </div>
      </div>

      <div class="mt-5">
        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('vendor.comment') }}</div>
        <div class="mt-2 text-sm text-gray-700 leading-relaxed">
          {{ $review->comment ?: '-' }}
        </div>
      </div>

      @if($review->photos && $review->photos->count())
        <div class="mt-5">
          <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('vendor.photos') }}</div>
          <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach($review->photos as $p)
              <div class="w-full h-24 rounded-xl overflow-hidden border border-gray-100 dark:border-slate-800 bg-gray-50 dark:bg-slate-800/50">
                <img src="{{ asset($p->storage_path ?? $p->path ?? '') }}" alt="Photo" class="w-full h-full object-cover">
              </div>
            @endforeach
          </div>
        </div>
      @endif

      @if($review->answers && $review->answers->count())
        <div class="mt-5">
          <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('vendor.answers') }}</div>
          <div class="mt-3 space-y-3">
            @foreach($review->answers as $answer)
              <div class="border rounded-xl p-3 bg-gray-50 dark:bg-slate-800/50">
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $answer->criteria?->question ?? '-' }}</div>
                <div class="text-sm font-semibold">
                  {{ $answer->choice?->label ?? $answer->text_value ?? '-' }}
                </div>
                @if($answer->photos && $answer->photos->count())
                  <div class="mt-2 grid grid-cols-3 gap-2">
                    @foreach($answer->photos as $ap)
                      <img src="{{ asset($ap->storage_path ?? '') }}" class="w-full h-16 object-cover rounded-lg" alt="Answer photo">
                    @endforeach
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>

    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-[24px] p-5 shadow">
      <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('vendor.user') }}</div>
      <div class="mt-3">
        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $review->user?->nickname ?? $review->user?->name ?? '-' }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $review->user?->phone ?? '-' }}</div>
      </div>

      <div class="mt-6 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('vendor.place') }}</div>
      <div class="mt-2 text-sm text-gray-700">{{ $review->place?->name ?? '-' }}</div>

      <div class="mt-6 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('vendor.branch') }}</div>
      <div class="mt-2 text-sm text-gray-700">{{ $review->branch?->name ?? '-' }}</div>
    </div>
  </div>
@endsection


