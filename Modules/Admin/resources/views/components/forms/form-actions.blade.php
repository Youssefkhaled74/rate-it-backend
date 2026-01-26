<!-- Form Actions Component (Sticky Footer) -->
@props([
    'submitLabel' => 'Save',
    'cancelHref' => '#',
    'loading' => false,
])

<div class="fixed bottom-0 left-0 lg:left-64 right-0 bg-[var(--surface)] border-t border-[var(--border)] px-4 md:px-8 py-6 z-20">
    <div class="flex items-center justify-end gap-3 max-w-full">
        <a href="{{ $cancelHref }}" class="px-6 py-2.5 rounded-2xl bg-[var(--surface-2)] text-[var(--text-primary)] font-semibold hover:bg-[var(--surface-hover)] transition-colors">
            {{ session('rtl') ? 'إلغاء' : 'Cancel' }}
        </a>
        <button type="submit" 
                class="px-6 py-2.5 rounded-2xl bg-[var(--brand)] text-white font-semibold hover:bg-red-700 transition-colors flex items-center gap-2 disabled:opacity-50"
                @if ($loading) disabled @endif>
            @if ($loading)
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle></svg>
            @endif
            {{ $submitLabel }}
        </button>
    </div>
</div>

<!-- Spacer to prevent content overlap -->
<div class="h-20"></div>
