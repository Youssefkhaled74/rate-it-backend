@extends('admin::layouts.app')

@section('title', __('admin.profile'))

@section('content')
<div class="max-w-4xl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-[var(--text-primary)]">{{ __('admin.my_profile') }}</h1>
        <a href="{{ route('admin.profile.edit') }}" class="px-4 py-2 bg-[var(--brand)] text-white rounded-lg hover:shadow-lg transition-all">
            {{ __('admin.edit_profile') }}
        </a>
    </div>

    <!-- Profile Card -->
    <div class="bg-[var(--surface)] rounded-xl border border-[var(--border)] shadow-sm p-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Avatar -->
            <div class="flex flex-col items-center md:col-span-1">
                <div class="w-32 h-32 bg-gradient-to-br from-[var(--brand)] to-red-700 rounded-full flex items-center justify-center text-white text-4xl font-bold mb-4">
                    {{ strtoupper(substr(auth('admin')->user()->name, 0, 1)) }}
                </div>
                <h2 class="text-xl font-bold text-[var(--text-primary)] text-center">{{ auth('admin')->user()->name }}</h2>
                <p class="text-[var(--text-secondary)] text-sm mt-1">
                    @if(auth('admin')->user()->is_super)
                        <span class="inline-block px-3 py-1 bg-[var(--brand)] text-white text-xs rounded-full">{{ __('admin.super_admin') }}</span>
                    @else
                        <span class="inline-block px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 text-xs rounded-full">{{ __('admin.admin') }}</span>
                    @endif
                </p>
            </div>

            <!-- Info -->
            <div class="md:col-span-2 space-y-6">
                <div>
                    <p class="text-sm text-[var(--text-secondary)] mb-1">{{ __('admin.email') }}</p>
                    <p class="text-lg font-medium text-[var(--text-primary)]">{{ auth('admin')->user()->email }}</p>
                </div>

                <div>
                    <p class="text-sm text-[var(--text-secondary)] mb-1">{{ __('admin.phone') }}</p>
                    <p class="text-lg font-medium text-[var(--text-primary)]">{{ auth('admin')->user()->phone ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-[var(--text-secondary)] mb-1">{{ __('admin.status') }}</p>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full {{ auth('admin')->user()->status === 'active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        <p class="text-lg font-medium text-[var(--text-primary)]">
                            {{ auth('admin')->user()->status === 'active' ? __('admin.active') : __('admin.inactive') }}
                        </p>
                    </div>
                </div>

                <div>
                    <p class="text-sm text-[var(--text-secondary)] mb-1">{{ __('admin.last_login') }}</p>
                    <p class="text-lg font-medium text-[var(--text-primary)]">
                        {{ auth('admin')->user()->last_login_at ? auth('admin')->user()->last_login_at->diffForHumans() : '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-[var(--text-secondary)] mb-1">{{ __('admin.member_since') }}</p>
                    <p class="text-lg font-medium text-[var(--text-primary)]">{{ auth('admin')->user()->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-8 flex gap-4">
        <a href="{{ route('admin.profile.password') }}" class="flex-1 px-4 py-3 bg-[var(--surface)] border border-[var(--border)] text-[var(--text-primary)] rounded-lg hover:bg-[var(--bg-hover)] transition-colors text-center font-medium">
            {{ __('admin.change_password') }}
        </a>
        <form action="{{ route('admin.logout') }}" method="POST" class="flex-1">
            @csrf
            <button type="submit" class="w-full px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors font-medium">
                {{ __('admin.logout') }}
            </button>
        </form>
    </div>
</div>
@endsection
