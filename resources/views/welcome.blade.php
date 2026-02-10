{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="theme-color" content="#0b0b0b" />
  <title>{{ config('app.name', 'Rate It') }}</title>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,500;700&family=Space+Grotesk:wght@400;500;600;700&display=swap');
    :root{
      --bg: #fafafa;
      --fg: #0a0a0a;
      --muted: rgba(0,0,0,.66);
      --muted2: rgba(0,0,0,.46);

      --card: rgba(255,255,255,.72);
      --line: rgba(0,0,0,.10);
      --shadow: 0 30px 90px rgba(0,0,0,.10);
      --shadowStrong: 0 30px 120px rgba(0,0,0,.18);

      --red1:#b91c1c; --red2:#ef4444; --amber:#f59e0b; --sky:#38bdf8; --green:#34d399;
      --r: 24px;
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family: "Space Grotesk", "Segoe UI", Tahoma, sans-serif;
      color: var(--fg);
      background:
        radial-gradient(1200px 600px at 18% 10%, rgba(239,68,68,.12), transparent 60%),
        radial-gradient(900px 500px at 85% 15%, rgba(245,158,11,.10), transparent 60%),
        radial-gradient(900px 650px at 50% 120%, rgba(56,189,248,.08), transparent 60%),
        linear-gradient(180deg, var(--bg), var(--bg));
    }

    a{color:inherit;text-decoration:none}
    .wrap{max-width:1180px;margin:0 auto;padding:26px}

    /* ===== Ambient background polish ===== */
    #page{position:relative; overflow:hidden;}
    .ambient{
      position:absolute;
      inset:-120px;
      pointer-events:none;
      z-index:0;
    }
    .blob{
      position:absolute;
      width: 520px;
      height: 520px;
      border-radius: 999px;
      filter: blur(38px);
      opacity: .55;
      transform: translate3d(0,0,0);
      animation: floaty 14s ease-in-out infinite;
    }
    .b1{ left: 6%; top: 6%; background: radial-gradient(circle at 30% 30%, rgba(239,68,68,.55), transparent 60%); }
    .b2{ right: 2%; top: 14%; width: 460px; height:460px; background: radial-gradient(circle at 35% 35%, rgba(245,158,11,.45), transparent 62%); animation-duration: 18s; }
    .b3{ left: 28%; bottom: -2%; width: 560px; height:560px; background: radial-gradient(circle at 35% 35%, rgba(56,189,248,.35), transparent 65%); animation-duration: 22s; }

    @keyframes floaty{
      0%   { transform: translate(0px,0px) scale(1); }
      50%  { transform: translate(30px,-18px) scale(1.06); }
      100% { transform: translate(0px,0px) scale(1); }
    }

    .noise{
      position:absolute; inset:0;
      opacity: .06;
      mix-blend-mode: overlay;
      background-image:
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='220' height='220'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='220' height='220' filter='url(%23n)' opacity='.55'/%3E%3C/svg%3E");
      background-size: 220px 220px;
    }

    .gridlines{
      position:absolute; inset:0;
      opacity: .08;
      background:
        linear-gradient(to right, rgba(0,0,0,.12) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(0,0,0,.12) 1px, transparent 1px);
      background-size: 72px 72px;
      mask-image: radial-gradient(circle at 50% 20%, black 20%, transparent 70%);
    }

    .particles{
      position:absolute;
      inset:0;
      pointer-events:none;
      z-index:0;
    }
    .particles span{
      position:absolute;
      width: 6px; height: 6px;
      border-radius: 99px;
      background: rgba(0,0,0,.12);
      opacity: .25;
      animation: drift linear infinite;
    }
    .particles span:nth-child(3n){ width:4px; height:4px; opacity:.22; }
    .particles span:nth-child(4n){ width:8px; height:8px; opacity:.18; filter: blur(1px); }

    @keyframes drift{
      from { transform: translate3d(0, 40px, 0); }
      to   { transform: translate3d(0,-60px, 0); }
    }

    /* position particles (manual, deterministic) */
    .particles span:nth-child(1){ left:10%; top:70%; animation-duration: 10s; }
    .particles span:nth-child(2){ left:18%; top:30%; animation-duration: 12s; }
    .particles span:nth-child(3){ left:26%; top:55%; animation-duration: 14s; }
    .particles span:nth-child(4){ left:34%; top:25%; animation-duration: 11s; }
    .particles span:nth-child(5){ left:42%; top:78%; animation-duration: 15s; }
    .particles span:nth-child(6){ left:50%; top:40%; animation-duration: 13s; }
    .particles span:nth-child(7){ left:58%; top:62%; animation-duration: 16s; }
    .particles span:nth-child(8){ left:66%; top:28%; animation-duration: 12s; }
    .particles span:nth-child(9){ left:74%; top:74%; animation-duration: 14s; }
    .particles span:nth-child(10){ left:82%; top:46%; animation-duration: 17s; }
    .particles span:nth-child(11){ left:90%; top:68%; animation-duration: 12s; }
    .particles span:nth-child(12){ left:14%; top:84%; animation-duration: 18s; }
    .particles span:nth-child(13){ left:38%; top:88%; animation-duration: 16s; }
    .particles span:nth-child(14){ left:62%; top:90%; animation-duration: 19s; }
    .particles span:nth-child(15){ left:86%; top:86%; animation-duration: 15s; }

    /* ensure content above background */
    .wrap{ position:relative; z-index:1; }

    /* extra micro-polish */
    .card{
      position:relative;
      border:1px solid var(--line);
      border-radius: calc(var(--r) + 6px);
      background: var(--card);
      box-shadow: var(--shadow);
      backdrop-filter: blur(14px);
      overflow:hidden;
    }
    .card::before{
      content:"";
      position:absolute; inset:-2px;
      background: radial-gradient(600px 200px at 20% 0%, rgba(56,189,248,.18), transparent 60%),
                  radial-gradient(600px 200px at 80% 10%, rgba(239,68,68,.14), transparent 60%);
      opacity:.55;
      pointer-events:none;
    }
    .card > *{ position:relative; z-index:1; }

    .top{display:flex;align-items:center;justify-content:space-between;gap:12px}
    .brand{display:flex;align-items:center;gap:12px}
    .logo{
      width:44px;height:44px;border-radius:16px;
      background: linear-gradient(135deg,var(--red1),var(--amber));
      box-shadow: 0 18px 60px rgba(0,0,0,.20);
      display:grid;place-items:center;color:white;
      font-weight:700;
    }
    .kicker{font-size:12px;color:var(--muted2);margin-bottom:2px}
    .name{font-size:16px;font-weight:750;letter-spacing:-.02em}

    .actions{display:flex;gap:10px;align-items:center}
    .btn{
      display:inline-flex;align-items:center;gap:10px;justify-content:center;
      padding:10px 14px;border-radius:16px;border:1px solid var(--line);
      background: rgba(255,255,255,.60);
      color: var(--fg);
      cursor:pointer;
      transition: transform .15s ease, background .15s ease, border-color .15s ease;
      user-select:none;
    }
    .btn:hover{transform: translateY(-1px)}
    .btn:focus{outline: none; box-shadow: 0 0 0 3px rgba(56,189,248,.25);}

    .btnPrimary{
      border-color: transparent;
      background: linear-gradient(135deg,var(--red1),var(--red2));
      color:white;
      box-shadow: 0 18px 50px rgba(185,28,28,.20);
      font-weight: 750;
    }
    .btnPrimary:focus{box-shadow: 0 0 0 3px rgba(239,68,68,.35)}

    .badge{
      display:inline-flex;align-items:center;gap:8px;
      padding:8px 12px;border-radius:999px;
      border:1px solid var(--line);
      background: rgba(255,255,255,.55);
      color: var(--muted);
      font-size:12px;font-weight:650;
    }
    .dot{width:9px;height:9px;border-radius:99px;background:var(--green);box-shadow:0 0 0 6px rgba(52,211,153,.14)}

    .grid{margin-top:20px;display:grid;grid-template-columns:1.18fr .82fr;gap:18px}

    .pad{padding:24px}

    .heroBadge{margin-bottom:14px}
    .h1{
      margin:0;
      font-size:48px;
      line-height:1.05;
      letter-spacing:-.03em;
      font-family: "Newsreader", "Times New Roman", serif;
    }
    .sub{margin:14px 0 0;color:var(--muted);line-height:1.7}

    .cta{margin-top:18px;display:flex;flex-wrap:wrap;gap:10px;align-items:center}
    .hint{font-size:12px;color:var(--muted2)}

    .miniGrid{margin-top:18px;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
    .mini{
      padding:14px;border-radius:18px;border:1px solid var(--line);
      background: rgba(0,0,0,.03);
      transition: transform .15s ease, background .15s ease, border-color .15s ease;
    }
    .mini:hover{transform: translateY(-2px);border-color: rgba(127,127,127,.25)}
    .mini b{display:block;margin-bottom:6px}

    .sep{height:1px;background:var(--line);margin:18px 0}

    .pillRow{display:flex;flex-wrap:wrap;gap:10px}
    .pill{
      display:inline-flex;align-items:center;gap:8px;
      padding:8px 12px;border-radius:999px;border:1px solid var(--line);
      background: rgba(255,255,255,.55);
      color: var(--muted);
      font-size:13px;font-weight:650;
    }
    .pDot{width:8px;height:8px;border-radius:99px}

    .rightTitle{display:flex;justify-content:space-between;gap:10px;align-items:flex-start}
    .small{font-size:12px;color:var(--muted2)}
    .title{font-size:18px;font-weight:750}

    .list{margin:12px 0 0;padding:0;list-style:none;display:grid;gap:10px;color:var(--muted)}
    .li{display:flex;gap:10px}
    .li span{margin-top:7px;width:9px;height:9px;border-radius:99px;flex:0 0 auto}

    .twoBtn{margin-top:14px;display:grid;grid-template-columns:1fr 1fr;gap:10px}

    .portalGrid{
      margin-top:14px;
      display:grid;
      grid-template-columns:repeat(3, minmax(0,1fr));
      gap:10px;
    }
    .portalCard{
      padding:14px;
      border-radius:18px;
      border:1px solid var(--line);
      background: rgba(255,255,255,.60);
      display:grid;
      gap:8px;
      transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease;
    }
    .portalCard:hover{
      transform: translateY(-2px);
      border-color: rgba(127,127,127,.25);
      box-shadow: var(--shadowStrong);
    }
    .portalTitle{
      font-size:15px;
      font-weight:700;
      display:flex;
      align-items:center;
      gap:8px;
    }
    .portalMeta{font-size:12px;color:var(--muted2)}
    .portalCard .btn{width:100%}

    .storeRow{
      margin-top:12px;
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:10px;
    }
    .storeBtn{
      display:flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      padding:12px 14px;
      border-radius:16px;
      border:1px solid var(--line);
      background: rgba(0,0,0,.04);
      font-weight:700;
      letter-spacing:.01em;
    }
    .storeBtn:hover{transform: translateY(-1px)}

    .footer{
      margin-top:18px;padding-top:16px;border-top:1px solid var(--line);
      display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;
      color:var(--muted2);font-size:13px
    }

    /* RTL helper */
    .rtl{direction:rtl}

    @media (max-width: 980px){
      .grid{grid-template-columns:1fr}
      .h1{font-size:40px}
      .miniGrid{grid-template-columns:1fr}
      .twoBtn{grid-template-columns:1fr}
      .portalGrid{grid-template-columns:1fr}
      .storeRow{grid-template-columns:1fr}
    }

    /* Reduce motion accessibility */
    @media (prefers-reduced-motion: reduce){
      .blob, .particles span{ animation: none !important; }
    }

    /* =========================
       DARK MODE FIX (High contrast)
       ========================= */
    #page.dark{
      --bg:#07070a;
      --fg:#f9fafb;
      --muted: rgba(255,255,255,.78);
      --muted2: rgba(255,255,255,.58);

      --card: rgba(10,10,12,.80);
      --line: rgba(255,255,255,.12);
      --shadow: 0 40px 120px rgba(0,0,0,.70);
    }

    #page.dark body{
      background:
        radial-gradient(1200px 600px at 18% 10%, rgba(239,68,68,.18), transparent 62%),
        radial-gradient(900px 500px at 85% 15%, rgba(245,158,11,.14), transparent 62%),
        radial-gradient(900px 650px at 50% 120%, rgba(56,189,248,.10), transparent 62%),
        linear-gradient(180deg, var(--bg), var(--bg));
    }

    #page.dark .btn{
      background: rgba(255,255,255,.08);
      border-color: rgba(255,255,255,.14);
    }
    #page.dark .btn:hover{ background: rgba(255,255,255,.12); }

    #page.dark .mini{ background: rgba(255,255,255,.06); }
    #page.dark .badge{ background: rgba(255,255,255,.08); }
    #page.dark .pill{ background: rgba(255,255,255,.07); }
    #page.dark .portalCard{ background: rgba(255,255,255,.06); }
    #page.dark .storeBtn{ background: rgba(255,255,255,.08); }

    #page.dark .particles span{ background: rgba(255,255,255,.18); }
    #page.dark .gridlines{
      background:
        linear-gradient(to right, rgba(255,255,255,.14) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(255,255,255,.14) 1px, transparent 1px);
      opacity:.07;
    }
  </style>
</head>

<body>
  <div id="page">
    <!-- Ambient background layers -->
    <div class="ambient" aria-hidden="true">
      <span class="blob b1"></span>
      <span class="blob b2"></span>
      <span class="blob b3"></span>
      <span class="noise"></span>
      <span class="gridlines"></span>
    </div>

    <div class="particles" aria-hidden="true">
      <span></span><span></span><span></span><span></span><span></span>
      <span></span><span></span><span></span><span></span><span></span>
      <span></span><span></span><span></span><span></span><span></span>
    </div>

    <div class="wrap">
      <header class="top">
        <div class="brand">
          <div class="logo" aria-hidden="true">&#9733;</div>
          <div>
            <div class="kicker" data-i18n="welcome">Welcome to</div>
            <div class="name">{{ config('app.name','Rate It') }}</div>
          </div>
        </div>

        <div class="actions">
          <button class="btn" id="langBtn"><span class="dot"></span><span id="langLabel">EN</span></button>
          <button class="btn" id="themeBtn"><span id="themeIcon">🌙</span><span data-i18n="theme">Theme</span></button>
        </div>
      </header>

      <main class="grid">
        <section class="card pad">
          <div class="badge heroBadge"><span class="dot"></span><span data-i18n="taglineBadge">Real reviews &bull; Rewards &bull; Trust</span></div>

          <h1 class="h1">
            <span data-i18n="heroTitle1">Rate places.</span>
            <span data-i18n="heroTitle2">Earn rewards.</span>
            <span style="color:var(--muted)" data-i18n="heroTitle3">Build trust.</span>
          </h1>

          <p class="sub" data-i18n="heroDesc">
            Discover places, read trusted reviews, then rate after visiting by scanning a QR code. Earn points and redeem vouchers.
          </p>

          <div class="cta">
            <a class="btn btnPrimary" href="{{ url('/app') }}" data-i18n="ctaUser">Continue as User</a>
            <a class="btn" href="{{ url('/vendor') }}" data-i18n="ctaVendor">Continue as Vendor</a>
            <span class="hint" data-i18n="ctaHint">One platform for users & brands.</span>
          </div>

          <div class="miniGrid">
            <div class="mini">
              <b data-i18n="step1Title">Scan QR</b>
              <div class="hint" data-i18n="step1Desc">After visiting, scan the branch QR to open rating.</div>
            </div>
            <div class="mini">
              <b data-i18n="step2Title">Rate & Review</b>
              <div class="hint" data-i18n="step2Desc">Submit your rating and optional comment/photos.</div>
            </div>
            <div class="mini">
              <b data-i18n="step3Title">Get Rewards</b>
              <div class="hint" data-i18n="step3Desc">Earn points and redeem vouchers from brands.</div>
            </div>
          </div>

          <div class="sep"></div>

          <div class="pillRow">
            <span class="pill"><span class="pDot" style="background:var(--green)"></span><span data-i18n="pill1">Verified visit (QR)</span></span>
            <span class="pill"><span class="pDot" style="background:var(--amber)"></span><span data-i18n="pill2">Points &rarr; Vouchers</span></span>
            <span class="pill"><span class="pDot" style="background:var(--sky)"></span><span data-i18n="pill3">Vendor verification</span></span>
          </div>
        </section>

        <aside class="card pad">
          <div class="rightTitle">
            <div>
              <div class="small" data-i18n="rightKicker">Choose your portal</div>
              <div class="title" data-i18n="rightTitle">Portals & Access</div>
            </div>
            <span class="badge"><span class="dot"></span><span data-i18n="status">Live</span></span>
          </div>

          <div class="sep"></div>

          <div class="title" style="font-size:16px" data-i18n="portalTitle">Admin, Vendor & Branch Staff</div>
          <div class="portalGrid">
            <div class="portalCard">
              <div class="portalTitle"><span class="pDot" style="background:var(--amber)"></span><span data-i18n="adminTitle">Admin Portal</span></div>
              <div class="portalMeta" data-i18n="adminDesc">Manage brands, approvals, and platform analytics.</div>
              <a class="btn btnPrimary" href="{{ url('/admin/login') }}" data-i18n="adminLogin">Admin Login</a>
            </div>
            <div class="portalCard">
              <div class="portalTitle"><span class="pDot" style="background:var(--green)"></span><span data-i18n="vendorTitle">Vendor Portal</span></div>
              <div class="portalMeta" data-i18n="vendorDesc">Manage branches, reviews, and vouchers.</div>
              <a class="btn btnPrimary" href="{{ url('/vendor/login') }}" data-i18n="vendorLogin">Vendor Login</a>
            </div>
            <div class="portalCard">
              <div class="portalTitle"><span class="pDot" style="background:var(--sky)"></span><span data-i18n="staffTitle">Branch Staff</span></div>
              <div class="portalMeta" data-i18n="staffDesc">Verify visits and redeem vouchers in-branch.</div>
              <a class="btn btnPrimary" href="{{ url('/vendor/verify') }}" data-i18n="staffVerify">Verify Voucher</a>
            </div>
          </div>

          <div class="sep"></div>

          <div class="title" style="font-size:16px" data-i18n="appTitle">Get the App</div>
          <ul class="list">
            <li class="li"><span style="background:var(--green)"></span><div data-i18n="appBullet1">Download the app to rate instantly after visiting.</div></li>
            <li class="li"><span style="background:var(--amber)"></span><div data-i18n="appBullet2">Available on Google Play and the App Store.</div></li>
          </ul>
          <div class="storeRow">
            <a class="storeBtn" href="{{ url('/app/download?platform=android') }}" data-i18n="playStore">Google Play</a>
            <a class="storeBtn" href="{{ url('/app/download?platform=ios') }}" data-i18n="appStore">App Store</a>
          </div>
        </aside>
      </main>

      <footer class="footer">
        <div data-i18n="footerLeft">Trusted reviews. Real rewards.</div>
        <div>&copy; {{ date('Y') }} {{ config('app.name','Rate It') }}</div>
      </footer>
    </div>
  </div>

<script>
(() => {
  const dict = {
    en: {
      welcome:"Welcome to",
      theme:"Theme",
      taglineBadge:"Real reviews • Rewards • Trust",
      heroTitle1:"Rate places.",
      heroTitle2:"Earn rewards.",
      heroTitle3:"Build trust.",
      heroDesc:"Discover places, read trusted reviews, then rate after visiting by scanning a QR code. Earn points and redeem vouchers.",
      ctaUser:"Continue as User",
      ctaVendor:"Continue as Vendor",
      ctaHint:"One platform for users & brands.",
      step1Title:"Scan QR",
      step1Desc:"After visiting, scan the branch QR to open rating.",
      step2Title:"Rate & Review",
      step2Desc:"Submit your rating and optional comment/photos.",
      step3Title:"Get Rewards",
      step3Desc:"Earn points and redeem vouchers from brands.",
      pill1:"Verified visit (QR)",
      pill2:"Points → Vouchers",
      pill3:"Vendor verification",
      rightKicker:"Choose your portal",
      rightTitle:"Portals & Access",
      status:"Live",
      portalTitle:"Admin, Vendor & Branch Staff",
      adminTitle:"Admin Portal",
      adminDesc:"Manage brands, approvals, and platform analytics.",
      adminLogin:"Admin Login",
      vendorTitle:"Vendor Portal",
      vendorDesc:"Manage branches, reviews, and vouchers.",
      vendorLogin:"Vendor Login",
      staffTitle:"Branch Staff",
      staffDesc:"Verify visits and redeem vouchers in-branch.",
      staffVerify:"Verify Voucher",
      appTitle:"Get the App",
      appBullet1:"Download the app to rate instantly after visiting.",
      appBullet2:"Available on Google Play and the App Store.",
      playStore:"Google Play",
      appStore:"App Store",
      footerLeft:"Trusted reviews. Real rewards."
    },
    ar: {
      welcome:"مرحباً بك في",
      theme:"المظهر",
      taglineBadge:"تقييمات حقيقية • مكافآت • ثقة",
      heroTitle1:"قيّم الأماكن.",
      heroTitle2:"اكسب مكافآت.",
      heroTitle3:"وابنِ الثقة.",
      heroDesc:"اكتشف الأماكن واقرأ تقييمات موثوقة، ثم قيّم بعد الزيارة عبر مسح QR. اكسب نقاط واستبدلها بقسائم.",
      ctaUser:"الدخول كمستخدم",
      ctaVendor:"الدخول كبائع",
      ctaHint:"منصة واحدة للمستخدمين والبراندات.",
      step1Title:"امسح QR",
      step1Desc:"بعد الزيارة امسح QR الخاص بالفرع لفتح شاشة التقييم.",
      step2Title:"قيّم واكتب رأيك",
      step2Desc:"أرسل تقييمك مع تعليق/صور اختيارياً.",
      step3Title:"احصل على مكافآت",
      step3Desc:"اكسب نقاط واستبدلها بقسائم من البراندات.",
      pill1:"زيارة مؤكدة (QR)",
      pill2:"نقاط ← قسائم",
      pill3:"تحقق البائع",
      rightKicker:"اختر البوابة",
      rightTitle:"الوصول والبوابات",
      status:"نشط",
      portalTitle:"الإدارة والبائع وموظفو الفروع",
      adminTitle:"بوابة الإدارة",
      adminDesc:"إدارة العلامات، الموافقات، وتحليلات المنصة.",
      adminLogin:"تسجيل دخول الإدارة",
      vendorTitle:"بوابة البائع",
      vendorDesc:"إدارة الفروع، التقييمات، والقسائم.",
      vendorLogin:"تسجيل دخول البائع",
      staffTitle:"موظفو الفرع",
      staffDesc:"التحقق من الزيارة واستبدال القسائم داخل الفرع.",
      staffVerify:"التحقق من القسيمة",
      appTitle:"احصل على التطبيق",
      appBullet1:"حمّل التطبيق للتقييم مباشرة بعد الزيارة.",
      appBullet2:"متاح على Google Play و App Store.",
      playStore:"Google Play",
      appStore:"App Store",
      footerLeft:"تقييمات موثوقة. مكافآت حقيقية."
    }
  };

  const page = document.getElementById("page");
  const langBtn = document.getElementById("langBtn");
  const langLabel = document.getElementById("langLabel");
  const themeBtn = document.getElementById("themeBtn");
  const themeIcon = document.getElementById("themeIcon");

  const applyTheme = (mode) => {
    page.classList.toggle("dark", mode === "dark");
    themeIcon.textContent = mode === "dark" ? "🌙" : "☀️";
    localStorage.setItem("theme", mode);
  };

  const applyLang = (lang) => {
    const pack = dict[lang] || dict.en;
    document.documentElement.lang = lang;
    document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
    document.body.classList.toggle("rtl", lang === "ar");
    langLabel.textContent = lang.toUpperCase();
    document.querySelectorAll("[data-i18n]").forEach(el => {
      const key = el.getAttribute("data-i18n");
      if (pack[key]) el.textContent = pack[key];
    });
    localStorage.setItem("lang", lang);
  };

  // init
  const storedTheme = localStorage.getItem("theme");
  if (storedTheme) applyTheme(storedTheme);
  else applyTheme(window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light");

  const storedLang = localStorage.getItem("lang");
  applyLang(storedLang || "en");

  themeBtn.addEventListener("click", () => applyTheme(page.classList.contains("dark") ? "light" : "dark"));
  langBtn.addEventListener("click", () => applyLang(document.documentElement.lang === "en" ? "ar" : "en"));
})();
</script>
</body>
</html>
