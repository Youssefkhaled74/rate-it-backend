@extends('admin::layouts.auth')

@section('title', __('admin.reset_password'))

@section('content')
<div class="pt-20">
    <div class="bg-[var(--surface)] rounded-xl border border-[var(--border)] shadow-lg p-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-[var(--text-primary)]">{{ __('admin.reset_password') }}</h1>
            <p class="text-[var(--text-secondary)] mt-2 text-sm">{{ __('admin.reset_password_description') }}</p>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.password.update') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ request('token') }}">

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                    {{ __('admin.email') }}
                </label>
                <input type="email" 
                       id="email"
                       name="email" 
                       value="{{ request('email') ?? old('email') }}"
                       required
                       class="w-full px-4 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] border border-[var(--border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:border-transparent transition-all"
                       placeholder="{{ __('admin.enter_email') }}">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                    {{ __('admin.new_password') }}
                </label>
                <input type="password" 
                       id="password"
                       name="password" 
                       required
                       class="w-full px-4 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] border border-[var(--border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:border-transparent transition-all"
                       placeholder="{{ __('admin.enter_password') }}">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                    {{ __('admin.confirm_password') }}
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

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-[var(--brand)] to-red-700 text-white font-medium py-2.5 rounded-lg hover:shadow-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:ring-offset-2">
                {{ __('admin.reset_password_button') }}
            </button>
        </form>

        <!-- Back to Login -->
        <div class="mt-6 text-center">
            <p class="text-sm text-[var(--text-secondary)]">
                <a href="{{ route('admin.login') }}" class="text-[var(--brand)] hover:text-red-700 font-medium transition-colors">
                    {{ __('admin.back_to_login') }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
