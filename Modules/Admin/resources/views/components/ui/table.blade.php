<!-- Table Component -->
@props([
    'headers' => [],
    'actions' => true,
])

<div class="bg-[var(--surface)] border border-[var(--border)] rounded-2xl shadow-[var(--shadow)] overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <!-- Header -->
            <thead class="bg-[var(--surface-2)] border-b border-[var(--border)]">
                <tr>
                    @foreach ($headers as $header)
                        <th class="px-6 py-4 text-left text-xs font-bold text-[var(--text-secondary)] uppercase tracking-wide">
                            {{ $header }}
                        </th>
                    @endforeach
                    @if ($actions)
                        <th class="px-6 py-4 text-left text-xs font-bold text-[var(--text-secondary)] uppercase tracking-wide">
                            {{ session('rtl') ? 'الإجراءات' : 'Actions' }}
                        </th>
                    @endif
                </tr>
            </thead>

            <!-- Body -->
            <tbody class="divide-y divide-[var(--border)]">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>

@isset($empty)
    <!-- Empty State (if no rows) -->
    <div class="text-center py-12 bg-[var(--surface)] rounded-2xl border border-[var(--border)]">
        {{ $empty }}
    </div>
@endisset
