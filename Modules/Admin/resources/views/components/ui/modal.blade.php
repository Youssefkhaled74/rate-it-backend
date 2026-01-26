<!-- Modal Component -->
@props([
    'id' => 'modal',
    'title' => '',
    'maxWidth' => 'md', // sm, md, lg, xl, 2xl
])

@php
$maxWidthClass = match($maxWidth) {
    'sm' => 'max-w-sm',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    default => 'max-w-md',
};
@endphp

<div x-data="{ open: false }" @{{ $id }}-open.window="open = true" @{{ $id }}-close.window="open = false">
    <!-- Backdrop -->
    <div x-show="open" x-transition class="fixed inset-0 bg-black/50 z-40" @click="open = false"></div>

    <!-- Modal -->
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="open = false">
        <div class="bg-[var(--surface)] rounded-2xl shadow-lg {{ $maxWidthClass }} w-full max-h-[90vh] overflow-y-auto"
             @click.stop>
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-[var(--border)]">
                @if ($title)
                    <h2 class="text-xl font-bold text-[var(--text-primary)]">{{ $title }}</h2>
                @endif
                <button @click="open = false" class="p-2 rounded-lg hover:bg-[var(--surface-2)] transition-colors">
                    <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
