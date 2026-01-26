@extends('admin::layouts.auth')

@section('title', 'Admin Login - Rate It')

@section('content')
<div class="pt-16 pb-8 px-4">
    <div class="max-w-sm mx-auto">
        <!-- Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-xl p-8 space-y-6">
            
            <!-- Logo/Brand -->
            <div class="flex flex-col items-center space-y-3">
                <div class="w-14 h-14 bg-gradient-to-br from-red-600 to-red-700 rounded-xl flex items-center justify-center shadow-lg">
                    <span class="text-white font-black text-2xl">R</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Rate It</h1>
            </div>

            <!-- Heading -->
            <div class="text-center space-y-2">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('admin.welcome_back', 'Welcome Back') }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    {{ __('admin.login_description', 'Sign in to your admin account') }}
                </p>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.login') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Email Field -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ __('admin.email', 'Email Address') }}
                    </label>
                    <input type="email" 
                           id="email"
                           name="email" 
                           value="{{ old('email') }}"
                           required
                           class="w-full px-4 py-3 text-gray-900 dark:text-white bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition-all"
                           placeholder="{{ __('admin.enter_email', 'you@example.com') }}">
                    @error('email')
                        <p class="text-red-600 dark:text-red-400 text-sm font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ __('admin.password', 'Password') }}
                    </label>
                    <input type="password" 
                           id="password"
                           name="password" 
                           required
                           class="w-full px-4 py-3 text-gray-900 dark:text-white bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition-all"
                           placeholder="{{ __('admin.enter_password', '••••••••') }}">
                    @error('password')
                        <p class="text-red-600 dark:text-red-400 text-sm font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" 
                           id="remember"
                           name="remember"
                           class="w-4 h-4 text-red-600 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded focus:ring-2 focus:ring-red-500">
                    <label for="remember" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('admin.remember_me', 'Remember me') }}
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold py-3 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                    {{ __('admin.sign_in', 'Sign In') }}
                </button>
            </form>

            <!-- Divider -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300 dark:border-slate-600"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-3 bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-400">
                        {{ __('admin.or', 'or') }}
                    </span>
                </div>
            </div>

            <!-- Forgot Password Link -->
            <div class="text-center">
                <a href="{{ route('admin.password.request') }}" 
                   class="text-sm font-semibold text-red-600 hover:text-red-700 dark:hover:text-red-500 transition-colors">
                    {{ __('admin.forgot_password', 'Forgot your password?') }}
                </a>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg">
            <p class="text-xs text-blue-800 dark:text-blue-200 leading-relaxed">
                <strong>Demo Account:</strong><br>
                Email: admin@rateit.com<br>
                Password: password123
            </p>
        </div>
    </div>
</div>
@endsection
