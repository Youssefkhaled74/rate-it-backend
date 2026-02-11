@extends('admin.layouts.app')

@section('page_title', __('admin.reviews'))
@section('title', __('admin.review_details'))

@section('content')
  <div class="mb-4">
    <a href="{{ route('admin.reviews.index', ['lang' => request('lang')]) }}" class="text-xs font-semibold text-red-700">
      {{ __('admin.reviews') }}
    </a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
      <div class="flex items-center justify-between">
        <div class="text-sm font-semibold text-gray-900">{{ __('admin.review') }} #{{ $review->id }}</div>
        <div class="text-xs text-gray-500">{{ __('admin.created_at') }}: {{ $review->created_at?->format('Y-m-d H:i') ?? '-' }}</div>
      </div>

      <div class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
          <div class="text-xs text-gray-500">{{ __('admin.rating') }}</div>
          <div class="text-2xl font-semibold text-gray-900">{{ $review->overall_rating ?? '-' }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
          <div class="text-xs text-gray-500">{{ __('admin.status') }}</div>
          <div class="text-sm font-semibold text-gray-900">{{ $review->status ?? '-' }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
          <div class="text-xs text-gray-500">Answers</div>
          <div class="text-2xl font-semibold text-gray-900">{{ $review->answers?->count() ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
          <div class="text-xs text-gray-500">{{ __('admin.photos') }}</div>
          <div class="text-2xl font-semibold text-gray-900">{{ $review->photos?->count() ?? 0 }}</div>
        </div>
      </div>

      <div class="mt-5 rounded-2xl border border-gray-200 bg-gray-50 p-4">
        <div class="text-sm font-semibold text-gray-900">Moderation Actions</div>
        <div class="mt-3 flex flex-wrap items-center gap-2">
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
        </div>
        <form method="POST" action="{{ route('admin.reviews.reply', $review) }}" class="mt-3 space-y-2">
          @csrf
          <textarea name="admin_reply_text" rows="3" class="w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200" placeholder="{{ __('admin.write_reply') }}">{{ old('admin_reply_text', $review->admin_reply_text) }}</textarea>
          <button type="submit" class="rounded-full bg-red-800 text-white px-5 py-2 text-sm font-semibold hover:bg-red-900 transition">{{ __('admin.send_reply') }}</button>
        </form>
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

      @if($review->is_hidden)
        <div class="mt-5">
          <div class="text-sm font-semibold text-gray-900">Hidden Reason</div>
          <div class="mt-2 text-sm text-red-700 leading-relaxed">
            {{ $review->hidden_reason ?: '-' }}
          </div>
          @if($review->hidden_at)
            <div class="mt-2 text-xs text-gray-400">{{ $review->hidden_at?->format('Y-m-d H:i') }}</div>
          @endif
        </div>
      @endif

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

      <div class="mt-6">
        <div class="text-sm font-semibold text-gray-900">User Answers</div>
        @if($review->answers && $review->answers->count())
          <div class="mt-3 space-y-3">
            @foreach($review->answers as $answer)
              @php
                $type = strtoupper((string) ($answer->criteria?->type ?? ''));
                $question = $answer->criteria?->question_en
                  ?? $answer->criteria?->question_ar
                  ?? $answer->criteria?->question_text
                  ?? 'Question';

                $value = '-';
                if ($type === 'RATING') {
                  $value = $answer->rating_value !== null ? ($answer->rating_value . ' / 5') : '-';
                } elseif ($type === 'YES_NO') {
                  $value = $answer->yes_no_value === null ? '-' : ($answer->yes_no_value ? 'Yes' : 'No');
                } elseif ($type === 'MULTIPLE_CHOICE') {
                  $value = $answer->choice?->choice_en
                    ?? $answer->choice?->choice_ar
                    ?? $answer->choice?->choice_text
                    ?? '-';
                } else {
                  $value = $answer->text_value ?: '-';
                }
              @endphp
              <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <div class="text-xs text-gray-500">{{ $type ?: 'ANSWER' }}</div>
                    <div class="mt-1 text-sm font-semibold text-gray-900">{{ $question }}</div>
                  </div>
                  <span class="text-xs font-semibold px-3 py-1 rounded-full bg-white border border-gray-200 text-gray-700">{{ $value }}</span>
                </div>
                @if($answer->photos && $answer->photos->count())
                  <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($answer->photos as $ap)
                      <div class="w-full h-16 rounded-lg overflow-hidden border border-gray-100 bg-white">
                        <img src="{{ asset($ap->storage_path ?? '') }}" class="w-full h-full object-cover" alt="Answer photo">
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        @else
          <div class="mt-2 text-sm text-gray-500">No answers found for this review.</div>
        @endif
      </div>

      <div class="mt-6">
        <div class="text-sm font-semibold text-gray-900">Moderation Timeline</div>
        <div class="mt-3 space-y-2 text-sm text-gray-700">
          <div class="rounded-xl border border-gray-100 bg-gray-50 px-3 py-2">
            Created at: {{ $review->created_at?->format('Y-m-d H:i') ?? '-' }}
          </div>
          <div class="rounded-xl border border-gray-100 bg-gray-50 px-3 py-2">
            Hidden: {{ $review->is_hidden ? 'Yes' : 'No' }}
            @if($review->hidden_at)
              ({{ $review->hidden_at?->format('Y-m-d H:i') }})
            @endif
            @if($review->hiddenByAdmin)
              by {{ $review->hiddenByAdmin->name ?? $review->hiddenByAdmin->email ?? ('#' . $review->hiddenByAdmin->id) }}
            @endif
          </div>
          <div class="rounded-xl border border-gray-100 bg-gray-50 px-3 py-2">
            Featured: {{ $review->is_featured ? 'Yes' : 'No' }}
            @if($review->featured_at)
              ({{ $review->featured_at?->format('Y-m-d H:i') }})
            @endif
            @if($review->featuredByAdmin)
              by {{ $review->featuredByAdmin->name ?? $review->featuredByAdmin->email ?? ('#' . $review->featuredByAdmin->id) }}
            @endif
          </div>
          <div class="rounded-xl border border-gray-100 bg-gray-50 px-3 py-2">
            Replied: {{ $review->replied_at ? 'Yes' : 'No' }}
            @if($review->replied_at)
              ({{ $review->replied_at?->format('Y-m-d H:i') }})
            @endif
            @if($review->repliedByAdmin)
              by {{ $review->repliedByAdmin->name ?? $review->repliedByAdmin->email ?? ('#' . $review->repliedByAdmin->id) }}
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
      <div class="text-sm font-semibold text-gray-900">{{ __('admin.user') }}</div>
      <div class="mt-3">
        <div class="text-sm font-semibold text-gray-900">{{ $review->user?->name ?? '-' }}</div>
        <div class="text-xs text-gray-500">{{ $review->user?->phone ?? '-' }}</div>
        <div class="text-xs text-gray-500">{{ $review->user?->email ?? '-' }}</div>
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
      <div class="mt-6 text-sm font-semibold text-gray-900">Brand</div>
      <div class="mt-2 text-sm text-gray-700">{{ $review->branch?->brand?->name_en ?? $review->branch?->brand?->name_ar ?? '-' }}</div>
    </div>
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
    (function () {
      const hideToggle = document.querySelector('[data-hide-toggle]');
      if (!hideToggle) return;

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

      hideToggle.addEventListener('click', function () {
        const reviewId = hideToggle.getAttribute('data-review-id');
        const isHidden = hideToggle.getAttribute('data-is-hidden') === '1';
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
    })();
  </script>
@endpush
