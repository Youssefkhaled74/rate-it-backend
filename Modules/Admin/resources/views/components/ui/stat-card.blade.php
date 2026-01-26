<!-- Stat Card Component -->
@props([
    'title' => '',
    'value' => '',
    'subtitle' => '',
    'icon' => null,
    'trend' => null,
])

<div class="bg-[var(--surface)] border border-[var(--border)] rounded-2xl p-6 shadow-[var(--shadow)]">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-sm text-[var(--text-secondary)] font-medium">{{ $title }}</p>
            <h3 class="text-3xl font-bold text-[var(--text-primary)] mt-2">{{ $value }}</h3>
            @if ($subtitle)
                <p class="text-xs text-[var(--text-tertiary)] mt-3">{{ $subtitle }}</p>
            @endif
        </div>
        @if ($icon)
            <div class="w-12 h-12 rounded-xl bg-[var(--brand-lighter)] flex items-center justify-center text-[var(--brand)] flex-shrink-0">
                {!! $icon !!}
            </div>
        @endif
    </div>
    
    @if ($trend)
        <div class="mt-4 flex items-center gap-2">
            <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $trend['positive'] ? 'text-[var(--success)]' : 'text-[var(--danger)]' }}">
                @if ($trend['positive'])
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414-1.414L13.586 7H12z" clip-rule="evenodd"></path>
                    </svg>
                @else
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 1.414L13.586 13H12z" clip-rule="evenodd"></path>
                    </svg>
                @endif
                {{ $trend['value'] }}%
            </span>
            <span class="text-xs text-[var(--text-tertiary)]">vs last month</span>
        </div>
    @endif
</div>
