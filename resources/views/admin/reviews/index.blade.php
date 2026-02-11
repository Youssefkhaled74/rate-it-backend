@extends('admin.layouts.app')

@section('page_title', __('admin.reviews'))
@section('title', __('admin.reviews'))

@section('content')
  @php
    $isArabic = app()->getLocale() === 'ar' || request('lang') === 'ar';
  @endphp
  <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.reviews') }}</h2>
        <div class="text-sm text-gray-500 mt-1">{{ __('admin.reviews_subtitle') }}</div>
      </div>
      <div class="flex items-center gap-2">
        <button type="button" id="exportReviewsCsv" data-export-base="{{ route('admin.reviews.export.csv') }}" class="h-9 rounded-full border border-gray-200 inline-flex items-center gap-2 px-3 text-gray-600 hover:border-gray-300" title="{{ __('admin.export_excel') }}">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <path d="M14 2v6h6"/>
            <path d="M9 12l6 6M15 12l-6 6"/>
          </svg>
          <span class="text-xs font-semibold">{{ __('admin.export_excel') }}</span>
        </button>
        <button type="button" id="exportReviewsPdf" data-export-base="{{ route('admin.reviews.export.pdf') }}" class="h-9 rounded-full border border-red-200 inline-flex items-center gap-2 px-3 text-red-700 hover:border-red-300" title="{{ __('admin.export_pdf') }}">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <path d="M14 2v6h6"/>
            <path d="M8 13h4a2 2 0 0 1 0 4H8z"/>
            <path d="M14 17v-4h2a2 2 0 0 1 0 4h-2z"/>
          </svg>
          <span class="text-xs font-semibold">{{ __('admin.export_pdf') }}</span>
        </button>
      </div>
    </div>

    <form method="GET" class="mt-5 grid grid-cols-1 md:grid-cols-7 gap-3">
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

      <select name="brand_id" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        <option value="">{{ __('admin.all_brands') }}</option>
        @foreach($brands as $brand)
          @php
            $brandLabel = $isArabic
              ? ($brand->name_ar ?: $brand->name_en ?: $brand->name ?? '-')
              : ($brand->name_en ?: $brand->name_ar ?: $brand->name ?? '-');
          @endphp
          <option value="{{ $brand->id }}" {{ (string)$brandId === (string)$brand->id ? 'selected' : '' }}>
            {{ $brandLabel }}
          </option>
        @endforeach
      </select>

      <select name="branch_id" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        <option value="">{{ __('admin.all_branches') }}</option>
        @foreach($branches as $branch)
          @php
            $branchLabel = $isArabic
              ? ($branch->name_ar ?? $branch->name ?? '-')
              : ($branch->name ?? $branch->name_ar ?? '-');
          @endphp
          <option value="{{ $branch->id }}" {{ (string)$branchId === (string)$branch->id ? 'selected' : '' }}>
            {{ $branchLabel }}
          </option>
        @endforeach
      </select>

      <input type="date" name="from" value="{{ $from }}" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
      <input type="date" name="to" value="{{ $to }}" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">

      <div class="md:col-span-7 flex items-center gap-2">
        <button type="submit" class="rounded-full bg-red-800 text-white px-6 py-2 text-sm font-semibold hover:bg-red-900 transition">{{ __('admin.filter') }}</button>
        <a href="{{ route('admin.reviews.index', ['lang' => request('lang')]) }}" class="text-sm text-gray-500">{{ __('admin.reset') }}</a>
      </div>
    </form>
  </div>

  <div class="mt-5 grid grid-cols-1 gap-4">
    @forelse($reviews as $review)
      @php
        $userName = trim((string) ($review->user?->name ?? __('admin.user')));
        $avatarPath = $review->user?->avatar_path
          ?? $review->user?->avatar
          ?? $review->user?->photo_path
          ?? $review->user?->picture
          ?? null;
        $avatarUrl = $avatarPath ? asset($avatarPath) : null;
        $initial = mb_strtoupper(mb_substr($userName, 0, 1));
        $placeName = $isArabic
          ? ($review->place?->display_name
            ?? $review->place?->name_ar
            ?? $review->place?->title_ar
            ?? $review->place?->name
            ?? $review->place?->name_en
            ?? $review->place?->title_en
            ?? '-')
          : ($review->place?->display_name
            ?? $review->place?->name
            ?? $review->place?->name_en
            ?? $review->place?->title_en
            ?? $review->place?->name_ar
            ?? $review->place?->title_ar
            ?? '-');
        $reviewBrandName = $isArabic
          ? ($review->branch?->brand?->name_ar ?? $review->branch?->brand?->name_en ?? '-')
          : ($review->branch?->brand?->name_en ?? $review->branch?->brand?->name_ar ?? '-');
        $reviewBranchName = $isArabic
          ? ($review->branch?->name_ar ?? $review->branch?->name ?? '-')
          : ($review->branch?->name ?? $review->branch?->name_ar ?? '-');
      @endphp
      <div class="bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
        <div class="flex items-start justify-between gap-4">
          <div class="flex items-start gap-3 min-w-0">
            <div class="w-10 h-10 rounded-full bg-gray-100 flex-shrink-0 overflow-hidden grid place-items-center text-xs font-semibold text-gray-700">
              @if($avatarUrl)
                <img src="{{ $avatarUrl }}"
                     alt="{{ $userName }}"
                     class="w-full h-full object-cover"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';">
                <span style="display:none" class="w-full h-full place-items-center bg-red-50 text-red-700">{{ $initial }}</span>
              @else
                <span class="w-full h-full grid place-items-center bg-red-50 text-red-700">{{ $initial }}</span>
              @endif
            </div>
            <div class="min-w-0">
              <div class="text-sm font-semibold text-gray-900 truncate">{{ $userName }}</div>
              <div class="text-xs text-gray-500 mt-0.5">
                {{ $review->created_at?->diffForHumans() ?? '-' }} • {{ $reviewBrandName }} • {{ $placeName }} • {{ $reviewBranchName }}
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

        @if($review->is_hidden && !empty($review->hidden_reason))
          <div class="mt-2 text-xs text-red-700 bg-red-50 border border-red-100 rounded-xl px-3 py-2 inline-flex items-center gap-1">
            <span class="font-semibold">Hidden reason:</span>
            <span>{{ $review->hidden_reason }}</span>
          </div>
        @endif

        <div class="mt-4 flex flex-wrap items-center gap-2">
          <span class="text-xs font-semibold text-gray-600 border border-gray-200 rounded-full px-3 py-1">Answers: {{ $review->answers_count ?? 0 }}</span>
          <span class="text-xs font-semibold text-gray-600 border border-gray-200 rounded-full px-3 py-1">Photos: {{ $review->photos_count ?? 0 }}</span>
          <a href="{{ route('admin.reviews.show', ['review' => $review, 'lang' => request('lang')]) }}" class="text-xs font-semibold text-indigo-700 border border-indigo-200 rounded-full px-3 py-1 hover:border-indigo-300">
            {{ __('admin.review_details') }}
          </a>
          <form id="hide-review-form-{{ $review->id }}" method="POST" action="{{ route('admin.reviews.toggle-hide', $review) }}">
            @csrf
            <input type="hidden" name="reason" id="hide-review-reason-{{ $review->id }}" value="">
            <button type="button"
                    data-hide-toggle
                    data-review-id="{{ $review->id }}"
                    data-is-hidden="{{ $review->is_hidden ? '1' : '0' }}"
                    class="text-xs font-semibold {{ $review->is_hidden ? 'text-green-700 border-green-200' : 'text-red-700 border-red-200' }} border rounded-full px-3 py-1 hover:border-red-300">
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

  <div id="hideReasonModal" class="hidden fixed inset-0 z-[70]">
    <div id="hideReasonOverlay" class="absolute inset-0 bg-gray-900/45 backdrop-blur-[1px]"></div>
    <div class="relative min-h-full flex items-center justify-center p-4">
      <div class="w-full max-w-lg rounded-2xl bg-white border border-gray-200 shadow-2xl p-5">
        <div class="text-base font-semibold text-gray-900">Hide Review</div>
        <div class="mt-1 text-sm text-gray-500">Please enter hidden reason:</div>
        <input id="hideReasonText" type="text" class="mt-3 w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-300" maxlength="1000">
        <div id="hideReasonError" class="mt-2 text-xs text-red-600 hidden">Hidden reason is required.</div>
        <div class="mt-4 flex items-center justify-end gap-2">
          <button type="button" id="hideReasonCancel" class="px-4 py-2 rounded-full border border-gray-200 text-sm font-semibold text-gray-600 hover:border-gray-300">Cancel</button>
          <button type="button" id="hideReasonSubmit" class="px-5 py-2 rounded-full bg-red-800 text-white text-sm font-semibold hover:bg-red-900">OK</button>
        </div>
      </div>
    </div>
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
        const base = csvBtn.getAttribute('data-export-base') || '';
        if (!base) return;
        window.location.href = buildExportUrl(base);
      });
      pdfBtn?.addEventListener('click', function(){
        const base = pdfBtn.getAttribute('data-export-base') || '';
        if (!base) return;
        window.location.href = buildExportUrl(base);
      });

      const modal = document.getElementById('hideReasonModal');
      const overlay = document.getElementById('hideReasonOverlay');
      const reasonInputEl = document.getElementById('hideReasonText');
      const reasonError = document.getElementById('hideReasonError');
      const cancelBtn = document.getElementById('hideReasonCancel');
      const submitBtn = document.getElementById('hideReasonSubmit');
      let pendingHide = null;

      function closeModal(){
        if (!modal) return;
        modal.classList.add('hidden');
        pendingHide = null;
        if (reasonInputEl) reasonInputEl.value = '';
        reasonError?.classList.add('hidden');
      }

      function openModal(form, reasonInput){
        if (!modal || !reasonInputEl) return;
        pendingHide = { form: form, reasonInput: reasonInput };
        modal.classList.remove('hidden');
        reasonInputEl.value = '';
        reasonError?.classList.add('hidden');
        setTimeout(function(){ reasonInputEl.focus(); }, 10);
      }

      function submitReason(){
        if (!pendingHide || !reasonInputEl) return;
        const cleaned = reasonInputEl.value.trim();
        if (!cleaned) {
          reasonError?.classList.remove('hidden');
          reasonInputEl.focus();
          return;
        }
        pendingHide.reasonInput.value = cleaned;
        pendingHide.form.submit();
      }

      submitBtn?.addEventListener('click', submitReason);
      cancelBtn?.addEventListener('click', closeModal);
      overlay?.addEventListener('click', closeModal);
      reasonInputEl?.addEventListener('keydown', function(e){
        if (e.key === 'Enter') {
          e.preventDefault();
          submitReason();
        }
      });
      document.addEventListener('keydown', function(e){
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeModal();
      });

      document.querySelectorAll('[data-hide-toggle]').forEach(function(btn){
        btn.addEventListener('click', function(){
          const reviewId = btn.getAttribute('data-review-id');
          const isHidden = btn.getAttribute('data-is-hidden') === '1';
          const form = document.getElementById('hide-review-form-' + reviewId);
          const reasonInput = document.getElementById('hide-review-reason-' + reviewId);
          if (!form || !reasonInput) return;

          if (isHidden) {
            reasonInput.value = '';
            form.submit();
            return;
          }

          openModal(form, reasonInput);
        });
      });
    })();
  </script>
@endpush
