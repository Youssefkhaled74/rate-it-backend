@props([
  'name',
  'label' => null,
  'options' => [],
  'selected' => null,
  'placeholder' => null,
  'required' => false,
  'id' => null,
  'help' => null,
  'wrapperClass' => '',
  'labelClass' => 'text-sm font-medium text-gray-700',
  'selectClass' => '',
])

@php
  $selectId = $id ?? $name;
  $selectedValue = old($name, $selected);
  $baseSelectClass = 'w-full rounded-2xl border border-red-200 bg-white px-4 py-3 pr-10 text-sm outline-none transition focus:border-red-400 focus:ring-4 focus:ring-red-100 appearance-none';
@endphp

<div class="{{ $wrapperClass }}">
  @if($label)
    <label for="{{ $selectId }}" class="{{ $labelClass }}">{{ $label }}</label>
  @endif

  <div class="relative {{ $label ? 'mt-2' : '' }}">
    <select
      name="{{ $name }}"
      id="{{ $selectId }}"
      {{ $required ? 'required' : '' }}
      {{ $attributes->merge(['class' => trim($baseSelectClass . ' ' . $selectClass)]) }}
    >
      @if(!is_null($placeholder))
        <option value="">{{ $placeholder }}</option>
      @endif

      @if(!empty($options))
        @foreach($options as $value => $text)
          <option value="{{ $value }}" {{ (string) $selectedValue === (string) $value ? 'selected' : '' }}>
            {{ $text }}
          </option>
        @endforeach
      @else
        {{ $slot }}
      @endif
    </select>

    <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-gray-700">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M6 9l6 6 6-6"/>
      </svg>
    </span>
  </div>

  @if($help)
    <p class="mt-1 text-xs text-gray-500">{{ $help }}</p>
  @endif
</div>
