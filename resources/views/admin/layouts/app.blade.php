<!doctype html>
<html lang="{{ app()->getLocale() }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'Admin')</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-900">
  <div class="min-h-screen flex">
    {{-- Sidebar --}}
    @include('admin.partials.sidebar')

    {{-- Main --}}
    <div class="flex-1 p-6">
      @include('admin.partials.topbar')

      <div class="mt-6">
        @yield('content')
      </div>
    </div>
  </div>

  {{-- Confirm modal (global) + inline script to handle data-confirm attributes --}}
  @includeWhen(true, 'components.admin.confirm-modal')

  <script>
    (function(){
      // Simple confirm modal handler
      const modal = document.getElementById('confirmModal');
      if (!modal) return;
      const titleEl = modal.querySelector('#confirmModalTitle');
      const descEl = modal.querySelector('#confirmModalDesc');
      const btnConfirm = modal.querySelector('[data-modal-confirm]');
      const btnCancel = modal.querySelector('[data-modal-cancel]');
      const overlay = modal.querySelector('[data-modal-close]');

      let pendingForm = null;

      function openModal(opts){
        pendingForm = opts.form || null;
        titleEl.textContent = opts.title || 'Confirm';
        descEl.textContent = opts.message || '';
        btnConfirm.textContent = opts.confirmText || 'Confirm';
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden','false');
        // focus confirm
        btnConfirm.focus();
        // key handler
        document.addEventListener('keydown', keyHandler);
      }

      function closeModal(){
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden','true');
        pendingForm = null;
        document.removeEventListener('keydown', keyHandler);
      }

      function keyHandler(e){
        if (e.key === 'Escape') closeModal();
        if (e.key === 'Enter' && document.activeElement === btnConfirm){
          doConfirm();
        }
      }

      function doConfirm(){
        if (!pendingForm) return closeModal();
        // submit the form (respect method spoofing and csrf)
        pendingForm.submit();
        closeModal();
      }

      // capture clicks on elements with data-confirm attribute
      document.addEventListener('click', function(ev){
        const el = ev.target.closest('[data-confirm]');
        if (!el) return;
        // find closest form
        const form = el.closest('form') || document.querySelector(`form[data-confirm-target="${el.getAttribute('data-confirm')}"]`);
        if (!form) return; // no form to submit
        ev.preventDefault();
        openModal({
          title: el.getAttribute('data-title') || 'Please confirm',
          message: el.getAttribute('data-message') || '',
          confirmText: el.getAttribute('data-confirm-text') || 'Confirm',
          form: form
        });
      }, false);

      btnCancel?.addEventListener('click', closeModal);
      overlay?.addEventListener('click', closeModal);
      btnConfirm?.addEventListener('click', doConfirm);

    })();
  </script>
</body>
</html>
