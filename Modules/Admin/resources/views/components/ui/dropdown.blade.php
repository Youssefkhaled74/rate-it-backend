<!-- Dropdown/Select Component -->
@props([
    'name' => '',
    'label' => '',
    'options' => [],
    'value' => '',
    'error' => null,
    'required' => false,
    'placeholder' => 'Select an option',
    'multiple' => false,
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

    <select 
        @if ($name) name="{{ $name }}" id="{{ $name }}" @endif
        @if ($multiple) multiple @endif
        @class([
            'w-full px-4 py-2.5 rounded-xl bg-[var(--surface-2)] border transition-all duration-200 text-[var(--text-primary)]',
            'focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:ring-offset-2',
            'border-[var(--danger)]' => $error || $errors->has($name),
            'border-[var(--border)]' => !$error && !$errors->has($name),
        ])
        {{ $attributes }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected($value == $optionValue)>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @if ($error || $errors->has($name))
        <p class="text-xs text-[var(--danger)] font-medium">
            {{ $error || $errors->first($name) }}
        </p>
    @endif
</div>
