<!-- Skeleton Loader Component -->
@props([
    'count' => 1,
    'type' => 'row', // row, card, line
])

@if ($type === 'card')
    @for ($i = 0; $i < $count; $i++)
        <div class="bg-[var(--surface)] border border-[var(--border)] rounded-2xl p-6 space-y-4 animate-pulse">
            <div class="h-4 bg-[var(--surface-2)] rounded w-3/4"></div>
            <div class="space-y-3">
                <div class="h-3 bg-[var(--surface-2)] rounded"></div>
                <div class="h-3 bg-[var(--surface-2)] rounded w-5/6"></div>
                <div class="h-3 bg-[var(--surface-2)] rounded w-4/6"></div>
            </div>
        </div>
    @endfor
@elseif ($type === 'line')
    @for ($i = 0; $i < $count; $i++)
        <div class="h-3 bg-[var(--surface-2)] rounded animate-pulse mb-3"></div>
    @endfor
@else
    <!-- Row skeleton for tables -->
    @for ($i = 0; $i < $count; $i++)
        <div class="p-6 border-b border-[var(--border)] last:border-b-0 flex gap-4 animate-pulse">
            <div class="h-10 w-10 bg-[var(--surface-2)] rounded-lg flex-shrink-0"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-[var(--surface-2)] rounded w-1/4"></div>
                <div class="h-3 bg-[var(--surface-2)] rounded w-1/3"></div>
            </div>
            <div class="h-10 w-10 bg-[var(--surface-2)] rounded-lg flex-shrink-0"></div>
        </div>
    @endfor
@endif
