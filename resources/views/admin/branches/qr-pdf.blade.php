<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Branch QR</title>
    <style>
      body { font-family: DejaVu Sans, Arial, sans-serif; color: #111; }
      .page { width: 100%; text-align: center; padding: 32px 24px; }
      .logo-wrap { margin-bottom: 18px; }
      .logo { width: 64px; height: 64px; display: inline-block; border-radius: 14px; background: #f6f6f6; }
      .app-title { font-size: 20px; font-weight: 700; margin: 6px 0 2px; }
      .app-sub { font-size: 12px; color: #666; margin-bottom: 24px; }
      .qr { margin: 0 auto 18px; width: 320px; height: 320px; border-radius: 18px; border: 1px solid #eee; }
      .branch-name { font-size: 18px; font-weight: 700; margin-top: 8px; }
      .note { font-size: 11px; color: #888; margin-top: 6px; }
    </style>
  </head>
  <body>
    <div class="page">
      <div class="logo-wrap">
        @if($rateitLogoBase64)
          <img class="logo" src="{{ $rateitLogoBase64 }}" alt="Rateit">
        @endif
        <div class="app-title">Rateit</div>
        <div class="app-sub">Admin Panel</div>
      </div>

      <div>
        <img class="qr" src="{{ $qrBase64 }}" alt="Branch QR">
      </div>

      <div class="branch-name">{{ $branch->display_name ?: ($branch->name ?: 'Branch') }}</div>
      <div class="note">Scan to access branch review flow</div>
    </div>
  </body>
</html>
