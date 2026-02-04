@php
  $isEdit = !empty($nationality);
@endphp

<div class="space-y-6">
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.country_code') }}</label>
      <input
        name="country_code"
        value="{{ old('country_code', $nationality->country_code ?? '') }}"
        placeholder="EG"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm uppercase outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
    </div>
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.flag_style') }}</label>
      <input
        name="flag_style"
        value="{{ old('flag_style', $nationality->flag_style ?? '') }}"
        placeholder="shiny"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.name_en') }}</label>
      <input
        name="name_en"
        value="{{ old('name_en', $nationality->name_en ?? '') }}"
        placeholder="Egyptian"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
        required
      >
    </div>
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.name_ar') }}</label>
      <input
        name="name_ar"
        value="{{ old('name_ar', $nationality->name_ar ?? '') }}"
        placeholder="Arabic name"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
    </div>
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">{{ __('admin.flag_size') }}</label>
    <input
      name="flag_size"
      type="number"
      min="16"
      max="256"
      value="{{ old('flag_size', $nationality->flag_size ?? '') }}"
      placeholder="64"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
    >
  </div>

  <div class="flex items-end">
    <label class="inline-flex items-center gap-3 cursor-pointer select-none">
      <input type="checkbox" name="is_active" value="1"
             {{ old('is_active', $nationality->is_active ?? 1) ? 'checked' : '' }}
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
