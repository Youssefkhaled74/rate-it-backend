<!-- Input Component -->
@props([
    'name' => '',
    'label' => '',
    'type' => 'text',
    'placeholder' => '',
    'value' => '',
    'error' => null,
    'required' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'helpText' => '',
])

<div class="space-y-2">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-semibold text-[var(--text-primary)]">
            {{ $label }}
            @if ($required)
                <span class="text-[var(--danger)]">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if ($icon && $iconPosition === 'left')
            <div class="absolute {{ session('rtl') ? 'right-3' : 'left-3' }} top-1/2 transform -translate-y-1/2 text-[var(--text-tertiary)]">
                {!! $icon !!}
            </div>
        @endif

        <input 
            @if ($name) name="{{ $name }}" id="{{ $name }}" @endif
            type="{{ $type }}" 
            placeholder="{{ $placeholder }}"
            @if ($value) value="{{ $value }}" @endif
            @class([
                'w-full px-4 py-2.5 rounded-xl bg-[var(--surface-2)] border transition-all duration-200 text-[var(--text-primary)] placeholder-[var(--text-tertiary)]',
                'focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:ring-offset-2 focus:bg-[var(--surface)]',
                'border-[var(--danger)]' => $error || $errors->has($name),
                'border-[var(--border)]' => !$error && !$errors->has($name),
                $icon && $iconPosition === 'left' ? (session('rtl') ? 'pr-10' : 'pl-10') : '',
                $icon && $iconPosition === 'right' ? (session('rtl') ? 'pl-10' : 'pr-10') : '',
            ])
            {{ $attributes }}
        />

        @if ($icon && $iconPosition === 'right')
            <div class="absolute {{ session('rtl') ? 'left-3' : 'right-3' }} top-1/2 transform -translate-y-1/2 text-[var(--text-tertiary)]">
                {!! $icon !!}
            </div>
        @endif
    </div>

    @if ($error || $errors->has($name))
        <p class="text-xs text-[var(--danger)] font-medium">
            {{ $error || $errors->first($name) }}
        </p>
    @elseif ($helpText)
        <p class="text-xs text-[var(--text-tertiary)]">{{ $helpText }}</p>
    @endif
</div>
