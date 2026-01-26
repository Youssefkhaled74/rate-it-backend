<!-- Premium Card Component -->
@props([
    'hoverable' => false,
    'border' => true,
    'clickable' => false,
    'noPadding' => false,
])

<div class="bg-[var(--surface)] rounded-2xl {{ $border ? 'border border-[var(--border)]' : '' }} shadow-[var(--shadow)] {{ $hoverable ? 'hover:shadow-[var(--shadow-md)] hover:border-[var(--border-light)]' : '' }} {{ $clickable ? 'cursor-pointer' : '' }} transition-all {{ !$noPadding ? 'p-6' : '' }}">
    {{ $slot }}
</div>
