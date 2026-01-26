<!-- Breadcrumbs Navigation -->
@if (isset($breadcrumbs) && count($breadcrumbs) > 0)
<nav class="flex items-center gap-2 text-sm mb-8">
    @foreach ($breadcrumbs as $key => $breadcrumb)
        @if (is_array($breadcrumb))
            <a href="{{ $breadcrumb['url'] }}" class="text-[var(--brand)] hover:underline font-medium">
                {{ $breadcrumb['label'] }}
            </a>
        @else
            <span class="text-[var(--text-primary)] font-medium">
                {{ $breadcrumb }}
            </span>
        @endif

        @if (!$loop->last)
            <svg class="w-4 h-4 text-[var(--text-tertiary)]" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
        @endif
    @endforeach
</nav>
@endif
