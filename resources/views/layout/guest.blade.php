@php
  $variant = $variant ?? 'login';
@endphp
<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'تسجيل الدخول') | {{ config('app.name') }}</title>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
      media="print"
      onload="this.media = 'all'"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.css') }}" />
  </head>
  <body class="{{ $variant === 'register' ? 'register-page' : 'login-page' }} bg-body-secondary">
    @if ($variant === 'register')
      <div class="register-box">
        <div class="register-logo">
          <a href="{{ url('/') }}"><b>{{ config('app.name') }}</b></a>
        </div>
        <div class="card">
          <div class="card-body register-card-body">@yield('content')</div>
        </div>
      </div>
    @else
      <div class="login-box">
        <div class="login-logo">
          <a href="{{ url('/') }}"><b>{{ config('app.name') }}</b></a>
        </div>
        <div class="card">
          <div class="card-body login-card-body">@yield('content')</div>
        </div>
      </div>
    @endif

    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('dist/js/adminlte.js') }}"></script>
  </body>
</html>
