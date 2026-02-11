{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="theme-color" content="#111111" />
  <title>{{ config('app.name', 'Rate It') }}</title>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;500;700&family=Tajawal:wght@300;500;700&family=Space+Grotesk:wght@400;500;600;700&display=swap');

    :root{
      --red:#b91c1c;
      --red-2:#ef4444;
      --bg:#f6f7f8;
      --fg:#111316;
      --muted:#5d636f;
      --muted-2:#818694;
      --line:rgba(0,0,0,.08);
      --glass:rgba(255,255,255,.70);
      --shadow:0 30px 80px rgba(0,0,0,.12);
      --shadow-strong:0 40px 110px rgba(0,0,0,.16);
      --radius:24px;
      --radius-sm:16px;
      --ring:0 0 0 3px rgba(239,68,68,.25);
      --success:#10b981;
      --amber:#f59e0b;
      --sky:#38bdf8;
    }

    .dark{
      --bg:#0a0b0f;
      --fg:#f8fafc;
      --muted:#c7ccd8;
      --muted-2:#9aa3b2;
      --line:rgba(255,255,255,.12);
      --glass:rgba(12,14,18,.72);
      --shadow:0 40px 120px rgba(0,0,0,.6);
      --shadow-strong:0 40px 140px rgba(0,0,0,.75);
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      background: var(--bg);
      color: var(--fg);
      font-family: "Space Grotesk", "IBM Plex Sans Arabic", "Tajawal", sans-serif;
    }
    [dir="rtl"] body{
      font-family: "IBM Plex Sans Arabic", "Tajawal", "Space Grotesk", sans-serif;
    }

    a{color:inherit;text-decoration:none}
    button{font-family:inherit}

    .page{
      min-height:100vh;
      position:relative;
      overflow:hidden;
      padding:24px;
      background:
        radial-gradient(900px 500px at 10% 12%, rgba(239,68,68,.18), transparent 60%),
        radial-gradient(900px 520px at 92% 18%, rgba(245,158,11,.10), transparent 62%),
        radial-gradient(1200px 760px at 50% 120%, rgba(56,189,248,.10), transparent 60%),
        linear-gradient(180deg, var(--bg), var(--bg));
      animation: bgFlow 18s ease-in-out infinite;
    }
    .dark .page{
      background:
        radial-gradient(900px 500px at 10% 12%, rgba(239,68,68,.22), transparent 62%),
        radial-gradient(900px 520px at 92% 18%, rgba(245,158,11,.16), transparent 62%),
        radial-gradient(1200px 760px at 50% 120%, rgba(56,189,248,.16), transparent 62%),
        linear-gradient(180deg, var(--bg), var(--bg));
    }

    @keyframes bgFlow{
      0%{background-position:0% 0%, 100% 0%, 50% 100%, 0 0;}
      50%{background-position:6% 4%, 94% 6%, 50% 98%, 0 0;}
      100%{background-position:0% 0%, 100% 0%, 50% 100%, 0 0;}
    }

    .noise{position:absolute; inset:0; pointer-events:none; opacity:.06; mix-blend-mode:overlay;
      background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='220' height='220'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='220' height='220' filter='url(%23n)' opacity='.55'/%3E%3C/svg%3E");
      background-size:220px 220px;
    }

    .container{max-width:1200px; margin:0 auto; position:relative; z-index:2;}

    .header{
      display:flex; align-items:center; justify-content:space-between; gap:12px;
      padding:8px 0 18px;
    }
    .brand{display:flex; align-items:center; gap:12px;}
    .logo{
      width:44px;height:44px;border-radius:14px;
      background: linear-gradient(135deg,var(--red),var(--red-2));
      display:grid;place-items:center;color:#fff;font-weight:700;
      box-shadow:0 16px 40px rgba(185,28,28,.35);
    }
    .brandText{display:flex; flex-direction:column; gap:2px;}
    .brandText .kicker{font-size:12px; color:var(--muted-2);}    
    .brandText .name{font-size:16px; font-weight:700; letter-spacing:-.01em;}

    .headerActions{display:flex; align-items:center; gap:10px;}
    .chip{
      display:inline-flex; align-items:center; gap:8px;
      padding:8px 12px; border-radius:999px; border:1px solid var(--line);
      background: var(--glass); color:var(--muted); font-size:12px; font-weight:600;
      backdrop-filter: blur(10px);
    }
    .btn{
      display:inline-flex; align-items:center; justify-content:center; gap:8px;
      padding:12px 16px; border-radius:999px; border:1px solid var(--line);
      background: var(--glass); color:var(--fg); font-size:14px; font-weight:600;
      cursor:pointer; transition:transform .15s ease, box-shadow .15s ease, border-color .15s ease, background .15s ease;
    }
    .btn:hover{transform:translateY(-1px); box-shadow:var(--shadow);}
    .btn:focus{outline:none; box-shadow:var(--ring);}

    .btnPrimary{
      background: linear-gradient(135deg,var(--red),var(--red-2));
      color:#fff; border-color:transparent; box-shadow:0 20px 50px rgba(185,28,28,.25);
    }
    .btnPrimary:focus{box-shadow:0 0 0 3px rgba(239,68,68,.35)}
    .btnGhost{background:transparent;}

    .main{
      display:grid; grid-template-columns:1.1fr .9fr; gap:24px;
    }

    .card{
      background: var(--glass);
      border:1px solid var(--line);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      backdrop-filter: blur(14px);
      padding:28px;
      position:relative;
      overflow:hidden;
      animation: enter .7s ease both;
    }
    .card::before{
      content:""; position:absolute; inset:-2px; pointer-events:none; opacity:.55;
      background: radial-gradient(500px 200px at 15% 0%, rgba(56,189,248,.16), transparent 65%),
                  radial-gradient(600px 240px at 85% 5%, rgba(239,68,68,.18), transparent 60%);
    }
    .card > *{position:relative; z-index:1;}

    @keyframes enter{from{opacity:0; transform:translateY(14px);} to{opacity:1; transform:translateY(0);}}

    .heroBadge{margin-bottom:14px;}
    .heroTitle{
      margin:0; font-size:42px; line-height:1.1; letter-spacing:-.02em; font-weight:700;
    }
    .heroTitle span{display:block;}
    .heroSub{margin-top:14px; font-size:15px; color:var(--muted); line-height:1.8;}

    .ctaRow{margin-top:18px; display:flex; flex-wrap:wrap; gap:10px; align-items:center;}
    .ctaHint{font-size:12px; color:var(--muted-2);}    

    .trustRow{margin-top:16px; display:flex; flex-wrap:wrap; gap:10px;}
    .trustChip{display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; border:1px solid var(--line); background:rgba(255,255,255,.55); font-size:12px; color:var(--muted); font-weight:600;}
    .dot{width:8px;height:8px;border-radius:50%;}

    .how{
      margin-top:22px; display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:12px;
    }
    .step{
      padding:14px; border-radius:var(--radius-sm); border:1px solid var(--line); background:rgba(0,0,0,.03);
      transition:transform .2s ease, border-color .2s ease;
    }
    .step:hover{transform:translateY(-2px); border-color:rgba(127,127,127,.2)}
    .step b{display:block; margin-bottom:6px; font-size:14px;}
    .step p{margin:0; font-size:12px; color:var(--muted); line-height:1.7;}

    .portalHeader{display:flex; align-items:center; justify-content:space-between; gap:10px;}
    .portalHeader h3{margin:0; font-size:18px;}
    .portalGrid{margin-top:16px; display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:12px;}
    .portalCard{
      padding:16px; border-radius:var(--radius-sm); border:1px solid var(--line); background:rgba(255,255,255,.6);
      display:grid; gap:8px; transition:transform .2s ease, border-color .2s ease, box-shadow .2s ease;
    }
    .portalCard:hover{transform:translateY(-3px); border-color:rgba(127,127,127,.25); box-shadow:var(--shadow-strong);}
    .portalTitle{font-size:14px; font-weight:700; display:flex; align-items:center; gap:8px;}
    .portalDesc{font-size:12px; color:var(--muted-2); line-height:1.6;}

    .banner{
      margin-top:16px; padding:18px; border-radius:var(--radius-sm); border:1px solid var(--line);
      background: linear-gradient(135deg, rgba(239,68,68,.08), rgba(56,189,248,.10));
      display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    }
    .storeBtns{display:flex; gap:10px; flex-wrap:wrap;}
    .storeBtn{
      display:inline-flex; align-items:center; justify-content:center; padding:10px 14px; border-radius:14px;
      border:1px solid var(--line); background:rgba(0,0,0,.04); font-weight:700; font-size:13px;
    }

    .footer{margin-top:18px; display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; color:var(--muted-2); font-size:12px;}

    .segmented{
      display:inline-flex; align-items:center; gap:6px; padding:6px; border-radius:999px; border:1px solid var(--line);
      background:rgba(255,255,255,.6);
    }
    .segmented .seg{padding:6px 12px; border-radius:999px; font-size:12px; color:var(--muted);}
    .segmented .seg.active{background:var(--fg); color:var(--bg);}    

    @media (max-width: 1050px){
      .main{grid-template-columns:1fr;}
      .portalGrid{grid-template-columns:1fr;}
      .how{grid-template-columns:1fr;}
      .heroTitle{font-size:36px;}
    }

    @media (max-width: 640px){
      .header{flex-direction:column; align-items:flex-start;}
      .heroTitle{font-size:30px;}
      .banner{flex-direction:column; align-items:flex-start;}
    }

    @media (prefers-reduced-motion: reduce){
      *{animation: none !important; transition: none !important;}
    }
  </style>
</head>

<body class="{{ session('theme','light') === 'dark' ? 'dark' : '' }}">
  <div class="page" id="page">
    <div class="noise" aria-hidden="true"></div>

    <div class="container">
      <header class="header">
        <div class="brand">
          <div class="logo" aria-hidden="true">
            <img src="{{ asset('assets/images/Vector.png') }}" alt="Rateit Logo" style="width:24px;height:24px;object-fit:contain;">
          </div>
          <div class="brandText">
            <div class="kicker">{{ __('landing.kicker') }}</div>
            <div class="name">{{ config('app.name','Rate It') }}</div>
          </div>
        </div>

        <div class="headerActions">
          <div class="segmented" role="tablist" aria-label="{{ __('landing.persona_tabs') }}">
            <span class="seg active">{{ __('landing.tab_user') }}</span>
            <span class="seg">{{ __('landing.tab_gate') }}</span>
            <span class="seg">{{ __('landing.tab_admin') }}</span>
          </div>
          <a class="btn btnGhost" href="{{ request()->fullUrlWithQuery(['lang' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}">
            {{ app()->getLocale() === 'ar' ? 'EN' : 'AR' }}
          </a>
          <button class="btn" id="themeToggle" type="button">{{ __('landing.theme_toggle') }}</button>
        </div>
      </header>

      <main class="main">
        <section class="card">
          <div class="chip heroBadge"><span class="dot" style="background:var(--success)"></span>{{ __('landing.badge') }}</div>
          <h1 class="heroTitle">
            <span>{{ __('landing.hero_title_line1') }}</span>
            <span>{{ __('landing.hero_title_line2') }}</span>
            <span style="color:var(--muted)">{{ __('landing.hero_title_line3') }}</span>
          </h1>
          <p class="heroSub">{{ __('landing.hero_subtitle') }}</p>

          <div class="ctaRow">
            <a class="btn btnPrimary" href="{{ url('/app') }}">{{ __('landing.cta_user') }}</a>
            <a class="btn" href="{{ url('/vendor/login') }}">{{ __('landing.cta_vendor') }}</a>
            <a class="btn" href="{{ url('/admin/login') }}">{{ __('landing.cta_admin') }}</a>
            <span class="ctaHint">{{ __('landing.cta_hint') }}</span>
          </div>

          <div class="trustRow">
            <span class="trustChip"><span class="dot" style="background:var(--success)"></span>{{ __('landing.trust_qr') }}</span>
            <span class="trustChip"><span class="dot" style="background:var(--amber)"></span>{{ __('landing.trust_points') }}</span>
            <span class="trustChip"><span class="dot" style="background:var(--sky)"></span>{{ __('landing.trust_secure') }}</span>
            <span class="trustChip"><span class="dot" style="background:var(--red)"></span>{{ __('landing.trust_moderation') }}</span>
          </div>

          <div class="how">
            <div class="step">
              <b>{{ __('landing.step1_title') }}</b>
              <p>{{ __('landing.step1_desc') }}</p>
            </div>
            <div class="step">
              <b>{{ __('landing.step2_title') }}</b>
              <p>{{ __('landing.step2_desc') }}</p>
            </div>
            <div class="step">
              <b>{{ __('landing.step3_title') }}</b>
              <p>{{ __('landing.step3_desc') }}</p>
            </div>
          </div>
        </section>

        <aside class="card">
          <div class="portalHeader">
            <h3>{{ __('landing.portals_title') }}</h3>
            <span class="chip"><span class="dot" style="background:var(--success)"></span>{{ __('landing.portals_live') }}</span>
          </div>

          <div class="portalGrid">
            <div class="portalCard">
              <div class="portalTitle"><span class="dot" style="background:var(--amber)"></span>{{ __('landing.portal_admin_title') }}</div>
              <div class="portalDesc">{{ __('landing.portal_admin_desc') }}</div>
              <a class="btn btnPrimary" href="{{ url('/admin/login') }}">{{ __('landing.portal_admin_cta') }}</a>
            </div>
            <div class="portalCard">
              <div class="portalTitle"><span class="dot" style="background:var(--success)"></span>{{ __('landing.portal_vendor_title') }}</div>
              <div class="portalDesc">{{ __('landing.portal_vendor_desc') }}</div>
              <a class="btn btnPrimary" href="{{ url('/vendor/login') }}">{{ __('landing.portal_vendor_cta') }}</a>
            </div>
            <div class="portalCard">
              <div class="portalTitle"><span class="dot" style="background:var(--sky)"></span>{{ __('landing.portal_branch_title') }}</div>
              <div class="portalDesc">{{ __('landing.portal_branch_desc') }}</div>
              <a class="btn btnPrimary" href="{{ url('/vendor/verify') }}">{{ __('landing.portal_branch_cta') }}</a>
            </div>
          </div>

          <div class="banner">
            <div>
              <div style="font-weight:700; margin-bottom:4px">{{ __('landing.app_title') }}</div>
              <div style="font-size:12px; color:var(--muted)">{{ __('landing.app_desc') }}</div>
            </div>
            <div class="storeBtns">
              <a class="storeBtn" href="{{ url('/app/download?platform=android') }}">{{ __('landing.app_google') }}</a>
              <a class="storeBtn" href="{{ url('/app/download?platform=ios') }}">{{ __('landing.app_apple') }}</a>
            </div>
          </div>
        </aside>
      </main>

      <footer class="footer">
        <div>{{ __('landing.footer_left') }}</div>
        <div>&copy; {{ date('Y') }} EVYX</div>
      </footer>
    </div>
  </div>

<script>
  (function(){
    const body = document.body;
    const btn = document.getElementById('themeToggle');
    if (!btn) return;
    const stored = localStorage.getItem('theme');
    if (stored === 'dark') body.classList.add('dark');
    btn.addEventListener('click', function(){
      body.classList.toggle('dark');
      localStorage.setItem('theme', body.classList.contains('dark') ? 'dark' : 'light');
    });
  })();
</script>
</body>
</html>
