@extends('admin.layouts.app')

@section('page_title', __('admin.rewards_system'))
@section('title', __('admin.rewards_system'))

@section('content')
  <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <div>
        <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.rewards_system') }}</h2>
        <div class="text-sm text-gray-500 mt-1">{{ __('admin.rewards_system_hint') }}</div>
      </div>
    </div>

    @if(session('success'))
      <div class="mt-4 rounded-2xl bg-green-50 border border-green-100 text-green-700 text-sm px-4 py-3">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="mt-4 rounded-2xl bg-red-50 border border-red-100 text-red-700 text-sm px-4 py-3">
        {{ session('error') }}
      </div>
    @endif

    <div class="mt-5 grid grid-cols-1 xl:grid-cols-2 gap-6">
      <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
        <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('admin.active_rules') }}</div>
        @if($activeSetting)
          <div class="mt-2 text-lg font-semibold text-gray-900">
            {{ __('admin.rules_version') }} #{{ $activeSetting->version }}
          </div>
          <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-700">
            <div>{{ __('admin.points_per_review') }}: <span class="font-semibold">{{ $activeSetting->points_per_review }}</span></div>
            <div>{{ __('admin.invite_points_per_friend') }}: <span class="font-semibold">{{ $activeSetting->invite_points_per_friend }}</span></div>
            <div>{{ __('admin.invitee_bonus_points') }}: <span class="font-semibold">{{ $activeSetting->invitee_bonus_points }}</span></div>
            <div>{{ __('admin.point_value_money') }}: <span class="font-semibold">{{ number_format((float)$activeSetting->point_value_money, 2) }} {{ $activeSetting->currency }}</span></div>
            <div>{{ __('admin.points_expiry_days') }}: <span class="font-semibold">{{ $activeSetting->points_expiry_days ?? __('admin.not_set') }}</span></div>
          </div>
        @else
          <div class="mt-2 text-sm text-gray-500">{{ __('admin.no_active_rules') }}</div>
        @endif
      </div>

      <div class="rounded-2xl border border-gray-100 bg-white p-5">
        <div class="text-sm font-semibold text-gray-900">{{ __('admin.create_new_rules') }}</div>
        <form method="POST" action="{{ route('admin.rewards.settings.store') }}" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
          @csrf
          <div>
            <label class="text-xs text-gray-500">{{ __('admin.points_per_review') }}</label>
            <input name="points_per_review" type="number" min="0" value="{{ old('points_per_review', 0) }}"
              class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200" required>
          </div>
          <div>
            <label class="text-xs text-gray-500">{{ __('admin.invite_points_per_friend') }}</label>
            <input name="invite_points_per_friend" type="number" min="0" value="{{ old('invite_points_per_friend', 50) }}"
              class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200" required>
          </div>
          <div>
            <label class="text-xs text-gray-500">{{ __('admin.invitee_bonus_points') }}</label>
            <input name="invitee_bonus_points" type="number" min="0" value="{{ old('invitee_bonus_points', 0) }}"
              class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200" required>
          </div>
          <div>
            <label class="text-xs text-gray-500">{{ __('admin.points_expiry_days') }}</label>
            <input name="points_expiry_days" type="number" min="0" value="{{ old('points_expiry_days') }}"
              class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
          </div>
          <div>
            <label class="text-xs text-gray-500">{{ __('admin.point_value_money') }}</label>
            <input name="point_value_money" type="number" min="0" step="0.01" value="{{ old('point_value_money', 0) }}"
              class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200" required>
          </div>
          <div>
            <label class="text-xs text-gray-500">{{ __('admin.currency') }}</label>
            <input name="currency" value="{{ old('currency', 'EGP') }}"
              class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200" required>
          </div>
          <div class="md:col-span-2 flex items-center justify-between gap-3">
            <label class="inline-flex items-center gap-2 text-sm text-gray-600">
              <input type="checkbox" name="activate_now" value="1" class="rounded border-gray-300">
              {{ __('admin.activate_now') }}
            </label>
            <button type="submit" class="rounded-full bg-red-800 text-white px-6 py-2 text-sm font-semibold hover:bg-red-900 transition">
              {{ __('admin.save_changes') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="mt-6 bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
    <div class="flex items-center justify-between">
      <div class="text-lg font-semibold text-gray-900">{{ __('admin.rules_history') }}</div>
    </div>

    <div class="mt-4 space-y-3">
      @foreach($settings as $s)
        <div class="rounded-2xl border border-gray-100 p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
          <div class="text-sm text-gray-700">
            <div class="font-semibold text-gray-900">{{ __('admin.rules_version') }} #{{ $s->version }}</div>
            <div class="mt-1 text-xs text-gray-500">
              {{ __('admin.points_per_review') }}: {{ $s->points_per_review }} ·
              {{ __('admin.invite_points_per_friend') }}: {{ $s->invite_points_per_friend }} ·
              {{ __('admin.invitee_bonus_points') }}: {{ $s->invitee_bonus_points }} ·
              {{ __('admin.point_value_money') }}: {{ number_format((float)$s->point_value_money, 2) }} {{ $s->currency }} ·
              {{ __('admin.points_expiry_days') }}: {{ $s->points_expiry_days ?? __('admin.not_set') }}
            </div>
          </div>

          <div class="flex items-center gap-2">
            @if($s->is_active)
              <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">{{ __('admin.active') }}</span>
            @else
              <form method="POST" action="{{ route('admin.rewards.settings.activate', $s) }}">
                @csrf
                <button type="submit" class="px-3 py-2 rounded-full border border-emerald-200 text-emerald-700 text-xs font-semibold hover:bg-emerald-50 transition">
                  {{ __('admin.activate') }}
                </button>
              </form>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-4">
      {{ $settings->links() }}
    </div>
  </div>

  <div class="mt-6 bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
    <div class="flex items-center justify-between">
      <div class="text-lg font-semibold text-gray-900">{{ __('admin.user_levels') }}</div>
      <a href="#levels-form" class="text-sm text-gray-500">{{ __('admin.add_level') }}</a>
    </div>

    <form id="levels-form" method="POST" action="{{ route('admin.rewards.levels.store') }}" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
      @csrf
      <div>
        <label class="text-xs text-gray-500">{{ __('admin.level_name') }}</label>
        <input name="name" value="{{ old('name') }}"
          class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200" required>
      </div>
      <div>
        <label class="text-xs text-gray-500">{{ __('admin.min_reviews') }}</label>
        <input name="min_reviews" type="number" min="0" value="{{ old('min_reviews', 0) }}"
          class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200" required>
      </div>
      <div>
        <label class="text-xs text-gray-500">{{ __('admin.bonus_percent') }}</label>
        <input name="bonus_percent" type="number" min="0" step="0.01" value="{{ old('bonus_percent', 0) }}"
          class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        <div class="text-[11px] text-gray-500 mt-1">{{ __('admin.bonus_percent') }} = % {{ __('admin.points') }}</div>
      </div>
      <div class="md:col-span-3">
        <label class="text-xs text-gray-500">{{ __('admin.level_benefits') }}</label>
        <div class="mt-2 rounded-2xl border border-gray-200 p-3 bg-gray-50">
          <div class="flex flex-col md:flex-row gap-2">
            <input id="benefit_input" type="text"
              class="flex-1 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200"
              placeholder="{{ __('admin.benefits_placeholder') }}">
            <button id="benefit_add_btn" type="button"
              class="rounded-xl bg-gray-900 text-white px-4 py-2 text-xs font-semibold hover:bg-black transition">
              + {{ __('admin.add') }}
            </button>
          </div>
          <div id="benefits_list" class="mt-3 flex flex-wrap gap-2"></div>
        </div>
        <textarea id="benefits_textarea" name="benefits_text" rows="3" class="hidden">{{ old('benefits_text') }}</textarea>
        <div class="text-[11px] text-gray-500 mt-1">{{ __('admin.benefits_placeholder') }}</div>
      </div>
      <div class="md:col-span-3">
        <button type="submit" class="rounded-full bg-red-800 text-white px-6 py-2 text-sm font-semibold hover:bg-red-900 transition">
          {{ __('admin.add_level') }}
        </button>
      </div>
    </form>

    @if($levels->count())
      <form id="levels-bulk-form" method="POST" action="{{ route('admin.rewards.levels.bulk-destroy') }}"
            data-confirm="{{ __('admin.confirm_delete_levels') }}"
            onsubmit="return confirm(this.dataset.confirm)">
        @csrf
        @method('DELETE')
        <div class="mt-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
          <label class="inline-flex items-center gap-2 text-sm text-gray-600">
            <input id="levels_select_all" type="checkbox" class="rounded border-gray-300">
            {{ __('admin.select_all') }}
          </label>
          <button id="levels_delete_selected" type="submit" disabled
                  class="px-4 py-2 rounded-full border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-50 transition disabled:opacity-50 disabled:cursor-not-allowed">
            {{ __('admin.delete_selected') }}
            <span id="levels_selected_count" class="ml-2 inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-[11px] font-semibold text-red-600">0</span>
          </button>
        </div>
      </form>

      <div class="mt-4 space-y-3">
        @foreach($levels as $lvl)
          <div class="rounded-2xl border border-gray-100 p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start gap-3">
              <input type="checkbox" name="level_ids[]" form="levels-bulk-form" value="{{ $lvl->id }}" class="mt-1 rounded border-gray-300 level-checkbox">
              <div>
                <div class="text-sm font-semibold text-gray-900">{{ $lvl->name }}</div>
                <div class="text-xs text-gray-500 mt-1">
                  {{ __('admin.min_reviews') }}: {{ $lvl->min_reviews }} ·
                  {{ __('admin.bonus_percent') }}: {{ number_format((float)($lvl->bonus_percent ?? 0), 2) }}%
                </div>
                @if(!empty($lvl->benefits))
                  <ul class="mt-2 text-xs text-gray-600 list-disc list-inside">
                    @foreach($lvl->benefits as $b)
                      <li>{{ $b }}</li>
                    @endforeach
                  </ul>
                @endif
              </div>
            </div>
            <div class="flex items-center gap-2">
              <a href="{{ route('admin.rewards.levels.edit', $lvl) }}"
                 class="px-3 py-2 rounded-full border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition">
                {{ __('admin.edit') }}
              </a>
              <form method="POST" action="{{ route('admin.rewards.levels.destroy', $lvl) }}"
                    data-confirm="{{ __('admin.confirm_delete_level') }}"
                    onsubmit="return confirm(this.dataset.confirm)">
                @csrf
                @method('DELETE')
                <button type="submit"
                  class="px-3 py-2 rounded-full border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-50 transition">
                  {{ __('admin.delete') }}
                </button>
              </form>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="mt-5 rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center">
        <div class="text-sm font-semibold text-gray-900">{{ __('admin.no_levels') }}</div>
        <div class="mt-1 text-xs text-gray-500">{{ __('admin.no_levels_hint') }}</div>
        <a href="#levels-form"
           class="mt-4 inline-flex items-center justify-center rounded-full bg-red-800 text-white px-5 py-2 text-xs font-semibold hover:bg-red-900 transition">
          {{ __('admin.add_level') }}
        </a>
      </div>
    @endif
  </div>
@endsection

@push('scripts')
<script>
  (function () {
    const input = document.getElementById('benefit_input');
    const addBtn = document.getElementById('benefit_add_btn');
    const list = document.getElementById('benefits_list');
    const textarea = document.getElementById('benefits_textarea');
    if (!input || !addBtn || !list || !textarea) return;

    function renderFromTextarea() {
      list.innerHTML = '';
      const lines = (textarea.value || '').split(/\r?\n/).map(s => s.trim()).filter(Boolean);
      lines.forEach(addChip);
      syncTextarea();
    }

    function addChip(text) {
      const chip = document.createElement('span');
      chip.className = 'inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-gray-200 text-xs text-gray-700';
      chip.innerHTML = `<span>${text}</span><button type="button" class="text-gray-400 hover:text-red-600">×</button>`;
      chip.querySelector('button').addEventListener('click', function () {
        chip.remove();
        syncTextarea();
      });
      list.appendChild(chip);
    }

    function syncTextarea() {
      const values = Array.from(list.querySelectorAll('span > span')).map(el => el.textContent.trim()).filter(Boolean);
      textarea.value = values.join('\n');
    }

    function addFromInput() {
      const value = (input.value || '').trim();
      if (!value) return;
      addChip(value);
      syncTextarea();
      input.value = '';
      input.focus();
    }

    addBtn.addEventListener('click', addFromInput);
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        addFromInput();
      }
    });

    renderFromTextarea();

    const selectAll = document.getElementById('levels_select_all');
    const deleteBtn = document.getElementById('levels_delete_selected');
    const selectedCount = document.getElementById('levels_selected_count');
    const checkboxes = Array.from(document.querySelectorAll('.level-checkbox'));

    function syncBulkState() {
      const checked = checkboxes.filter(cb => cb.checked);
      if (deleteBtn) deleteBtn.disabled = checked.length === 0;
      if (selectedCount) selectedCount.textContent = checked.length;
      if (selectAll) selectAll.checked = checked.length > 0 && checked.length === checkboxes.length;
    }

    if (selectAll) {
      selectAll.addEventListener('change', function () {
        checkboxes.forEach(cb => { cb.checked = selectAll.checked; });
        syncBulkState();
      });
    }

    checkboxes.forEach(cb => cb.addEventListener('change', syncBulkState));
    syncBulkState();
  })();
</script>
@endpush


