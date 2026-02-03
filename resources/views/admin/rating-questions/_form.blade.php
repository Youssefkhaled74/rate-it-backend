@php
  $isEdit = !empty($question);
  $type = old('type', $question->type ?? 'RATING');
  $choicesEn = old('choices_en', $isEdit && $question->type === 'MULTIPLE_CHOICE' ? $question->choices->pluck('choice_en')->filter()->values()->toArray() : []);
  $choicesAr = old('choices_ar', $isEdit && $question->type === 'MULTIPLE_CHOICE' ? $question->choices->pluck('choice_ar')->filter()->values()->toArray() : []);
  if (empty($choicesEn)) $choicesEn = ['',''];
@endphp

<div class="space-y-6">

  <div>
    <label class="text-sm font-medium text-gray-700">{{ __('admin.question_text') }}</label>
    <input
      name="question_text"
      value="{{ old('question_text', $question->question_en ?? $question->question_text ?? '') }}"
      placeholder="{{ __('admin.question_text') }}"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
      required
    >
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">{{ __('admin.question_text_ar') }}</label>
    <input
      name="question_ar"
      value="{{ old('question_ar', $question->question_ar ?? '') }}"
      placeholder="{{ __('admin.question_text_ar') }}"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
    >
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.subcategories') }}</label>
      <select name="subcategory_id" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                    focus:border-red-300 focus:ring-4 focus:ring-red-100" required>
        <option value="">{{ __('admin.subcategories') }}</option>
        @foreach($subcategories as $sub)
          <option value="{{ $sub->id }}" {{ (string) old('subcategory_id', $question->subcategory_id ?? '') === (string) $sub->id ? 'selected' : '' }}>
            {{ $sub->name_en }} {{ $sub->category?->name_en ? '— ' . $sub->category->name_en : '' }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.type') }}</label>
      <select name="type" id="question_type" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                    focus:border-red-300 focus:ring-4 focus:ring-red-100" required>
        <option value="RATING" {{ $type === 'RATING' ? 'selected' : '' }}>{{ __('admin.rating') }} (1–5)</option>
        <option value="YES_NO" {{ $type === 'YES_NO' ? 'selected' : '' }}>{{ __('admin.yes_no') }}</option>
        <option value="MULTIPLE_CHOICE" {{ $type === 'MULTIPLE_CHOICE' ? 'selected' : '' }}>{{ __('admin.multiple_choice') }}</option>
      </select>
    </div>
  </div>

  <div id="choices_block" class="{{ $type === 'MULTIPLE_CHOICE' ? '' : 'hidden' }}">
    <div class="flex items-center justify-between">
      <label class="text-sm font-medium text-gray-700">{{ __('admin.choices') }}</label>
      <button type="button" id="add_choice_btn"
              class="text-xs font-semibold text-red-900 hover:text-red-950">
        + {{ __('admin.add') }}
      </button>
    </div>

    <div id="choices_list" class="mt-3 space-y-3">
      @foreach($choicesEn as $i => $choiceEn)
        <div class="choice-row grid grid-cols-1 sm:grid-cols-2 gap-3">
          <input
            name="choices_en[]"
            value="{{ $choiceEn }}"
            placeholder="{{ __('admin.choices_en') }}"
            class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                   focus:border-red-300 focus:ring-4 focus:ring-red-100"
          >
          <div class="flex gap-2">
            <input
              name="choices_ar[]"
              value="{{ $choicesAr[$i] ?? '' }}"
              placeholder="{{ __('admin.choices_ar') }}"
              class="flex-1 rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                     focus:border-red-300 focus:ring-4 focus:ring-red-100"
            >
            <button type="button"
                    class="remove-choice w-11 h-11 rounded-2xl bg-red-50 border border-red-100 text-red-700 hover:bg-red-100">
              ✕
            </button>
          </div>
        </div>
      @endforeach
    </div>

    <div class="text-xs text-gray-500 mt-2">{{ __('admin.choices') }}: {{ __('admin.add') }} 2+</div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.sort_order') }}</label>
      <input
        name="sort_order"
        type="number"
        value="{{ old('sort_order', $question->sort_order ?? 0) }}"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
    </div>

    <div class="flex items-end gap-6">
      <label class="inline-flex items-center gap-3 cursor-pointer select-none">
        <input type="checkbox" name="is_required" value="1"
               {{ old('is_required', $question->is_required ?? 0) ? 'checked' : '' }}
               class="sr-only peer">

        <span class="w-12 h-6 rounded-full bg-gray-200 peer-checked:bg-green-500 transition relative">
          <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white transition peer-checked:translate-x-6"></span>
        </span>

        <span class="text-sm text-gray-700">
          <span class="font-semibold">{{ __('admin.required') }}</span>
        </span>
      </label>

      <label class="inline-flex items-center gap-3 cursor-pointer select-none">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', $question->is_active ?? 1) ? 'checked' : '' }}
               class="sr-only peer">

        <span class="w-12 h-6 rounded-full bg-gray-200 peer-checked:bg-green-500 transition relative">
          <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white transition peer-checked:translate-x-6"></span>
        </span>

        <span class="text-sm text-gray-700">
          <span class="font-semibold">{{ __('admin.active') }}</span>
        </span>
      </label>
    </div>
  </div>
</div>

<script>
  (function(){
    const typeSelect = document.getElementById('question_type');
    const block = document.getElementById('choices_block');
    if (!typeSelect || !block) return;
    const toggle = () => {
      if (typeSelect.value === 'MULTIPLE_CHOICE') block.classList.remove('hidden');
      else block.classList.add('hidden');
    };
    typeSelect.addEventListener('change', toggle);
    toggle();

    const list = document.getElementById('choices_list');
    const addBtn = document.getElementById('add_choice_btn');
    const rowHtml = () => `
      <div class="choice-row grid grid-cols-1 sm:grid-cols-2 gap-3">
        <input name="choices_en[]" placeholder="{{ __('admin.choices_en') }}"
               class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                      focus:border-red-300 focus:ring-4 focus:ring-red-100">
        <div class="flex gap-2">
          <input name="choices_ar[]" placeholder="{{ __('admin.choices_ar') }}"
                 class="flex-1 rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                        focus:border-red-300 focus:ring-4 focus:ring-red-100">
          <button type="button"
                  class="remove-choice w-11 h-11 rounded-2xl bg-red-50 border border-red-100 text-red-700 hover:bg-red-100">✕</button>
        </div>
      </div>
    `;

    if (addBtn && list) {
      addBtn.addEventListener('click', () => {
        list.insertAdjacentHTML('beforeend', rowHtml());
      });
      list.addEventListener('click', (e) => {
        const btn = e.target.closest('.remove-choice');
        if (!btn) return;
        const row = btn.closest('.choice-row');
        if (!row) return;
        row.remove();
      });
    }
  })();
</script>
