@extends('admin::layouts.app')

@section('title', __('admin.edit_admin'))

@section('content')
<div class="max-w-2xl">
    <h1 class="text-3xl font-bold text-[var(--text-primary)] mb-8">{{ __('admin.edit_admin') }}: {{ $admin->name }}</h1>

    <form action="{{ route('admin.admins.update', $admin) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-[var(--surface)] rounded-xl border border-[var(--border)] shadow-sm p-8">
            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                    {{ __('admin.name') }} *
                </label>
                <input type="text" 
                       id="name"
                       name="name" 
                       value="{{ old('name', $admin->name) }}"
                       required
                       class="w-full px-4 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] border border-[var(--border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:border-transparent transition-all @error('name') border-red-500 @enderror"
                       placeholder="{{ __('admin.enter_name') }}">
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                    {{ __('admin.email') }} *
                </label>
                <input type="email" 
                       id="email"
                       name="email" 
                       value="{{ old('email', $admin->email) }}"
                       required
                       class="w-full px-4 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] border border-[var(--border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                       placeholder="{{ __('admin.enter_email') }}">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div class="mb-6">
                <label for="phone" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                    {{ __('admin.phone') }}
                </label>
                <input type="tel" 
                       id="phone"
                       name="phone" 
                       value="{{ old('phone', $admin->phone) }}"
                       class="w-full px-4 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] border border-[var(--border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:border-transparent transition-all"
                       placeholder="{{ __('admin.enter_phone') }}">
                @error('phone')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password (optional for update) -->
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <p class="text-xs text-blue-700 dark:text-blue-300">{{ __('admin.leave_blank_keep_password') }}</p>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                    {{ __('admin.new_password') }}
                </label>
                <input type="password" 
                       id="password"
                       name="password" 
                       class="w-full px-4 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] border border-[var(--border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                       placeholder="{{ __('admin.enter_new_password') }}">
                <p class="text-xs text-[var(--text-secondary)] mt-1">{{ __('admin.password_min_8') }}</p>
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                    {{ __('admin.confirm_password') }}
                </label>
                <input type="password" 
                       id="password_confirmation"
                       name="password_confirmation" 
                       class="w-full px-4 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] border border-[var(--border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:border-transparent transition-all">
                @error('password_confirmation')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                    {{ __('admin.status') }} *
                </label>
                <select id="status" 
                        name="status" 
                        required
                        class="w-full px-4 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] border border-[var(--border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:border-transparent transition-all">
                    <option value="active" {{ old('status', $admin->status) === 'active' ? 'selected' : '' }}>{{ __('admin.active') }}</option>
                    <option value="inactive" {{ old('status', $admin->status) === 'inactive' ? 'selected' : '' }}>{{ __('admin.inactive') }}</option>
                </select>
                @error('status')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-700 rounded-lg">
                <p class="text-xs text-gray-700 dark:text-gray-300">
                    <strong>{{ __('admin.created_at') }}:</strong> {{ $admin->created_at->format('M d, Y H:i') }}<br>
                    <strong>{{ __('admin.last_login') }}:</strong> {{ $admin->last_login_at?->format('M d, Y H:i') ?? __('admin.never') }}
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-4 border-t border-[var(--border)]">
                <button type="submit" class="px-6 py-2.5 bg-[var(--brand)] text-white rounded-lg hover:shadow-lg transition-all font-medium">
                    {{ __('admin.save_changes') }}
                </button>
                <a href="{{ route('admin.admins.index') }}" class="px-6 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] rounded-lg border border-[var(--border)] hover:bg-[var(--bg-hover)] transition-all font-medium">
                    {{ __('admin.cancel') }}
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
