<!-- Pagination Component -->
@props([
    'paginator' => null,
    'total' => 0,
    'perPage' => 10,
    'currentPage' => 1,
])

@if ($paginator && $paginator->hasPages())
<div class="flex items-center justify-between px-6 py-4 bg-[var(--surface)] border-t border-[var(--border)] rounded-b-2xl">
    <!-- Info -->
    <div class="text-sm text-[var(--text-secondary)]">
        {{ session('rtl') ? 'يعرض' : 'Showing' }} 
        <span class="font-semibold">{{ $paginator->firstItem() }}</span> 
        {{ session('rtl') ? 'إلى' : 'to' }}
        <span class="font-semibold">{{ $paginator->lastItem() }}</span>
        {{ session('rtl') ? 'من' : 'of' }}
        <span class="font-semibold">{{ $paginator->total() }}</span>
        {{ session('rtl') ? 'النتائج' : 'results' }}
    </div>

    <!-- Links -->
    <div class="flex items-center gap-2">
        <!-- Previous -->
        @if ($paginator->onFirstPage())
            <button disabled class="px-3 py-2 rounded-lg bg-[var(--surface-2)] text-[var(--text-tertiary)] cursor-not-allowed opacity-50">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            </button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 rounded-lg bg-[var(--surface-2)] text-[var(--text-primary)] hover:bg-[var(--surface-hover)] transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            </a>
        @endif

        <!-- Page Numbers -->
        <div class="flex gap-1">
            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <button disabled class="px-3 py-2 rounded-lg font-semibold bg-[var(--brand)] text-white">
                        {{ $page }}
                    </button>
                @else
                    <a href="{{ $url }}" class="px-3 py-2 rounded-lg font-semibold bg-[var(--surface-2)] text-[var(--text-primary)] hover:bg-[var(--surface-hover)] transition-colors">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
        </div>

        <!-- Next -->
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 rounded-lg bg-[var(--surface-2)] text-[var(--text-primary)] hover:bg-[var(--surface-hover)] transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </a>
        @else
            <button disabled class="px-3 py-2 rounded-lg bg-[var(--surface-2)] text-[var(--text-tertiary)] cursor-not-allowed opacity-50">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </button>
        @endif
    </div>
</div>
@endif
