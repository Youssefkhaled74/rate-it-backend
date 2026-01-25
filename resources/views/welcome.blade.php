{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="en" dir="ltr" class="h-full scroll-smooth">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="theme-color" content="#0b0b0b" />
  <title>{{ config('app.name', 'Rate It') }}</title>

  <style>
    :root{
      --bg: #fafafa;
      --fg: #0a0a0a;
      --muted: rgba(0,0,0,.65);
      --muted2: rgba(0,0,0,.45);
      --card: rgba(255,255,255,.70);
      --line: rgba(0,0,0,.10);
      --shadow: 0 30px 90px rgba(0,0,0,.12);
      --red1:#b91c1c; --red2:#ef4444; --amber:#f59e0b; --sky:#38bdf8; --green:#34d399;
      --r: 24px;
    }
    .dark{
      --bg: #09090b;
      --fg: #fafafa;
      --muted: rgba(255,255,255,.70);
      --muted2: rgba(255,255,255,.50);
      --card: rgba(255,255,255,.06);
      --line: rgba(255,255,255,.10);
      --shadow: 0 40px 120px rgba(0,0,0,.55);
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
      background:
        radial-gradient(1200px 600px at 18% 10%, rgba(239,68,68,.14), transparent 60%),
        radial-gradient(900px 500px at 85% 15%, rgba(245,158,11,.12), transparent 60%),
        radial-gradient(900px 650px at 50% 120%, rgba(56,189,248,.10), transparent 60%),
        linear-gradient(180deg, var(--bg), var(--bg));
      color: var(--fg);
    }
    a{color:inherit;text-decoration:none}
    .wrap{max-width:1120px;margin:0 auto;padding:22px}
    .top{display:flex;align-items:center;justify-content:space-between;gap:12px}
    .brand{display:flex;align-items:center;gap:12px}
    .logo{
      width:44px;height:44px;border-radius:16px;
      background: linear-gradient(135deg,var(--red1),var(--amber));
      box-shadow: 0 18px 60px rgba(0,0,0,.25);
      display:grid;place-items:center;color:white;
    }
    .kicker{font-size:12px;color:var(--muted2);margin-bottom:2px}
    .name{font-size:16px;font-weight:750;letter-spacing:-.02em}
    .actions{display:flex;gap:10px;align-items:center}
    .btn{
      display:inline-flex;align-items:center;gap:10px;justify-content:center;
      padding:10px 14px;border-radius:16px;border:1px solid var(--line);
      background: rgba(255,255,255,.55);
      color: var(--fg);
      cursor:pointer;
      transition: transform .15s ease, background .15s ease, border-color .15s ease;
      user-select:none;
    }
    .dark .btn{background: rgba(255,255,255,.06)}
    .btn:hover{transform: translateY(-1px)}
    .btnPrimary{
      border-color: transparent;
      background: linear-gradient(135deg,var(--red1),var(--red2));
      color:white;
      box-shadow: 0 18px 50px rgba(185,28,28,.22);
      font-weight: 750;
    }
    .badge{
      display:inline-flex;align-items:center;gap:8px;
      padding:8px 12px;border-radius:999px;
      border:1px solid var(--line);
      background: rgba(255,255,255,.45);
      color: var(--muted);
      font-size:12px;font-weight:650;
    }
    .dark .badge{background: rgba(0,0,0,.18)}
    .dot{width:9px;height:9px;border-radius:99px;background:var(--green);box-shadow:0 0 0 6px rgba(52,211,153,.14)}
    .grid{margin-top:18px;display:grid;grid-template-columns:1.15fr .85fr;gap:16px}
    .card{
      border:1px solid var(--line);
      border-radius: calc(var(--r) + 6px);
      background: var(--card);
      box-shadow: var(--shadow);
      backdrop-filter: blur(14px);
    }
    .pad{padding:22px}
    .heroBadge{margin-bottom:14px}
    .h1{
      margin:0;
      font-size:48px;
      line-height:1.05;
      letter-spacing:-.03em;
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
    .dark .mini{background: rgba(0,0,0,.18)}
    .mini:hover{transform: translateY(-1px);border-color: rgba(127,127,127,.25)}
    .mini b{display:block;margin-bottom:6px}
    .sep{height:1px;background:var(--line);margin:18px 0}
    .pillRow{display:flex;flex-wrap:wrap;gap:10px}
    .pill{
      display:inline-flex;align-items:center;gap:8px;
      padding:8px 12px;border-radius:999px;border:1px solid var(--line);
      background: rgba(255,255,255,.45);
      color: var(--muted);
      font-size:13px;font-weight:650;
    }
    .dark .pill{background: rgba(255,255,255,.05)}
    .pDot{width:8px;height:8px;border-radius:99px}
    .rightTitle{display:flex;justify-content:space-between;gap:10px;align-items:flex-start}
    .small{font-size:12px;color:var(--muted2)}
    .title{font-size:18px;font-weight:750}
    .list{margin:12px 0 0;padding:0;list-style:none;display:grid;gap:10px;color:var(--muted)}
    .li{display:flex;gap:10px}
    .li span{margin-top:7px;width:9px;height:9px;border-radius:99px;flex:0 0 auto}
    .twoBtn{margin-top:14px;display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .footer{
      margin-top:18px;padding-top:16px;border-top:1px solid var(--line);
      display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;
      color:var(--muted2);font-size:13px
    }
    .rtl{direction:rtl}
    @media (max-width: 980px){
      .grid{grid-template-columns:1fr}
      .h1{font-size:40px}
      .miniGrid{grid-template-columns:1fr}
      .twoBtn{grid-template-columns:1fr}
    }
  </style>
</head>

<body>
  <div id="page" class="">
    <div class="wrap">
      <header class="top">
        <div class="brand">
          <div class="logo" aria-hidden="true">★</div>
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
          <div class="badge heroBadge"><span class="dot"></span><span data-i18n="taglineBadge">Real reviews • Rewards • Trust</span></div>

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
            <span class="pill"><span class="pDot" style="background:var(--amber)"></span><span data-i18n="pill2">Points → Vouchers</span></span>
            <span class="pill"><span class="pDot" style="background:var(--sky)"></span><span data-i18n="pill3">Vendor verification</span></span>
          </div>
        </section>

        <aside class="card pad">
          <div class="rightTitle">
            <div>
              <div class="small" data-i18n="rightKicker">Choose your portal</div>
              <div class="title" data-i18n="rightTitle">User / Vendor</div>
            </div>
            <span class="badge"><span class="dot"></span><span data-i18n="status">Live</span></span>
          </div>

          <div class="sep"></div>

          <div class="title" style="font-size:16px" data-i18n="userCardTitle">For Users</div>
          <ul class="list">
            <li class="li"><span style="background:var(--green)"></span><div data-i18n="userBullet1">Browse places and reviews</div></li>
            <li class="li"><span style="background:var(--amber)"></span><div data-i18n="userBullet2">Rate after scanning QR</div></li>
            <li class="li"><span style="background:var(--sky)"></span><div data-i18n="userBullet3">Earn points & redeem vouchers</div></li>
          </ul>
          <div class="twoBtn">
            <a class="btn btnPrimary" href="{{ url('/app/download') }}" data-i18n="downloadApp">Download App</a>
            <a class="btn" href="{{ url('/app') }}" data-i18n="explorePlaces">Explore Places</a>
          </div>

          <div class="sep"></div>

          <div class="title" style="font-size:16px" data-i18n="vendorCardTitle">For Vendors</div>
          <ul class="list">
            <li class="li"><span style="background:var(--green)"></span><div data-i18n="vendorBullet1">See reviews and insights for your branches</div></li>
            <li class="li"><span style="background:var(--amber)"></span><div data-i18n="vendorBullet2">Control review frequency per branch</div></li>
            <li class="li"><span style="background:var(--sky)"></span><div data-i18n="vendorBullet3">Verify & redeem vouchers (branch staff)</div></li>
          </ul>
          <div class="twoBtn">
            <a class="btn btnPrimary" href="{{ url('/vendor/login') }}" data-i18n="vendorLogin">Vendor Login</a>
            <a class="btn" href="{{ url('/vendor/verify') }}" data-i18n="verifyVoucher">Verify Voucher</a>
          </div>
        </aside>
      </main>

      <footer class="footer">
        <div data-i18n="footerLeft">Trusted reviews. Real rewards.</div>
        <div>© {{ date('Y') }} {{ config('app.name','Rate It') }}</div>
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
      rightTitle:"User / Vendor",
      status:"Live",
      userCardTitle:"For Users",
      userBullet1:"Browse places and reviews",
      userBullet2:"Rate after scanning QR",
      userBullet3:"Earn points & redeem vouchers",
      downloadApp:"Download App",
      explorePlaces:"Explore Places",
      vendorCardTitle:"For Vendors",
      vendorBullet1:"See reviews and insights for your branches",
      vendorBullet2:"Control review frequency per branch",
      vendorBullet3:"Verify & redeem vouchers (branch staff)",
      vendorLogin:"Vendor Login",
      verifyVoucher:"Verify Voucher",
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
      rightTitle:"مستخدم / بائع",
      status:"نشط",
      userCardTitle:"للمستخدمين",
      userBullet1:"تصفح الأماكن والتقييمات",
      userBullet2:"قيّم بعد مسح QR",
      userBullet3:"اكسب نقاط واستبدلها بقسائم",
      downloadApp:"تحميل التطبيق",
      explorePlaces:"استكشف الأماكن",
      vendorCardTitle:"للبائعين",
      vendorBullet1:"عرض التقييمات والإحصاءات لفروعك",
      vendorBullet2:"التحكم في تكرار التقييم لكل فرع",
      vendorBullet3:"التحقق من القسائم واستبدالها (موظف الفرع)",
      vendorLogin:"تسجيل دخول البائع",
      verifyVoucher:"التحقق من القسيمة",
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
