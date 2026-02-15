{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="theme-color" content="#ab171d" />
  <title>{{ config('app.name', 'Rate It') }}</title>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;700&family=Outfit:wght@400;500;600;700;800&display=swap');

    :root {
      --bg: #f8fafc;
      --surface: #ffffff;
      --surface-2: #f1f5f9;
      --text: #0f172a;
      --muted: #475569;
      --line: #e2e8f0;
      --brand: #ab171d;
      --brand-2: #ab171d;
      --ok: #10b981;
      --warn: #f59e0b;
      --sky: #0ea5e9;
      --radius: 18px;
      --shadow: 0 18px 50px rgba(2, 6, 23, .08);
      --ring: 0 0 0 3px rgba(239, 68, 68, .25);
    }

    body.dark {
      --bg: #020617;
      --surface: #0f172a;
      --surface-2: #111827;
      --text: #f8fafc;
      --muted: #94a3b8;
      --line: #1e293b;
      --shadow: 0 22px 55px rgba(0, 0, 0, .45);
    }

    * { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; }

    body {
      background: radial-gradient(900px 400px at 20% -10%, rgba(239, 68, 68, .14), transparent 60%),
                  radial-gradient(900px 400px at 95% 0%, rgba(14, 165, 233, .08), transparent 62%),
                  var(--bg);
      color: var(--text);
      font-family: "Outfit", "IBM Plex Sans Arabic", sans-serif;
      min-height: 100vh;
    }

    [dir="rtl"] body {
      font-family: "IBM Plex Sans Arabic", "Outfit", sans-serif;
    }

    a { color: inherit; text-decoration: none; }
    button { font-family: inherit; }

    .container {
      max-width: 1160px;
      margin: 0 auto;
      padding: 20px;
    }

    .nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      margin-bottom: 18px;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .logo {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      background: linear-gradient(135deg, var(--brand), var(--brand-2));
      display: grid;
      place-items: center;
      box-shadow: 0 10px 30px rgba(185, 28, 28, .35);
    }

    .brand small { display: block; color: var(--muted); font-size: 12px; }
    .brand b { font-size: 20px; }

    .navActions {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1px solid var(--line);
      background: var(--surface);
      color: var(--text);
      border-radius: 999px;
      padding: 10px 16px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: .2s ease;
    }

    .btn:hover { transform: translateY(-1px); box-shadow: var(--shadow); }
    .btn:focus { outline: none; box-shadow: var(--ring); }

    .btnPrimary {
      border-color: transparent;
      background: linear-gradient(135deg, var(--brand), var(--brand-2));
      color: #fff;
    }

    .hero {
      background: var(--surface);
      border: 1px solid var(--line);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 28px;
      display: grid;
      grid-template-columns: 1.15fr .85fr;
      gap: 24px;
    }

    .pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border: 1px solid var(--line);
      background: var(--surface-2);
      border-radius: 999px;
      padding: 8px 12px;
      color: var(--muted);
      font-size: 12px;
      font-weight: 600;
      margin-bottom: 14px;
    }

    .dot { width: 8px; height: 8px; border-radius: 50%; }

    .hero h1 {
      margin: 0;
      font-size: 52px;
      line-height: 1.05;
      letter-spacing: -.02em;
    }

    .hero h1 span { display: block; }
    .hero h1 .muted { color: var(--muted); }

    .hero p {
      margin: 14px 0 0;
      color: var(--muted);
      line-height: 1.8;
      font-size: 17px;
    }

    .cta {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 18px;
    }

    .hint {
      margin-top: 10px;
      font-size: 13px;
      color: var(--muted);
    }

    .trust {
      margin-top: 18px;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .trust .item {
      border: 1px solid var(--line);
      background: var(--surface-2);
      border-radius: 999px;
      padding: 8px 12px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--muted);
      font-size: 13px;
      font-weight: 600;
    }

    .panel {
      border: 1px solid var(--line);
      border-radius: 14px;
      background: var(--surface-2);
      padding: 18px;
      display: grid;
      gap: 12px;
      align-content: start;
    }

    .panel h3 {
      margin: 0;
      font-size: 20px;
    }

    .steps {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 12px;
      margin-top: 20px;
    }

    .step {
      border: 1px solid var(--line);
      background: var(--surface);
      border-radius: 14px;
      padding: 16px;
    }

    .step b { display: block; margin-bottom: 6px; }
    .step p { margin: 0; color: var(--muted); font-size: 14px; line-height: 1.7; }

    .section {
      margin-top: 20px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: var(--surface);
      box-shadow: var(--shadow);
      padding: 24px;
    }

    .sectionHead {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 14px;
    }

    .sectionHead h2 { margin: 0; }

    .portals {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 12px;
    }

    .portal {
      border: 1px solid var(--line);
      border-radius: 14px;
      background: var(--surface-2);
      padding: 16px;
      display: grid;
      gap: 10px;
    }

    .portalTitle {
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 700;
      font-size: 17px;
    }

    .portalDesc {
      color: var(--muted);
      line-height: 1.7;
      min-height: 76px;
      font-size: 14px;
    }

    .download {
      margin-top: 14px;
      border: 1px dashed var(--line);
      border-radius: 14px;
      background: linear-gradient(135deg, rgba(239, 68, 68, .07), rgba(14, 165, 233, .08));
      padding: 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      flex-wrap: wrap;
    }

    .stores { display: flex; gap: 8px; flex-wrap: wrap; }

    .footer {
      margin-top: 16px;
      display: flex;
      justify-content: space-between;
      gap: 10px;
      flex-wrap: wrap;
      color: var(--muted);
      font-size: 13px;
      padding: 0 2px;
    }

    @media (max-width: 980px) {
      .hero { grid-template-columns: 1fr; }
      .hero h1 { font-size: 42px; }
      .steps { grid-template-columns: 1fr; }
      .portals { grid-template-columns: 1fr; }
    }

    @media (max-width: 640px) {
      .container { padding: 14px; }
      .hero { padding: 20px; }
      .hero h1 { font-size: 34px; }
      .nav { flex-direction: column; align-items: flex-start; }
    }

    @media (prefers-reduced-motion: reduce) {
      * { transition: none !important; }
    }
  </style>
</head>

<body class="{{ session('theme','light') === 'dark' ? 'dark' : '' }}">
  <div class="container">
    <header class="nav">
      <div class="brand">
        <div class="logo" aria-hidden="true">
          <img
            src="{{ asset('assets/images/Vector.png') }}"
            alt="Rateit Logo"
            style="width:24px;height:24px;object-fit:contain;"
            onerror="this.onerror=null;this.src='{{ asset('assets/images/category-icon-placeholder.png') }}';"
          >
        </div>
        <div>
          <small>{{ __('landing.kicker') }}</small>
          <b>Rateit</b>
        </div>
      </div>

      <div class="navActions">
        <a class="btn" href="{{ url('/app') }}">{{ __('landing.tab_user') }}</a>
        <a class="btn" href="{{ url('/vendor/verify') }}">{{ __('landing.tab_gate') }}</a>
        <a class="btn" href="{{ url('/admin/login') }}">{{ __('landing.tab_admin') }}</a>
        <a class="btn" href="{{ request()->fullUrlWithQuery(['lang' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}">
          {{ app()->getLocale() === 'ar' ? 'EN' : 'AR' }}
        </a>
        <button class="btn" id="themeToggle" type="button">{{ __('landing.theme_toggle') }}</button>
      </div>
    </header>

    <main class="hero">
      <section>
        <div class="pill">
          <span class="dot" style="background:var(--ok)"></span>
          {{ __('landing.badge') }}
        </div>

        <h1>
          <span>{{ __('landing.hero_title_line1') }}</span>
          <span>{{ __('landing.hero_title_line2') }}</span>
          <span class="muted">{{ __('landing.hero_title_line3') }}</span>
        </h1>

        <p>{{ __('landing.hero_subtitle') }}</p>

        <div class="cta">
          <a class="btn btnPrimary" href="{{ url('/app') }}">{{ __('landing.cta_user') }}</a>
          <a class="btn" href="{{ url('/vendor/login') }}">{{ __('landing.cta_vendor') }}</a>
          <a class="btn" href="{{ url('/admin/login') }}">{{ __('landing.cta_admin') }}</a>
        </div>
        <div class="hint">{{ __('landing.cta_hint') }}</div>

        <div class="trust">
          <span class="item"><span class="dot" style="background:var(--ok)"></span>{{ __('landing.trust_qr') }}</span>
          <span class="item"><span class="dot" style="background:var(--warn)"></span>{{ __('landing.trust_points') }}</span>
          <span class="item"><span class="dot" style="background:var(--sky)"></span>{{ __('landing.trust_secure') }}</span>
          <span class="item"><span class="dot" style="background:var(--brand)"></span>{{ __('landing.trust_moderation') }}</span>
        </div>
      </section>

      <aside class="panel">
        <h3>{{ __('landing.portals_title') }}</h3>
        <div class="pill" style="margin:0; width:max-content;">
          <span class="dot" style="background:var(--ok)"></span>
          {{ __('landing.portals_live') }}
        </div>
        <div class="step">
          <b>{{ __('landing.portal_admin_title') }}</b>
          <p>{{ __('landing.portal_admin_desc') }}</p>
        </div>
        <div class="step">
          <b>{{ __('landing.portal_vendor_title') }}</b>
          <p>{{ __('landing.portal_vendor_desc') }}</p>
        </div>
        <div class="step">
          <b>{{ __('landing.portal_branch_title') }}</b>
          <p>{{ __('landing.portal_branch_desc') }}</p>
        </div>
      </aside>
    </main>

    <section class="steps">
      <article class="step">
        <b>{{ __('landing.step1_title') }}</b>
        <p>{{ __('landing.step1_desc') }}</p>
      </article>
      <article class="step">
        <b>{{ __('landing.step2_title') }}</b>
        <p>{{ __('landing.step2_desc') }}</p>
      </article>
      <article class="step">
        <b>{{ __('landing.step3_title') }}</b>
        <p>{{ __('landing.step3_desc') }}</p>
      </article>
    </section>

    <section class="section">
      <div class="sectionHead">
        <h2>{{ __('landing.portals_title') }}</h2>
        <span class="pill" style="margin:0;">
          <span class="dot" style="background:var(--ok)"></span>
          {{ __('landing.portals_live') }}
        </span>
      </div>

      <div class="portals">
        <article class="portal">
          <div class="portalTitle"><span class="dot" style="background:var(--warn)"></span>{{ __('landing.portal_admin_title') }}</div>
          <div class="portalDesc">{{ __('landing.portal_admin_desc') }}</div>
          <a class="btn btnPrimary" href="{{ url('/admin/login') }}">{{ __('landing.portal_admin_cta') }}</a>
        </article>
        <article class="portal">
          <div class="portalTitle"><span class="dot" style="background:var(--ok)"></span>{{ __('landing.portal_vendor_title') }}</div>
          <div class="portalDesc">{{ __('landing.portal_vendor_desc') }}</div>
          <a class="btn btnPrimary" href="{{ url('/vendor/login') }}">{{ __('landing.portal_vendor_cta') }}</a>
        </article>
        <article class="portal">
          <div class="portalTitle"><span class="dot" style="background:var(--sky)"></span>{{ __('landing.portal_branch_title') }}</div>
          <div class="portalDesc">{{ __('landing.portal_branch_desc') }}</div>
          <a class="btn btnPrimary" href="{{ url('/vendor/verify') }}">{{ __('landing.portal_branch_cta') }}</a>
        </article>
      </div>

      <div class="download">
        <div>
          <div style="font-weight:700; margin-bottom:4px;">{{ __('landing.app_title') }}</div>
          <div style="font-size:14px; color:var(--muted);">{{ __('landing.app_desc') }}</div>
        </div>
        <div class="stores">
          <a class="btn" href="{{ url('/app/download?platform=android') }}">{{ __('landing.app_google') }}</a>
          <a class="btn" href="{{ url('/app/download?platform=ios') }}">{{ __('landing.app_apple') }}</a>
        </div>
      </div>
    </section>

    <footer class="footer">
      <div>{{ __('landing.footer_left') }}</div>
      <div>&copy; {{ date('Y') }} EVYX</div>
    </footer>
  </div>

  <script>
    (function(){
      const body = document.body;
      const btn = document.getElementById('themeToggle');
      if (!btn) return;

      const stored = localStorage.getItem('theme');
      if (stored === 'dark') {
        body.classList.add('dark');
      }

      btn.addEventListener('click', function(){
        body.classList.toggle('dark');
        localStorage.setItem('theme', body.classList.contains('dark') ? 'dark' : 'light');
      });
    })();
  </script>
</body>
</html>
