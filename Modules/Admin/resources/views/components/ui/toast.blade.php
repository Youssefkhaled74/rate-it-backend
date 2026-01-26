<!-- Toast Component -->
@props([
    'id' => 'toast',
    'variant' => 'success', // success, error, warning, info
    'message' => '',
    'duration' => 5000,
])

@php
$variantClasses = match($variant) {
    'error' => ['bg' => 'bg-[var(--danger-light)]', 'text' => 'text-[var(--danger)]', 'border' => 'border-[var(--danger)]'],
    'warning' => ['bg' => 'bg-[var(--warning-light)]', 'text' => 'text-[var(--warning)]', 'border' => 'border-[var(--warning)]'],
    'info' => ['bg' => 'bg-[var(--info-light)]', 'text' => 'text-[var(--info)]', 'border' => 'border-[var(--info)]'],
    default => ['bg' => 'bg-[var(--success-light)]', 'text' => 'text-[var(--success)]', 'border' => 'border-[var(--success)]'],
};
@endphp

<div x-data="{ show: false, init() { this.show = true; setTimeout(() => this.show = false, {{ $duration }}); } }" 
     x-init="init()"
     x-show="show"
     x-transition
     class="fixed bottom-6 right-6 max-w-sm z-50">
    <div class="px-6 py-4 rounded-2xl {{ $variantClasses['bg'] }} border {{ $variantClasses['border'] }} flex items-start gap-4 shadow-lg">
        <svg class="w-5 h-5 {{ $variantClasses['text'] }} flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            @if ($variant === 'success')
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            @elseif ($variant === 'error')
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            @elseif ($variant === 'warning')
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            @else
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            @endif
        </svg>
        <div class="flex-1">
            <p class="font-semibold {{ $variantClasses['text'] }}">{{ ucfirst($variant) }}</p>
            <p class="text-sm text-[var(--text-secondary)] mt-1">{{ $message }}</p>
        </div>
        <button @click="show = false" class="text-[var(--text-tertiary)] hover:text-[var(--text-secondary)] flex-shrink-0">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
    </div>
</div>
