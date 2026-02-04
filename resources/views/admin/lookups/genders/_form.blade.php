@php
  $isEdit = !empty($gender);
@endphp

<div class="space-y-6">
  <div>
    <label class="text-sm font-medium text-gray-700">{{ __('admin.code') }}</label>
    <input
      name="code"
      value="{{ old('code', $gender->code ?? '') }}"
      placeholder="M or F"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
      required
    >
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.name_en') }}</label>
      <input
        name="name_en"
        value="{{ old('name_en', $gender->name_en ?? '') }}"
        placeholder="Male"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
        required
      >
    </div>
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.name_ar') }}</label>
      <input
        name="name_ar"
        value="{{ old('name_ar', $gender->name_ar ?? '') }}"
        placeholder="Arabic name"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
    </div>
  </div>

  <div class="flex items-end">
    <label class="inline-flex items-center gap-3 cursor-pointer select-none">
      <input type="checkbox" name="is_active" value="1"
             {{ old('is_active', $gender->is_active ?? 1) ? 'checked' : '' }}
             class="sr-only peer">

      <span class="w-12 h-6 rounded-full bg-gray-200 peer-checked:bg-green-500 transition relative">
        <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white transition peer-checked:translate-x-6"></span>
      </span>

      <span class="text-sm text-gray-700">
        <span class="font-semibold">{{ __('admin.active') }}</span>
        <span class="text-gray-500">- {{ __('admin.visible') }}</span>
      </span>
    </label>
  </div>
</div>
