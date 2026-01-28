<!doctype html>
<html lang="{{ app()->getLocale() }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'Admin')</title>

  {{-- Tailwind CDN (بدون npm) --}}
  <script src="https://cdn.tailwindcss.com"></script>

  {{-- Optional: Tailwind config inline --}}
  <script>
    tailwind.config = {
      theme: {
        extend: {
          boxShadow: { soft: "0 20px 60px rgba(0,0,0,.08)" }
        }
      }
    }
  </script>
</head>
<body class="bg-gray-100 text-gray-900">
  @yield('content')
</body>
</html>
