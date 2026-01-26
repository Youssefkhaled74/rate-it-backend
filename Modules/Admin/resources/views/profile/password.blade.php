@extends('admin::layouts.app')

@section('title', __('admin.change_password'))

@section('content')
<div class="max-w-2xl">
    <!-- Header -->
    <h1 class="text-3xl font-bold text-[var(--text-primary)] mb-8">{{ __('admin.change_password') }}</h1>

    <!-- Form -->
    <form action="{{ route('admin.profile.password.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-[var(--surface)] rounded-xl border border-[var(--border)] shadow-sm p-8">
            <!-- Current Password -->
            <div class="mb-6">
                <label for="current_password" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                    {{ __('admin.current_password') }} *
                </label>
                <input type="password" 
                       id="current_password"
                       name="current_password" 
                       required
                       class="w-full px-4 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] border border-[var(--border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:border-transparent transition-all"
                       placeholder="{{ __('admin.enter_current_password') }}">
                @error('current_password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                    {{ __('admin.new_password') }} *
                </label>
                <input type="password" 
                       id="password"
                       name="password" 
                       required
                       class="w-full px-4 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] border border-[var(--border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:border-transparent transition-all"
                       placeholder="{{ __('admin.enter_new_password') }}">
                <p class="text-xs text-[var(--text-secondary)] mt-1">{{ __('admin.password_min_8') }}</p>
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                    {{ __('admin.confirm_password') }} *
                </label>
                <input type="password" 
                       id="password_confirmation"
                       name="password_confirmation" 
                       required
                       class="w-full px-4 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] border border-[var(--border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:border-transparent transition-all"
                       placeholder="{{ __('admin.confirm_password') }}">
                @error('password_confirmation')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Alert -->
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    {{ __('admin.password_security_note') }}
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-4 border-t border-[var(--border)]">
                <button type="submit" class="px-6 py-2.5 bg-[var(--brand)] text-white rounded-lg hover:shadow-lg transition-all font-medium">
                    {{ __('admin.update_password') }}
                </button>
                <a href="{{ route('admin.profile.show') }}" class="px-6 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] rounded-lg border border-[var(--border)] hover:bg-[var(--bg-hover)] transition-all font-medium">
                    {{ __('admin.cancel') }}
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
