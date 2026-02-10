<!doctype html>
<html lang="{{ app()->getLocale() }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', __('vendor.panel'))</title>

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
        darkMode: 'class'
      }
    </script>
  @endif
  <script>
    (function() {
      const saved = localStorage.getItem('vendor_theme');
      const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      const initial = saved || (prefersDark ? 'dark' : 'light');
      if (initial === 'dark') document.documentElement.classList.add('dark');
    })();
  </script>

  @stack('styles')
  <style>
    body {
      font-feature-settings: "cv02", "cv03", "cv04", "cv11";
      font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
    }
    [dir="rtl"] body { font-family: 'Almarai', ui-sans-serif, system-ui, -apple-system, sans-serif; }
    :root { --sidebar-w: 16.5rem; --sidebar-gap: 1.5rem; }
    .vendor-sidebar-spacer { width: 0 !important; padding: 0 !important; }
    [dir="rtl"] .vendor-shell { flex-direction: row-reverse; }
    [dir="rtl"] .vendor-sidebar-fixed { right: var(--sidebar-gap); left: auto; }
    .vendor-main { margin-inline-start: calc(var(--sidebar-w) + var(--sidebar-gap)); }
    [dir="rtl"] .vendor-main { padding-right: 1.5rem; padding-left: 1.5rem; }

    .vendor-logo-row { gap: 0.75rem; }
    [dir="rtl"] .vendor-logo-row { flex-direction: row-reverse; text-align: right; }
    .vendor-nav-item { border-radius: 9999px; }
    .vendor-nav-item .vendor-nav-text { white-space: nowrap; }
    [dir="rtl"] .vendor-nav-item { flex-direction: row-reverse; }
    [dir="rtl"] .vendor-nav-item .vendor-nav-text { text-align: right; }
    [dir="rtl"] .vendor-logout { flex-direction: row-reverse; text-align: right; }
    [dir="rtl"] .vendor-sidebar-inner { text-align: right; }
    [dir="rtl"] .vendor-nav-item { justify-content: flex-start; }

    .vendor-shell { min-height: 100vh; }
    .vendor-sidebar { width: var(--sidebar-w); }
    .vendor-sidebar-inner {
      background: #8b1c1c;
      border-radius: 3rem;
      padding-block: 1.75rem;
      padding-inline: 1.35rem;
      box-shadow: 0 30px 90px rgba(0,0,0,.18);
    }
    .vendor-nav-scroll { scrollbar-width: none; }
    .vendor-nav-scroll::-webkit-scrollbar { width: 0; height: 0; }
    [dir="rtl"] .vendor-nav-scroll { padding-left: 0.25rem; padding-right: 0; }
    .vendor-nav-item {
      padding-block: 0.7rem;
      padding-inline: 1rem;
      color: rgba(255,255,255,.9);
    }
    .vendor-nav-item:hover { background: rgba(255,255,255,.12); }
    .vendor-nav-item.is-active { background: rgba(255,255,255,.22); color: #fff; }
    .vendor-nav-icon { color: #fff; opacity: .95; }
    .vendor-logout { padding-block: 0.75rem; padding-inline: 1rem; }
    [dir="rtl"] .vendor-main .max-w-md { margin-right: auto; margin-left: 0; }
    [dir="rtl"] .vendor-main input,
    [dir="rtl"] .vendor-main textarea,
    [dir="rtl"] .vendor-main select { text-align: right; }

    [dir="rtl"] .rtl-icon-left { left: auto !important; right: 0.75rem !important; }
    [dir="rtl"] .rtl-icon-right { right: auto !important; left: 0.75rem !important; }
    [dir="rtl"] .rtl-menu-right { right: auto !important; left: 0 !important; }
    [dir="rtl"] .rtl-search-input { padding-right: 2.75rem !important; padding-left: 1rem !important; }
    [dir="rtl"] .rtl-search-icon { left: auto !important; right: 0.75rem !important; }
    [dir="rtl"] .rtl-gap { flex-direction: row-reverse; }

    .dark .vendor-main input,
    .dark .vendor-main select,
    .dark .vendor-main textarea {
      background-color: #0f172a;
      border-color: #334155;
      color: #e2e8f0;
    }
    .dark .vendor-main input::placeholder,
    .dark .vendor-main textarea::placeholder {
      color: #94a3b8;
    }
    .dark .vendor-main table { color: #e2e8f0; }
    .dark .vendor-main thead { color: #94a3b8; }

    .vendor-container { max-width: 100%; margin: 0; }
    .vendor-card {
      background: #fff;
      border: 1px solid #f3f4f6;
      border-radius: 1.25rem;
      padding: 1.25rem;
      box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
    }
    .dark .vendor-card {
      background: #0f172a;
      border-color: #1f2937;
      box-shadow: 0 12px 36px rgba(0,0,0,0.35);
    }
  </style>
</head>

<body class="bg-gray-100 text-gray-900 dark:bg-slate-950 dark:text-gray-100 font-sans antialiased">
  <div class="min-h-screen flex vendor-shell">
    @include('vendor.partials.sidebar')

    <div class="flex-1 overflow-y-auto">
      <div class="vendor-container px-6 py-6">
        @include('vendor.partials.topbar')

        <div class="mt-6">
          @yield('content')
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('js/vendor-theme.js') }}"></script>
  @stack('scripts')
</body>
</html>
