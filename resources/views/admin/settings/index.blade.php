@extends('admin.layouts.app')

@section('page_title', __('admin.settings'))
@section('title', __('admin.settings'))

@section('content')
  <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft space-y-6">
    <div>
      <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.subscription_settings') }}</h2>
      <div class="text-sm text-gray-500 mt-1">{{ __('admin.subscription_settings_help') }}</div>
    </div>

    @if(session('success'))
      <div class="rounded-2xl bg-green-50 border border-green-100 text-green-700 text-sm px-4 py-3">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.subscription.update') }}" class="space-y-4">
      @csrf

      <div>
        <label class="text-sm font-medium text-gray-700">{{ __('admin.free_trial_days') }}</label>
        <input
          type="number"
          min="0"
          max="3650"
          name="free_trial_days"
          value="{{ old('free_trial_days', $freeTrialDays) }}"
          class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                 focus:border-red-300 focus:ring-4 focus:ring-red-100"
          required
        >
        <div class="text-xs text-gray-500 mt-1">{{ __('admin.days') }}</div>
        @error('free_trial_days')
          <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
        @enderror
      </div>

      <button
        type="submit"
        class="rounded-full bg-red-800 text-white px-6 py-2 text-sm font-semibold hover:bg-red-900 transition"
      >
        {{ __('admin.save_changes') }}
      </button>
    </form>
  </div>
@endsection
