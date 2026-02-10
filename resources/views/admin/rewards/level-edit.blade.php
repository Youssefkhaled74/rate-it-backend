@extends('admin.layouts.app')

@section('page_title', __('admin.edit_level'))
@section('title', __('admin.edit_level'))

@section('content')
  <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.edit_level') }}</h2>
      <a href="{{ route('admin.rewards.index') }}" class="text-sm text-gray-500">{{ __('admin.back') }}</a>
    </div>

    @if ($errors->any())
      <div class="mt-4 rounded-2xl bg-red-50 border border-red-100 text-red-700 text-sm px-4 py-3">
        <div class="font-semibold mb-1">{{ __('admin.fix_errors') }}</div>
        @foreach($errors->all() as $err)
          <div>{{ $err }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('admin.rewards.levels.update', $level) }}" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      @csrf
      @method('PATCH')

      <div>
        <label class="text-xs text-gray-500">{{ __('admin.level_name') }}</label>
        <input name="name" value="{{ old('name', $level->name) }}"
          class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200" required>
      </div>
      <div>
        <label class="text-xs text-gray-500">{{ __('admin.min_reviews') }}</label>
        <input name="min_reviews" type="number" min="0" value="{{ old('min_reviews', $level->min_reviews) }}"
          class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200" required>
      </div>
      <div>
        <label class="text-xs text-gray-500">{{ __('admin.bonus_percent') }}</label>
        <input name="bonus_percent" type="number" min="0" step="0.01" value="{{ old('bonus_percent', $level->bonus_percent ?? 0) }}"
          class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
      </div>
      <div class="md:col-span-2">
        <label class="text-xs text-gray-500">{{ __('admin.level_benefits') }}</label>
        <textarea name="benefits_text" rows="4"
          class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200"
          placeholder="{{ __('admin.benefits_placeholder') }}">{{ old('benefits_text', $benefitsText) }}</textarea>
      </div>
      <div class="md:col-span-2">
        <button type="submit" class="rounded-full bg-red-800 text-white px-6 py-2 text-sm font-semibold hover:bg-red-900 transition">
          {{ __('admin.save_changes') }}
        </button>
      </div>
    </form>
  </div>
@endsection
