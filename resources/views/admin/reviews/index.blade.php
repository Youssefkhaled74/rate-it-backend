@extends('admin.layouts.app')

@section('page_title', __('admin.reviews'))
@section('title', __('admin.reviews'))

@section('content')
  <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.reviews') }}</h2>
        <div class="text-sm text-gray-500 mt-1">{{ __('admin.reviews_subtitle') }}</div>
      </div>
      <div class="flex items-center gap-2">
        <button type="button" id="exportReviewsCsv" class="w-9 h-9 rounded-full border border-gray-200 grid place-items-center text-gray-600 hover:border-gray-300" title="Export Excel">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <path d="M14 2v6h6"/>
            <path d="M9 12l6 6M15 12l-6 6"/>
          </svg>
        </button>
        <button type="button" id="exportReviewsPdf" class="w-9 h-9 rounded-full border border-red-200 grid place-items-center text-red-700 hover:border-red-300" title="Export PDF">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <path d="M14 2v6h6"/>
            <path d="M8 13h4a2 2 0 0 1 0 4H8z"/>
            <path d="M14 17v-4h2a2 2 0 0 1 0 4h-2z"/>
          </svg>
        </button>
      </div>
    </div>

    <form method="GET" class="mt-5 grid grid-cols-1 md:grid-cols-5 gap-3">
      <input name="q" value="{{ $q }}" placeholder="{{ __('admin.search') }}" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
      <select name="status" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>{{ __('admin.all') }}</option>
        <option value="hidden" {{ $status === 'hidden' ? 'selected' : '' }}>{{ __('admin.hidden') }}</option>
        <option value="visible" {{ $status === 'visible' ? 'selected' : '' }}>{{ __('admin.visible') }}</option>
        <option value="featured" {{ $status === 'featured' ? 'selected' : '' }}>{{ __('admin.featured') }}</option>
        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>{{ __('admin.pending_reply') }}</option>
      </select>
      <select name="rating" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        <option value="">{{ __('admin.rating') }}</option>
        @for($i=5;$i>=1;$i--)
          <option value="{{ $i }}" {{ (string)$rating === (string)$i ? 'selected' : '' }}>{{ $i }}</option>
        @endfor
      </select>
      <input type="date" name="from" value="{{ $from }}" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
      <input type="date" name="to" value="{{ $to }}" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
      <div class="md:col-span-5 flex items-center gap-2">
        <button type="submit" class="rounded-full bg-red-800 text-white px-6 py-2 text-sm font-semibold hover:bg-red-900 transition">{{ __('admin.filter') }}</button>
        <a href="{{ route('admin.reviews.index') }}" class="text-sm text-gray-500">{{ __('admin.reset') }}</a>
      </div>
    </form>
  </div>

  <div class="mt-5 grid grid-cols-1 gap-4">
    @forelse($reviews as $review)
      @php
        $placeName = $review->place?->display_name
          ?? $review->place?->name
          ?? $review->place?->name_en
          ?? $review->place?->title_en
          ?? $review->place?->name_ar
          ?? $review->place?->title_ar
          ?? '-';
      @endphp
      <div class="bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
        <div class="flex items-start justify-between gap-4">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-10 h-10 rounded-full bg-gray-100 flex-shrink-0"></div>
            <div class="min-w-0">
              <div class="text-sm font-semibold text-gray-900 truncate">{{ $review->user?->name ?? __('admin.user') }}</div>
              <div class="text-xs text-gray-500 mt-0.5">
                {{ $review->created_at?->diffForHumans() ?? '-' }} • {{ $placeName }} • {{ $review->branch?->name ?? '-' }}
              </div>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ $review->overall_rating ?? '-' }}/5</span>
            @if($review->is_hidden)
              <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700">{{ __('admin.hidden') }}</span>
            @endif
            @if($review->is_featured)
              <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700">{{ __('admin.featured') }}</span>
            @endif
          </div>
        </div>

        <div class="mt-3 text-sm text-gray-700 leading-relaxed">
          {{ \Illuminate\Support\Str::limit($review->comment ?? '', 180) }}
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2">
          <a href="{{ route('admin.reviews.show', $review) }}" class="text-xs font-semibold text-gray-600 border border-gray-200 rounded-full px-3 py-1 hover:border-gray-300">{{ __('admin.view') }}</a>
          <form method="POST" action="{{ route('admin.reviews.toggle-hide', $review) }}">
            @csrf
            <input type="hidden" name="reason" value="">
            <button type="submit" class="text-xs font-semibold {{ $review->is_hidden ? 'text-green-700 border-green-200' : 'text-red-700 border-red-200' }} border rounded-full px-3 py-1 hover:border-red-300">
              {{ $review->is_hidden ? __('admin.unhide') : __('admin.hide') }}
            </button>
          </form>
          <form method="POST" action="{{ route('admin.reviews.toggle-featured', $review) }}">
            @csrf
            <button type="submit" class="text-xs font-semibold {{ $review->is_featured ? 'text-yellow-700 border-yellow-200' : 'text-gray-600 border-gray-200' }} border rounded-full px-3 py-1 hover:border-yellow-300">
              {{ $review->is_featured ? __('admin.unfeature') : __('admin.feature') }}
            </button>
          </form>
          <button type="button" class="text-xs font-semibold text-gray-600 border border-gray-200 rounded-full px-3 py-1 hover:border-gray-300" data-reply-toggle="reply-{{ $review->id }}">
            {{ __('admin.reply') }}
          </button>
        </div>

        <div id="reply-{{ $review->id }}" class="mt-4 hidden">
          <form method="POST" action="{{ route('admin.reviews.reply', $review) }}" class="space-y-2">
            @csrf
            <textarea name="admin_reply_text" rows="3" class="w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200" placeholder="{{ __('admin.write_reply') }}"></textarea>
            <button type="submit" class="rounded-full bg-red-800 text-white px-5 py-2 text-sm font-semibold hover:bg-red-900 transition">{{ __('admin.send_reply') }}</button>
          </form>
        </div>
      </div>
    @empty
      <div class="bg-white border border-gray-100 rounded-[24px] p-10 text-center text-gray-500 shadow-soft">
        {{ __('admin.no_reviews_found') }}
      </div>
    @endforelse
  </div>

  <div class="mt-6">
    {{ $reviews->links() }}
  </div>
@endsection

@push('scripts')
  <script>
    (function(){
      document.querySelectorAll('[data-reply-toggle]').forEach(function(btn){
        btn.addEventListener('click', function(){
          const id = btn.getAttribute('data-reply-toggle');
          const el = document.getElementById(id);
          if (el) el.classList.toggle('hidden');
        });
      });

      function buildExportUrl(base){
        const params = new URLSearchParams(window.location.search);
        const qs = params.toString();
        return qs ? (base + '?' + qs) : base;
      }
      const csvBtn = document.getElementById('exportReviewsCsv');
      const pdfBtn = document.getElementById('exportReviewsPdf');
      csvBtn?.addEventListener('click', function(){
        window.location.href = buildExportUrl(@json(route('admin.reviews.export.csv')));
      });
      pdfBtn?.addEventListener('click', function(){
        window.location.href = buildExportUrl(@json(route('admin.reviews.export.pdf')));
      });
    })();
  </script>
@endpush
