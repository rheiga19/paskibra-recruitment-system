<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'Paskibra Compreng') }}</title>

<!-- General CSS Files -->
<link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">

<!-- Template CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">

<style>
/* ══ DARK MODE (default) ══ */
:root {
  --merah: #CC0000;
  --merah-hover: #a30000;
  --bg: #080808;
  --card-bg: #1A1A1A;
  --card-border: rgba(255,255,255,.07);
  --teks-utama: #FFFFFF;
  --teks-redup: #9CA3AF;
  --input-bg: #111111;
  --input-border: rgba(255,255,255,.12);
  --input-color: #FFFFFF;
  --input-placeholder: #6B7280;
  --link-color: #FF6B6B;
  --shadow: rgba(0,0,0,.5);
}

/* ══ LIGHT MODE ══ */
[data-theme="light"] {
  --bg: #F3F4F6;
  --card-bg: #FFFFFF;
  --card-border: #E5E7EB;
  --teks-utama: #111111;
  --teks-redup: #6B7280;
  --input-bg: #F9FAFB;
  --input-border: #D1D5DB;
  --input-color: #111111;
  --input-placeholder: #9CA3AF;
  --link-color: #CC0000;
  --shadow: rgba(0,0,0,.08);
}

/* ── BASE ── */
*, *::before, *::after { box-sizing: border-box; }

body {
  background: var(--bg) !important;
  color: var(--teks-utama) !important;
  transition: background .3s, color .3s;
  min-height: 100vh;
}

/* ── SECTION BACKGROUND ── */
.section {
  background: var(--bg) !important;
}

/* ── LOGIN BRAND ── */
.login-brand {
  text-align: center;
  margin-bottom: 25px;
}

.login-logo-wrapper {
  width: 90px;
  height: 90px;
  margin: 0 auto 12px auto;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: linear-gradient(135deg, #cc0000, #800000);
  box-shadow: 0 12px 30px rgba(204,0,0,.35);
}

.login-logo-wrapper img {
  width: 100px;
  height: 100px;
  object-fit: contain;
}

.login-title {
  font-size: 22px;
  font-weight: 700;
  letter-spacing: 2px;
  margin-bottom: 2px;
  color: var(--teks-utama);
}

.login-sub {
  font-size: 13px;
  color: var(--teks-redup);
}

/* ── CARD ── */
.card {
  background: var(--card-bg) !important;
  border: 1px solid var(--card-border) !important;
  border-radius: 14px !important;
  box-shadow: 0 10px 40px var(--shadow) !important;
  transition: background .3s, border-color .3s;
}

.card-body {
  padding: 30px !important;
}

/* ── FORM LABELS ── */
.form-group label,
label {
  color: var(--teks-utama) !important;
  font-size: 13px;
  font-weight: 600;
}

/* ── INPUTS ── */
.form-control {
  background: var(--input-bg) !important;
  border: 1px solid var(--input-border) !important;
  color: var(--input-color) !important;
  border-radius: 8px !important;
  transition: border-color .2s, background .3s;
}

.form-control::placeholder {
  color: var(--input-placeholder) !important;
}

.form-control:focus {
  border-color: var(--merah) !important;
  box-shadow: 0 0 0 3px rgba(204,0,0,.15) !important;
  background: var(--input-bg) !important;
}

/* ── INPUT GROUP ── */
.input-group-text {
  background: var(--input-bg) !important;
  border: 1px solid var(--input-border) !important;
  color: var(--teks-redup) !important;
  border-radius: 8px 0 0 8px !important;
}

/* ── CHECKBOX ── */
.custom-control-label {
  color: var(--teks-redup) !important;
  font-size: 13px;
}

/* ── BUTTON ── */
.btn-primary {
  background: var(--merah) !important;
  border-color: var(--merah) !important;
  border-radius: 8px !important;
  font-weight: 600;
  letter-spacing: .5px;
  transition: all .2s !important;
}

.btn-primary:hover {
  background: var(--merah-hover) !important;
  border-color: var(--merah-hover) !important;
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(204,0,0,.35) !important;
}

/* ── LINKS ── */
a {
  color: var(--link-color) !important;
  transition: color .2s;
}

a:hover {
  color: var(--merah-hover) !important;
  text-decoration: none !important;
}

/* kembali ke beranda khusus */
.link-back {
  color: var(--teks-redup) !important;
  font-size: 13px;
}

.link-back:hover {
  color: var(--teks-utama) !important;
}

/* ── ALERT ── */
.alert {
  border-radius: 10px !important;
  font-size: 13px;
  border: none !important;
}

.alert-success {
  background: rgba(16,185,129,.15) !important;
  color: #6EE7B7 !important;
}

.alert-danger {
  background: rgba(204,0,0,.15) !important;
  color: #FCA5A5 !important;
}

[data-theme="light"] .alert-success {
  background: #ECFDF5 !important;
  color: #065F46 !important;
}

[data-theme="light"] .alert-danger {
  background: #FEF2F2 !important;
  color: #991B1B !important;
}

/* ── THEME TOGGLE ── */
.theme-toggle {
  position: fixed;
  bottom: 28px;
  right: 28px;
  z-index: 999;
  width: 46px;
  height: 46px;
  border-radius: 50%;
  background: var(--merah);
  color: white;
  border: none;
  font-size: 18px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 20px rgba(204,0,0,.4);
  transition: all .3s;
}

.theme-toggle:hover {
  transform: scale(1.1) rotate(15deg);
  box-shadow: 0 6px 28px rgba(204,0,0,.6);
}
</style>

{{-- Terapkan tema SEBELUM render (hindari flash) --}}
<script>
  (function () {
    const stored = localStorage.getItem('theme');
    const preferLight = window.matchMedia('(prefers-color-scheme: light)').matches;
    const theme = stored || (preferLight ? 'light' : 'dark');
    if (theme === 'light') document.documentElement.setAttribute('data-theme', 'light');
  })();
</script>

@stack('css')
</head>

<body>

<div id="app">
<section class="section">
<div class="container mt-5">
<div class="row">

<div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">

  {{-- LOGO --}}
  <div class="login-brand">
    <div class="login-logo-wrapper">
      <img src="{{ asset('images/logo.png') }}" alt="Logo Paskibra">
    </div>
    <div class="login-title">PASKIBRA</div>
    <div class="login-sub">Kecamatan Compreng</div>
  </div>

  {{-- Flash Message --}}
  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
  </div>
  @endif

  @if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
  </div>
  @endif

  {{-- LOGIN CARD --}}
  <div class="card card-primary">
    <div class="card-body">
      {{ $slot }}
    </div>
  </div>

  {{-- BACK HOME --}}
  <div class="text-center mt-3 mb-5">
    <a href="{{ route('home') }}" class="link-back">
      <i class="fas fa-arrow-left mr-1"></i> Kembali ke Beranda
    </a>
  </div>

</div>
</div>
</div>
</section>
</div>

{{-- THEME TOGGLE --}}
<button class="theme-toggle" id="themeToggle" title="Ganti tema" onclick="toggleTheme()">
  <span id="themeIcon">☀️</span>
</button>

<!-- JS -->
<script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
<script src="{{ asset('assets/modules/popper.js') }}"></script>
<script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/stisla.js') }}"></script>
<script src="{{ asset('assets/js/scripts.js') }}"></script>

<script>
  const html = document.documentElement;
  const icon = document.getElementById('themeIcon');

  function applyTheme(theme) {
    if (theme === 'light') {
      html.setAttribute('data-theme', 'light');
      icon.textContent = '🌙';
    } else {
      html.removeAttribute('data-theme');
      icon.textContent = '☀️';
    }
  }

  function toggleTheme() {
    const current = html.getAttribute('data-theme');
    const next = current === 'light' ? 'dark' : 'light';
    localStorage.setItem('theme', next);
    applyTheme(next);
  }

  // Sinkron dengan pilihan yang sudah tersimpan
  const stored = localStorage.getItem('theme');
  if (stored) {
    applyTheme(stored);
  } else if (window.matchMedia('(prefers-color-scheme: light)').matches) {
    applyTheme('light');
  } else {
    applyTheme('dark');
  }

  // Ikuti perubahan device secara realtime (jika belum ada override manual)
  window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', (e) => {
    if (!localStorage.getItem('theme')) {
      applyTheme(e.matches ? 'light' : 'dark');
    }
  });
</script>

@stack('js')

</body>
</html>