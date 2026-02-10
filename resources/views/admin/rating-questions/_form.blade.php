@php
  $isEdit = !empty($question);
  $type = old('type', $question->type ?? 'RATING');
  $choicesEn = old('choices_en', $isEdit && $question->type === 'MULTIPLE_CHOICE' ? $question->choices->pluck('choice_en')->filter()->values()->toArray() : []);
  $choicesAr = old('choices_ar', $isEdit && $question->type === 'MULTIPLE_CHOICE' ? $question->choices->pluck('choice_ar')->filter()->values()->toArray() : []);
  $choicesValue = old('choices_value', $isEdit && $question->type === 'MULTIPLE_CHOICE' ? $question->choices->pluck('value')->values()->toArray() : []);
  $choicesWeight = old('choices_weight', $isEdit && $question->type === 'MULTIPLE_CHOICE' ? $question->choices->pluck('weight')->values()->toArray() : []);
  if (empty($choicesEn)) $choicesEn = ['',''];
  if (empty($choicesValue)) $choicesValue = array_fill(0, count($choicesEn), null);
  if (empty($choicesWeight)) $choicesWeight = array_fill(0, count($choicesEn), 1);
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
    <x-admin.select
      name="subcategory_id"
      :label="__('admin.subcategories')"
      :placeholder="__('admin.subcategories')"
      :required="true"
    >
      @foreach($subcategories as $sub)
        <option value="{{ $sub->id }}" {{ (string) old('subcategory_id', $question->subcategory_id ?? '') === (string) $sub->id ? 'selected' : '' }}>
          {{ $sub->name_en }} {{ $sub->category?->name_en ? 'â€” ' . $sub->category->name_en : '' }}
        </option>
      @endforeach
    </x-admin.select>

    <x-admin.select
      name="type"
      id="question_type"
      :label="__('admin.type')"
      :required="true"
    >
      <option value="RATING" {{ $type === 'RATING' ? 'selected' : '' }}>{{ __('admin.rating') }} (1-5)</option>
      <option value="YES_NO" {{ $type === 'YES_NO' ? 'selected' : '' }}>{{ __('admin.yes_no') }}</option>
      <option value="MULTIPLE_CHOICE" {{ $type === 'MULTIPLE_CHOICE' ? 'selected' : '' }}>{{ __('admin.multiple_choice') }}</option>
      <option value="TEXT" {{ $type === 'TEXT' ? 'selected' : '' }}>{{ __('admin.text') }}</option>
      <option value="PHOTO" {{ $type === 'PHOTO' ? 'selected' : '' }}>{{ __('admin.photo_upload') }}</option>
    </x-admin.select>

  </div>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.weight') }}</label>
      <input
        name="weight"
        type="number"
        step="0.01"
        min="0"
        value="{{ old('weight', $question->weight ?? 0) }}"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
      <div class="text-xs text-gray-500 mt-1">{{ __('admin.weight_hint') }}</div>
    </div>
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.points') }}</label>
      <input
        name="points"
        type="number"
        min="0"
        value="{{ old('points', $question->points ?? 0) }}"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
      <div class="text-xs text-gray-500 mt-1">{{ __('admin.points_hint') }}</div>
    </div>
  </div>

  <div id="choices_block" class="{{ $type === 'MULTIPLE_CHOICE' ? '' : 'hidden' }}">
    <div class="flex items-center justify-between">
      <div>
        <label class="text-sm font-medium text-gray-700">{{ __('admin.choices') }}</label>
        <div class="text-xs text-gray-500 mt-1">{{ __('admin.add_at_least_two_choices') }}</div>
      </div>
      <button type="button" id="add_choice_btn"
              class="text-xs font-semibold text-red-900 hover:text-red-950">
        + {{ __('admin.add') }}
      </button>
    </div>

    <div class="mt-3 hidden lg:grid lg:grid-cols-12 gap-3 text-[11px] uppercase tracking-wide text-gray-400">
      <div class="lg:col-span-4">{{ __('admin.choices_en') }}</div>
      <div class="lg:col-span-2">{{ __('admin.choice_value') }}</div>
      <div class="lg:col-span-4">{{ __('admin.choices_ar') }}</div>
      <div class="lg:col-span-1">{{ __('admin.choice_weight') }}</div>
      <div class="lg:col-span-1 text-right">{{ __('admin.actions') }}</div>
    </div>

    <div id="choices_list" class="mt-2 space-y-3">
      @foreach($choicesEn as $i => $choiceEn)
        <div class="choice-row rounded-2xl border border-gray-100 bg-gray-50/60 p-3">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-center">
            <div class="lg:col-span-4">
              <label class="text-[11px] text-gray-500 lg:hidden">{{ __('admin.choices_en') }}</label>
              <input
                name="choices_en[]"
                value="{{ $choiceEn }}"
                placeholder="{{ __('admin.choices_en') }}"
                class="mt-1 lg:mt-0 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                       focus:border-red-300 focus:ring-4 focus:ring-red-100"
              >
            </div>
            <div class="lg:col-span-2">
              <label class="text-[11px] text-gray-500 lg:hidden">{{ __('admin.choice_value') }}</label>
              <input
                name="choices_value[]"
                type="number"
                min="1"
                max="5"
                step="1"
                value="{{ $choicesValue[$i] ?? '' }}"
                placeholder="{{ __('admin.choice_value') }}"
                class="mt-1 lg:mt-0 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                       focus:border-red-300 focus:ring-4 focus:ring-red-100"
              >
            </div>
            <div class="lg:col-span-4">
              <label class="text-[11px] text-gray-500 lg:hidden">{{ __('admin.choices_ar') }}</label>
              <input
                name="choices_ar[]"
                value="{{ $choicesAr[$i] ?? '' }}"
                placeholder="{{ __('admin.choices_ar') }}"
                class="mt-1 lg:mt-0 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                       focus:border-red-300 focus:ring-4 focus:ring-red-100"
              >
            </div>
            <div class="lg:col-span-1">
              <label class="text-[11px] text-gray-500 lg:hidden">{{ __('admin.choice_weight') }}</label>
              <input
                name="choices_weight[]"
                type="number"
                min="0"
                step="0.01"
                value="{{ $choicesWeight[$i] ?? 1 }}"
                placeholder="{{ __('admin.choice_weight') }}"
                class="mt-1 lg:mt-0 w-full rounded-2xl border border-gray-200 bg-white px-3 py-3 text-sm outline-none transition
                       focus:border-red-300 focus:ring-4 focus:ring-red-100"
              >
            </div>
            <div class="lg:col-span-1 flex lg:justify-end">
              <button type="button"
                      class="remove-choice w-11 h-11 rounded-2xl bg-red-50 border border-red-100 text-red-700 hover:bg-red-100">
                x
              </button>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <div id="yesno_block" class="{{ $type === 'YES_NO' ? '' : 'hidden' }}">
    <div class="rounded-2xl border border-gray-100 bg-gray-50/60 p-4">
      <div class="text-sm font-medium text-gray-700 mb-2">{{ __('admin.yes_no_scoring') }}</div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="text-xs text-gray-500">{{ __('admin.yes_value') }}</label>
          <input
            name="yes_value"
            type="number"
            min="1"
            max="5"
            step="1"
            value="{{ old('yes_value', $question->yes_value ?? 5) }}"
            class="mt-1 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                   focus:border-red-300 focus:ring-4 focus:ring-red-100"
          >
        </div>
        <div>
          <label class="text-xs text-gray-500">{{ __('admin.no_value') }}</label>
          <input
            name="no_value"
            type="number"
            min="1"
            max="5"
            step="1"
            value="{{ old('no_value', $question->no_value ?? 1) }}"
            class="mt-1 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                   focus:border-red-300 focus:ring-4 focus:ring-red-100"
          >
        </div>
        <div>
          <label class="text-xs text-gray-500">{{ __('admin.yes_weight') }}</label>
          <input
            name="yes_weight"
            type="number"
            min="0"
            step="0.01"
            value="{{ old('yes_weight', $question->yes_weight ?? 1) }}"
            class="mt-1 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                   focus:border-red-300 focus:ring-4 focus:ring-red-100"
          >
        </div>
        <div>
          <label class="text-xs text-gray-500">{{ __('admin.no_weight') }}</label>
          <input
            name="no_weight"
            type="number"
            min="0"
            step="0.01"
            value="{{ old('no_weight', $question->no_weight ?? 1) }}"
            class="mt-1 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                   focus:border-red-300 focus:ring-4 focus:ring-red-100"
          >
        </div>
      </div>
    </div>
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
    const yesNoBlock = document.getElementById('yesno_block');
    if (!typeSelect || !block) return;
    const toggle = () => {
      if (typeSelect.value === 'MULTIPLE_CHOICE') block.classList.remove('hidden');
      else block.classList.add('hidden');
      if (yesNoBlock) {
        if (typeSelect.value === 'YES_NO') yesNoBlock.classList.remove('hidden');
        else yesNoBlock.classList.add('hidden');
      }
    };
    typeSelect.addEventListener('change', toggle);
    toggle();

    const list = document.getElementById('choices_list');
    const addBtn = document.getElementById('add_choice_btn');
    const rowHtml = () => `
      <div class="choice-row grid grid-cols-1 lg:grid-cols-4 gap-3">
        <input name="choices_en[]" placeholder="{{ __('admin.choices_en') }}"
               class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                      focus:border-red-300 focus:ring-4 focus:ring-red-100">
        <input name="choices_value[]" type="number" min="1" max="5" step="1" placeholder="{{ __('admin.choice_value') }}"
               class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                      focus:border-red-300 focus:ring-4 focus:ring-red-100">
        <div class="flex gap-2">
          <input name="choices_ar[]" placeholder="{{ __('admin.choices_ar') }}"
                 class="flex-1 rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                        focus:border-red-300 focus:ring-4 focus:ring-red-100">
          <input name="choices_weight[]" type="number" min="0" step="0.01" placeholder="{{ __('admin.choice_weight') }}"
                 class="w-28 rounded-2xl border border-gray-200 bg-white px-3 py-3 text-sm outline-none transition
                        focus:border-red-300 focus:ring-4 focus:ring-red-100">
          <button type="button"
                  class="remove-choice w-11 h-11 rounded-2xl bg-red-50 border border-red-100 text-red-700 hover:bg-red-100">x</button>
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




