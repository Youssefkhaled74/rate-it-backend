<!-- Button Component -->
@props([
    'variant' => 'primary', // primary, secondary, ghost, danger
    'size' => 'md', // sm, md, lg
    'icon' => null,
    'iconPosition' => 'left',
    'loading' => false,
    'disabled' => false,
    'fullWidth' => false,
    'href' => null,
])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2 font-semibold rounded-2xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$sizeClasses = match($size) {
    'sm' => 'px-3 py-1.5 text-sm',
    'lg' => 'px-8 py-3.5 text-base',
    default => 'px-6 py-2.5 text-sm',
};

$variantClasses = match($variant) {
    'secondary' => 'bg-[var(--surface-2)] text-[var(--text-primary)] hover:bg-[var(--surface-hover)] focus:ring-[var(--brand)]',
    'ghost' => 'bg-transparent text-[var(--text-primary)] hover:bg-[var(--surface-2)] focus:ring-[var(--brand)]',
    'danger' => 'bg-[var(--danger)] text-white hover:bg-red-700 focus:ring-[var(--danger)]',
    default => 'bg-[var(--brand)] text-white hover:bg-red-700 focus:ring-[var(--brand)]',
};

$widthClass = $fullWidth ? 'w-full' : '';
@endphp

@if ($href)
    <a href="{{ $href }}" @class([$baseClasses, $sizeClasses, $variantClasses, $widthClass]) {{ $attributes }}>
        @if ($loading)
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        @elseif ($icon && $iconPosition === 'left')
            {!! $icon !!}
        @endif
        {{ $slot }}
        @if ($icon && $iconPosition === 'right')
            {!! $icon !!}
        @endif
    </a>
@else
    <button @class([$baseClasses, $sizeClasses, $variantClasses, $widthClass]) {{ $disabled ? 'disabled' : '' }} {{ $attributes }}>
        @if ($loading)
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        @elseif ($icon && $iconPosition === 'left')
            {!! $icon !!}
        @endif
        {{ $slot }}
        @if ($icon && $iconPosition === 'right')
            {!! $icon !!}
        @endif
    </button>
@endif
