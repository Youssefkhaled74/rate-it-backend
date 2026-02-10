@php
  $isEdit = !empty($plan);
  $price = $isEdit ? number_format(($plan->price_cents ?? 0) / 100, 2, '.', '') : '0.00';
@endphp

<div class="space-y-6">
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.code') }}</label>
      <input
        name="code"
        value="{{ old('code', $plan->code ?? '') }}"
        placeholder="BASIC"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
        required
      >
    </div>
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.sort_order') }}</label>
      <input
        name="sort_order"
        type="number"
        value="{{ old('sort_order', $plan->sort_order ?? 0) }}"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.name_en') }}</label>
      <input
        name="name_en"
        value="{{ old('name_en', $plan->name_en ?? '') }}"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
        required
      >
    </div>
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.name_ar') }}</label>
      <input
        name="name_ar"
        value="{{ old('name_ar', $plan->name_ar ?? '') }}"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.description_en') }}</label>
      <textarea
        name="description_en"
        rows="3"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >{{ old('description_en', $plan->description_en ?? '') }}</textarea>
    </div>
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.description_ar') }}</label>
      <textarea
        name="description_ar"
        rows="3"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >{{ old('description_ar', $plan->description_ar ?? '') }}</textarea>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.price') }}</label>
      <input
        name="price"
        type="number"
        min="0"
        step="0.01"
        value="{{ old('price', $price) }}"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
        required
      >
    </div>
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.currency') }}</label>
      <input
        name="currency"
        value="{{ old('currency', $plan->currency ?? 'USD') }}"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
        required
      >
    </div>
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.interval') }}</label>
      <select
        name="interval"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
        <option value="month" {{ old('interval', $plan->interval ?? 'month') === 'month' ? 'selected' : '' }}>{{ __('admin.month') }}</option>
        <option value="year" {{ old('interval', $plan->interval ?? 'month') === 'year' ? 'selected' : '' }}>{{ __('admin.year') }}</option>
      </select>
    </div>
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.interval_count') }}</label>
      <input
        name="interval_count"
        type="number"
        min="1"
        value="{{ old('interval_count', $plan->interval_count ?? 1) }}"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
        required
      >
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">{{ __('admin.trial_days') }}</label>
      <input
        name="trial_days"
        type="number"
        min="0"
        value="{{ old('trial_days', $plan->trial_days ?? 0) }}"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
    </div>
    <div class="flex items-end">
      <label class="inline-flex items-center gap-3 cursor-pointer select-none">
        <input type="checkbox" name="is_best_value" value="1"
               {{ old('is_best_value', $plan->is_best_value ?? 0) ? 'checked' : '' }}
               class="sr-only peer">

        <span class="w-12 h-6 rounded-full bg-gray-200 peer-checked:bg-green-500 transition relative">
          <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white transition peer-checked:translate-x-6"></span>
        </span>

        <span class="text-sm text-gray-700">
          <span class="font-semibold">{{ __('admin.best_value') }}</span>
        </span>
      </label>
    </div>
    <div class="flex items-end">
      <label class="inline-flex items-center gap-3 cursor-pointer select-none">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', $plan->is_active ?? 1) ? 'checked' : '' }}
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
