<!-- Empty State Component -->
@props([
    'icon' => null,
    'title' => 'No data found',
    'description' => '',
    'action' => null,
    'actionLabel' => 'Create New',
    'actionHref' => '#',
])

<div class="py-16 text-center">
    @if ($icon)
        <div class="w-16 h-16 rounded-2xl bg-[var(--surface-2)] flex items-center justify-center mx-auto mb-6 text-[var(--text-tertiary)]">
            {!! $icon !!}
        </div>
    @else
        <svg class="w-16 h-16 mx-auto mb-6 text-[var(--text-tertiary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
        </svg>
    @endif

    <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-2">{{ $title }}</h3>
    
    @if ($description)
        <p class="text-[var(--text-secondary)] mb-6 max-w-sm mx-auto">{{ $description }}</p>
    @endif

    @if ($action)
        {{ $action }}
    @else
        <a href="{{ $actionHref }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[var(--brand)] text-white font-semibold rounded-2xl hover:bg-red-700 transition-colors">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
            </svg>
            {{ $actionLabel }}
        </a>
    @endif
</div>
