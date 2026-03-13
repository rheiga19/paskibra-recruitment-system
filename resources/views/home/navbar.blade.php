{{-- resources/views/components/home-navbar.blade.php --}}
<nav id="navbar">
  <a href="{{ route('home') }}" class="nav-logo footer-logo">
    <div class="nav-logo-emblem">
    <img src="{{ asset('images/logo.png') }}" alt="Logo Paskibra">
  </div>
    <div>
      <div class="nav-logo-text">PASKIBRA</div>
      <div class="nav-logo-sub">Kec. Compreng</div>
    </div>
  </a>

  <ul class="nav-links">
    <li><a href="{{ url('/') }}#tentang">Tentang</a></li>
    <li><a href="{{ url('/') }}#pendaftaran">Pendaftaran</a></li>
    <li><a href="{{ url('/') }}#syarat">Syarat</a></li>
    <li><a href="{{ url('/') }}#berita">Berita</a></li>
    <li><a href="{{ url('/') }}#galeri">Galeri</a></li>
    <li><a href="{{ route('pengumuman') }}">Pengumuman</a></li>
    <li><a href="{{ url('/') }}#faq">FAQ</a></li>
  </ul>

  <div class="nav-actions">
    @auth
      <a href="{{ route('dashboard') }}" class="btn-primary-red">Dashboard</a>
    @else
      <a href="{{ route('login') }}" class="btn-outline">Masuk</a>
      <a href="{{ route('register') }}" class="btn-primary-red">Daftar Sekarang</a>
    @endauth
  </div>

  <button class="nav-hamburger" id="hamburger" onclick="toggleMenu()">
    <span></span><span></span><span></span>
  </button>
</nav>

{{-- Mobile Menu --}}
<div class="nav-mobile" id="navMobile">
  <ul>
    <li><a href="{{ url('/') }}#tentang" onclick="toggleMenu()">Tentang</a></li>
    <li><a href="{{ url('/') }}#pendaftaran" onclick="toggleMenu()">Pendaftaran</a></li>
    <li><a href="{{ url('/') }}#syarat" onclick="toggleMenu()">Syarat</a></li>
    <li><a href="{{ url('/') }}#berita" onclick="toggleMenu()">Berita</a></li>
    <li><a href="{{ url('/') }}#galeri" onclick="toggleMenu()">Galeri</a></li>
    <li><a href="{{ route('pengumuman') }}" onclick="toggleMenu()">Pengumuman</a></li>
    <li><a href="{{ url('/') }}#faq" onclick="toggleMenu()">FAQ</a></li>
  </ul>
  <div class="nav-mobile-actions">
    @auth
      <a href="{{ route('dashboard') }}" class="btn-primary-red">Dashboard</a>
    @else
      <a href="{{ route('login') }}" class="btn-outline">Masuk</a>
      <a href="{{ route('register') }}" class="btn-primary-red">Daftar Sekarang</a>
    @endauth
  </div>
</div>