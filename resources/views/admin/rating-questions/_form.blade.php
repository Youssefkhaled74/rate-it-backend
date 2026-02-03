@php
  $isEdit = !empty($question);
  $type = old('type', $question->type ?? 'RATING');
  $choicesEn = old('choices_en', $isEdit && $question->type === 'MULTIPLE_CHOICE' ? $question->choices->pluck('choice_en')->filter()->implode(', ') : '');
  $choicesAr = old('choices_ar', $isEdit && $question->type === 'MULTIPLE_CHOICE' ? $question->choices->pluck('choice_ar')->filter()->implode(', ') : '');
@endphp

<div class="space-y-6">

  <div>
    <label class="text-sm font-medium text-gray-700">Question Text</label>
    <input
      name="question_text"
      value="{{ old('question_text', $question->question_en ?? $question->question_text ?? '') }}"
      placeholder="Question Text"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
      required
    >
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">Question Text (AR)</label>
    <input
      name="question_ar"
      value="{{ old('question_ar', $question->question_ar ?? '') }}"
      placeholder="Arabic text"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
    >
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">Subcategories</label>
      <select name="subcategory_id" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                    focus:border-red-300 focus:ring-4 focus:ring-red-100" required>
        <option value="">Select subcategory</option>
        @foreach($subcategories as $sub)
          <option value="{{ $sub->id }}" {{ (string) old('subcategory_id', $question->subcategory_id ?? '') === (string) $sub->id ? 'selected' : '' }}>
            {{ $sub->name_en }} {{ $sub->category?->name_en ? '— ' . $sub->category->name_en : '' }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-sm font-medium text-gray-700">Type</label>
      <select name="type" id="question_type" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                    focus:border-red-300 focus:ring-4 focus:ring-red-100" required>
        <option value="RATING" {{ $type === 'RATING' ? 'selected' : '' }}>Rating (1–5)</option>
        <option value="YES_NO" {{ $type === 'YES_NO' ? 'selected' : '' }}>Yes / No</option>
        <option value="MULTIPLE_CHOICE" {{ $type === 'MULTIPLE_CHOICE' ? 'selected' : '' }}>Multiple Choice</option>
      </select>
    </div>
  </div>

  <div id="choices_block" class="{{ $type === 'MULTIPLE_CHOICE' ? '' : 'hidden' }}">
    <label class="text-sm font-medium text-gray-700">Choices (EN)</label>
    <input
      name="choices_en"
      value="{{ $choicesEn }}"
      placeholder="Coffee, Tea, Food"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
    >
    <div class="text-xs text-gray-500 mt-2">Separate choices with commas. Minimum 2 choices.</div>

    <label class="text-sm font-medium text-gray-700 mt-4 block">Choices (AR)</label>
    <input
      name="choices_ar"
      value="{{ $choicesAr }}"
      placeholder="Arabic choices separated by commas"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
    >
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">Sort Order</label>
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
          <span class="font-semibold">Required</span>
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
          <span class="font-semibold">Active</span>
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
  })();
</script>
