@extends('admin::layouts.app')

@section('title', __('admin.manage_admins'))

@section('content')
<div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-[var(--text-primary)]">{{ __('admin.manage_admins') }}</h1>
        @can('create', \Modules\Admin\app\Models\Admin::class)
            <a href="{{ route('admin.admins.create') }}" class="px-4 py-2 bg-[var(--brand)] text-white rounded-lg hover:shadow-lg transition-all">
                {{ __('admin.add_admin') }}
            </a>
        @endcan
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-[var(--surface)] rounded-lg border border-[var(--border)] p-6">
            <p class="text-sm text-[var(--text-secondary)] mb-1">{{ __('admin.total_admins') }}</p>
            <p class="text-3xl font-bold text-[var(--text-primary)]">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-[var(--surface)] rounded-lg border border-[var(--border)] p-6">
            <p class="text-sm text-[var(--text-secondary)] mb-1">{{ __('admin.active_admins') }}</p>
            <p class="text-3xl font-bold text-green-600">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-[var(--surface)] rounded-lg border border-[var(--border)] p-6">
            <p class="text-sm text-[var(--text-secondary)] mb-1">{{ __('admin.inactive_admins') }}</p>
            <p class="text-3xl font-bold text-red-600">{{ $stats['inactive'] }}</p>
        </div>
        <div class="bg-[var(--surface)] rounded-lg border border-[var(--border)] p-6">
            <p class="text-sm text-[var(--text-secondary)] mb-1">{{ __('admin.super_admins') }}</p>
            <p class="text-3xl font-bold text-[var(--brand)]">{{ $stats['super_admins'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-[var(--surface)] rounded-lg border border-[var(--border)] p-4 mb-6">
        <form action="{{ route('admin.admins.index') }}" method="GET" class="flex gap-4 flex-wrap items-end">
            <div class="flex-1 min-w-48">
                <label for="search" class="block text-sm font-medium text-[var(--text-primary)] mb-1">{{ __('admin.search') }}</label>
                <input type="text" 
                       id="search"
                       name="search" 
                       value="{{ $search }}"
                       class="w-full px-3 py-2 bg-[var(--bg)] border border-[var(--border)] rounded-lg focus:ring-2 focus:ring-[var(--brand)]"
                       placeholder="{{ __('admin.search_by_name_email') }}">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-[var(--text-primary)] mb-1">{{ __('admin.status') }}</label>
                <select id="status" name="status" class="px-3 py-2 bg-[var(--bg)] border border-[var(--border)] rounded-lg focus:ring-2 focus:ring-[var(--brand)]">
                    <option value="">{{ __('admin.all_statuses') }}</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>{{ __('admin.active') }}</option>
                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>{{ __('admin.inactive') }}</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-[var(--brand)] text-white rounded-lg hover:shadow-lg">
                {{ __('admin.search') }}
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-[var(--surface)] rounded-lg border border-[var(--border)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[var(--bg-hover)] border-b border-[var(--border)]">
                        <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">{{ __('admin.name') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">{{ __('admin.email') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">{{ __('admin.role') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">{{ __('admin.status') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">{{ __('admin.last_login') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody divide-y divide-[var(--border)]">
                    @forelse($admins as $admin)
                        <tr class="hover:bg-[var(--bg-hover)] transition-colors">
                            <td class="px-6 py-4 text-sm text-[var(--text-primary)]">{{ $admin->name }}</td>
                            <td class="px-6 py-4 text-sm text-[var(--text-primary)]">{{ $admin->email }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($admin->is_super)
                                    <span class="px-2 py-1 bg-[var(--brand)] text-white text-xs rounded-full">{{ __('admin.super_admin') }}</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 text-xs rounded-full">{{ __('admin.admin') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center gap-2 px-2 py-1 text-xs rounded-full {{ $admin->status === 'active' ? 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400' }}">
                                    <span class="w-2 h-2 rounded-full {{ $admin->status === 'active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    {{ $admin->status === 'active' ? __('admin.active') : __('admin.inactive') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-[var(--text-secondary)]">
                                {{ $admin->last_login_at?->diffForHumans() ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex gap-2">
                                    @can('update', $admin)
                                        <a href="{{ route('admin.admins.edit', $admin) }}" class="px-3 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                            {{ __('admin.edit') }}
                                        </a>
                                    @endcan
                                    @can('deactivate', $admin)
                                        @if($admin->status === 'active')
                                            <form action="{{ route('admin.admins.deactivate', $admin) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors" onclick="return confirm('{{ __('admin.confirm_deactivate') }}')">
                                                    {{ __('admin.deactivate') }}
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.admins.activate', $admin) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                                                    {{ __('admin.activate') }}
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-[var(--text-secondary)]">
                                {{ __('admin.no_admins_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($admins->hasPages())
            <div class="px-6 py-4 border-t border-[var(--border)] flex items-center justify-between">
                <p class="text-sm text-[var(--text-secondary)]">
                    {{ __('admin.showing') }} {{ $admins->firstItem() }} {{ __('admin.to') }} {{ $admins->lastItem() }} {{ __('admin.of') }} {{ $admins->total() }}
                </p>
                {{ $admins->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
