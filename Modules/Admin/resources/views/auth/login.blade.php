@extends('admin::layouts.auth')

@section('title', __('admin.login'))

@section('content')
<div class="pt-20">
    <div class="bg-[var(--surface)] rounded-xl border border-[var(--border)] shadow-lg p-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="w-12 h-12 bg-gradient-to-br from-[var(--brand)] to-red-700 rounded-lg flex items-center justify-center mb-4">
                <span class="text-white font-bold text-xl">R</span>
            </div>
            <h1 class="text-2xl font-bold text-[var(--text-primary)]">{{ __('admin.welcome_back') }}</h1>
            <p class="text-[var(--text-secondary)] mt-2">{{ __('admin.login_description') }}</p>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.login') }}" method="POST" class="space-y-5">
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

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                    {{ __('admin.password') }}
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

            <!-- Remember Me -->
            <div class="flex items-center">
                <input type="checkbox" 
                       id="remember"
                       name="remember"
                       class="w-4 h-4 text-[var(--brand)] bg-[var(--bg)] border border-[var(--border)] rounded focus:ring-2 focus:ring-[var(--brand)]">
                <label for="remember" class="ml-2 text-sm text-[var(--text-secondary)]">
                    {{ __('admin.remember_me') }}
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-[var(--brand)] to-red-700 text-white font-medium py-2.5 rounded-lg hover:shadow-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:ring-offset-2">
                {{ __('admin.sign_in') }}
            </button>
        </form>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-[var(--border)]"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-[var(--surface)] text-[var(--text-secondary)]">{{ __('admin.or') }}</span>
            </div>
        </div>

        <!-- Forgot Password Link -->
        <div class="text-center">
            <a href="{{ route('admin.password.request') }}" 
               class="text-sm text-[var(--brand)] hover:text-red-700 font-medium transition-colors">
                {{ __('admin.forgot_password') }}
            </a>
        </div>
    </div>

    <!-- Demo Credentials (Remove in production) -->
    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
        <p class="text-xs text-blue-700 dark:text-blue-300">
            <strong>{{ __('admin.demo_credentials') }}:</strong><br>
            Email: admin@rateit.com<br>
            Password: password123
        </p>
    </div>
</div>
@endsection
