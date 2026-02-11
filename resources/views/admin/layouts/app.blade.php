<!doctype html>
<html lang="{{ app()->getLocale() }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'Admin')</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700&display=swap" rel="stylesheet">

  @if (file_exists(public_path('build/manifest.json')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  @else
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'],
              ar: ['Almarai', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'],
            },
          },
        },
      }
    </script>
  @endif
  
  @stack('styles')
  <style>
    /* Ensures the font looks crisp on all browsers */
    body {
      font-feature-settings: "cv02", "cv03", "cv04", "cv11";
      font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
    }
    [dir="rtl"] body { font-family: 'Almarai', ui-sans-serif, system-ui, -apple-system, sans-serif; }
    /* Basic RTL layout fixes */
    :root {
      --sidebar-w: 16.5rem;
      --sidebar-gap: 1.25rem;
    }
    /* Sidebar is fixed, so remove flow space to avoid double gaps */
    .admin-sidebar-spacer { width: 0 !important; padding: 0 !important; }
    [dir="rtl"] .admin-shell { flex-direction: row-reverse; }
    [dir="rtl"] .admin-sidebar-fixed { right: var(--sidebar-gap); left: auto; }
    .admin-main { margin-inline-start: calc(var(--sidebar-w) + var(--sidebar-gap)); }
    [dir="rtl"] .admin-main { padding-right: 1.5rem; padding-left: 1.5rem; }
    /* Sidebar nav look + RTL mirroring */
    .admin-logo-row { gap: 0.75rem; }
    [dir="rtl"] .admin-logo-row { flex-direction: row-reverse; text-align: right; }
    .admin-nav-item { border-radius: 9999px; }
    .admin-nav-item .admin-nav-text { white-space: nowrap; }
    [dir="rtl"] .admin-nav-item { flex-direction: row-reverse; }
    [dir="rtl"] .admin-nav-item .admin-nav-text { text-align: right; }
    [dir="rtl"] .admin-logout { flex-direction: row-reverse; text-align: right; }
    [dir="rtl"] .admin-sidebar-inner { text-align: right; }
    [dir="rtl"] .admin-nav-item { justify-content: flex-end; }

    /* Mobile sidebar */
    @media (max-width: 1024px) {
      :root {
        --sidebar-w: 15.5rem;
        --sidebar-gap: 0.75rem;
      }
      .admin-main { margin-inline-start: 0; padding: 1rem; }
      .admin-sidebar-spacer { display: none !important; }
      .admin-sidebar-fixed {
        position: fixed !important;
        top: 0.75rem;
        bottom: 0.75rem;
        left: 0.75rem;
        transform: translateX(-110%);
        transition: transform 200ms ease;
        z-index: 60;
      }
      [dir="rtl"] .admin-sidebar-fixed {
        left: auto;
        right: 0.75rem;
        transform: translateX(110%);
      }
      body.admin-sidebar-open .admin-sidebar-fixed {
        transform: translateX(0);
      }
      .admin-sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.25);
        opacity: 0;
        pointer-events: none;
        transition: opacity 200ms ease;
        z-index: 50;
      }
      body.admin-sidebar-open .admin-sidebar-overlay {
        opacity: 1;
        pointer-events: auto;
      }
    }

    /* Sidebar layout matching screenshot */
    .admin-sidebar { width: var(--sidebar-w); }
    .admin-sidebar-inner {
      background: #8b1c1c;
      border-radius: 3rem;
      padding-block: 1.75rem;
      padding-inline: 1.35rem;
      box-shadow: 0 30px 90px rgba(0,0,0,.18);
    }
    .admin-nav-scroll { scrollbar-width: none; }
    .admin-nav-scroll::-webkit-scrollbar { width: 0; height: 0; }
    [dir="rtl"] .admin-nav-scroll { padding-left: 0.25rem; padding-right: 0; }
    .admin-nav-item {
      padding-block: 0.7rem;
      padding-inline: 1rem;
      color: rgba(255,255,255,.9);
    }
    .admin-nav-item:hover { background: rgba(255,255,255,.12); }
    .admin-nav-item.is-active { background: rgba(255,255,255,.22); color: #fff; }
    .admin-nav-icon { color: #fff; opacity: .95; }
    .admin-logout { padding-block: 0.75rem; padding-inline: 1rem; }
    [dir="rtl"] .admin-main .max-w-md { margin-right: auto; margin-left: 0; }
    [dir="rtl"] .admin-main input,
    [dir="rtl"] .admin-main textarea,
    [dir="rtl"] .admin-main select { text-align: right; }

    /* Common absolute icon + menu positions */
    [dir="rtl"] .rtl-icon-left { left: auto !important; right: 0.75rem !important; }
    [dir="rtl"] .rtl-icon-right { right: auto !important; left: 0.75rem !important; }
    [dir="rtl"] .rtl-menu-right { right: auto !important; left: 0 !important; }

    /* Search inputs with icon */
    [dir="rtl"] .rtl-search-input { padding-right: 2.75rem !important; padding-left: 1rem !important; }
    [dir="rtl"] .rtl-search-icon { left: auto !important; right: 0.75rem !important; }

    /* Toggle and dots menus */
    [dir="rtl"] .rtl-toggle { left: auto !important; right: 0.75rem !important; }
    [dir="rtl"] .rtl-dots { right: auto !important; left: 0.75rem !important; }
    [dir="rtl"] .rtl-chip { text-align: right; }
    [dir="rtl"] .rtl-gap { flex-direction: row-reverse; }
  </style>
</head>

<body class="bg-gray-100 text-gray-900 font-sans antialiased">
  <div class="min-h-screen flex admin-shell">
    {{-- Sidebar --}}
    @include('admin.partials.sidebar')
    <div class="admin-sidebar-overlay" data-admin-sidebar-overlay></div>

    {{-- Main --}}
    <div class="flex-1 p-6 admin-main">
      @include('admin.partials.topbar')

      <div class="mt-6">
        @yield('content')
      </div>
    </div>
  </div>

  {{-- Confirm modal (global) + inline script to handle data-confirm attributes --}}
  @includeWhen(true, 'components.admin.confirm-modal')

  <script id="admin-i18n" type="application/json">
    {!! json_encode([
      'confirm' => __('admin.confirm'),
      'pleaseConfirm' => __('admin.please_confirm'),
    ], JSON_UNESCAPED_UNICODE) !!}
  </script>

  <script>
    (function(){
      const sidebarOverlay = document.querySelector('[data-admin-sidebar-overlay]');
      document.addEventListener('click', function(e){
        const btn = e.target.closest('[data-admin-sidebar-toggle]');
        if (btn) {
          e.preventDefault();
          document.body.classList.toggle('admin-sidebar-open');
          return;
        }
        if (sidebarOverlay && e.target === sidebarOverlay) {
          document.body.classList.remove('admin-sidebar-open');
        }
      });

      // Simple confirm modal handler
      const modal = document.getElementById('confirmModal');
      if (!modal) return;
      const titleEl = modal.querySelector('#confirmModalTitle');
      const descEl = modal.querySelector('#confirmModalDesc');
      const btnConfirm = modal.querySelector('[data-modal-confirm]');
      const btnCancel = modal.querySelector('[data-modal-cancel]');
      const overlay = modal.querySelector('[data-modal-close]');

      let pendingForm = null;

      const i18nEl = document.getElementById('admin-i18n');
      const i18n = i18nEl ? JSON.parse(i18nEl.textContent) : { confirm: 'Confirm', pleaseConfirm: 'Please confirm' };

      function openModal(opts){
        pendingForm = opts.form || null;
        titleEl.textContent = opts.title || i18n.confirm;
        descEl.textContent = opts.message || '';
        btnConfirm.textContent = opts.confirmText || i18n.confirm;
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
          title: el.getAttribute('data-title') || i18n.pleaseConfirm,
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
  @stack('scripts')
</body>
</html>
