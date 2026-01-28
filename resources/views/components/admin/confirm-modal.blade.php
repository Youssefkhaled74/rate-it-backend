@once
<div id="confirmModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
  <div class="fixed inset-0 bg-black/40 transition-opacity" data-modal-close></div>

  <div class="fixed inset-0 flex items-center justify-center p-4">
    <div role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle" aria-describedby="confirmModalDesc"
         class="w-full max-w-lg bg-white rounded-2xl shadow-soft p-6 ring-1 ring-black/5" data-modal>

      <header class="mb-4">
        <h3 id="confirmModalTitle" class="text-lg font-semibold text-gray-900">Title</h3>
      </header>

      <div id="confirmModalDesc" class="text-sm text-gray-600 mb-6">Message</div>

      <div class="flex justify-end gap-3">
        <button type="button" class="rounded-2xl bg-gray-100 text-gray-800 px-4 py-2 text-sm" data-modal-cancel>Cancel</button>
        <button type="button" class="rounded-2xl bg-red-800 text-white px-4 py-2 text-sm" data-modal-confirm>Delete</button>
      </div>
    </div>
  </div>
</div>
@endonce
