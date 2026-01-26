@extends('admin::layouts.auth')

@section('title', __('admin.forgot_password'))

@section('content')
<div class="pt-20">
    <div class="bg-[var(--surface)] rounded-xl border border-[var(--border)] shadow-lg p-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-[var(--text-primary)]">{{ __('admin.forgot_password') }}</h1>
            <p class="text-[var(--text-secondary)] mt-2 text-sm">{{ __('admin.forgot_password_description') }}</p>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('admin.password.email') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                    {{ __('admin.email') }}
                </label>
                <input type="email" 
                       id="email"
                       name="email" 
                       value="{{ old('email') }}"
                       required
                       class="w-full px-4 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] border border-[var(--border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:border-transparent transition-all"
                       placeholder="{{ __('admin.enter_email') }}">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-[var(--brand)] to-red-700 text-white font-medium py-2.5 rounded-lg hover:shadow-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:ring-offset-2">
                {{ __('admin.send_reset_link') }}
            </button>
        </form>

        <!-- Back to Login -->
        <div class="mt-6 text-center">
            <p class="text-sm text-[var(--text-secondary)]">
                {{ __('admin.remember_password') }}
                <a href="{{ route('admin.login') }}" class="text-[var(--brand)] hover:text-red-700 font-medium transition-colors">
                    {{ __('admin.sign_in_here') }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
