<!-- Badge Component -->
@props([
    'variant' => 'neutral', // success, warning, danger, info, neutral
    'size' => 'md', // sm, md, lg
    'icon' => null,
])

@php
$variantClasses = match($variant) {
    'success' => 'bg-[var(--success-light)] text-[var(--success)]',
    'warning' => 'bg-[var(--warning-light)] text-[var(--warning)]',
    'danger' => 'bg-[var(--danger-light)] text-[var(--danger)]',
    'info' => 'bg-[var(--info-light)] text-[var(--info)]',
    default => 'bg-[var(--surface-2)] text-[var(--text-secondary)]',
};

$sizeClasses = match($size) {
    'sm' => 'px-2.5 py-1 text-xs',
    'lg' => 'px-4 py-2 text-sm',
    default => 'px-3 py-1.5 text-xs',
};
@endphp

<span @class(["inline-flex items-center gap-1.5 font-semibold rounded-lg", $variantClasses, $sizeClasses])>
    @if ($icon)
        {!! $icon !!}
    @endif
    {{ $slot }}
</span>
