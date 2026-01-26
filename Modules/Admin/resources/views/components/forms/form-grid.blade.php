<!-- Form Grid Component -->
@props([
    'columns' => 2,
])

<div @class([
    'grid gap-6',
    'grid-cols-1' => $columns === 1,
    'grid-cols-1 md:grid-cols-2' => $columns === 2,
    'grid-cols-1 md:grid-cols-3' => $columns === 3,
])>
    {{ $slot }}
</div>
