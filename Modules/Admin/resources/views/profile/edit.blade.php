@extends('admin::layouts.app')

@section('title', __('admin.edit_profile'))

@section('content')
<div class="max-w-2xl">
    <!-- Header -->
    <h1 class="text-3xl font-bold text-[var(--text-primary)] mb-8">{{ __('admin.edit_profile') }}</h1>

    <!-- Form -->
    <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-6">
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
                       value="{{ old('name', auth('admin')->user()->name) }}"
                       required
                       class="w-full px-4 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] border border-[var(--border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:border-transparent transition-all"
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
                       value="{{ old('email', auth('admin')->user()->email) }}"
                       required
                       class="w-full px-4 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] border border-[var(--border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:border-transparent transition-all"
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
                       value="{{ old('phone', auth('admin')->user()->phone) }}"
                       class="w-full px-4 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] border border-[var(--border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:border-transparent transition-all"
                       placeholder="{{ __('admin.enter_phone') }}">
                @error('phone')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-4 border-t border-[var(--border)]">
                <button type="submit" class="px-6 py-2.5 bg-[var(--brand)] text-white rounded-lg hover:shadow-lg transition-all font-medium">
                    {{ __('admin.save_changes') }}
                </button>
                <a href="{{ route('admin.profile.show') }}" class="px-6 py-2.5 bg-[var(--bg)] text-[var(--text-primary)] rounded-lg border border-[var(--border)] hover:bg-[var(--bg-hover)] transition-all font-medium">
                    {{ __('admin.cancel') }}
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
