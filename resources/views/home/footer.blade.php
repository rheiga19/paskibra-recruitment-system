{{-- resources/views/components/home-footer.blade.php --}}
<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <a href="{{ route('home') }}" class="nav-logo">
        <div class="nav-logo-emblem"> <img src="{{ asset('images/logo.png') }}" alt="Logo Paskibra"> </div>
        <div>
          <div class="nav-logo-text">PASKIBRA</div>
          <div class="nav-logo-sub">Kec. Compreng</div>
        </div>
      </a>
      <p>Portal resmi rekrutmen Pasukan Pengibar Bendera Kecamatan Compreng, Kabupaten Subang, Jawa Barat.</p>
      <div class="footer-social">
        <a href="#" class="social-btn">𝕏</a>
        <a href="#" class="social-btn">f</a>
        <a href="#" class="social-btn">in</a>
        <a href="#" class="social-btn">▶</a>
      </div>
    </div>

    <div class="footer-col">
      <h4>Navigasi</h4>
      <ul>
        <li><a href="{{ url('/') }}#tentang">Tentang</a></li>
        <li><a href="{{ url('/') }}#pendaftaran">Pendaftaran</a></li>
        <li><a href="{{ url('/') }}#syarat">Syarat</a></li>
        <li><a href="{{ url('/') }}#berita">Berita</a></li>
        <li><a href="{{ url('/') }}#galeri">Galeri</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Informasi</h4>
      <ul>
        <li><a href="#">Semua Berita</a></li>
        <li><a href="#">Galeri Foto</a></li>
        <li><a href="#">Pengumuman</a></li>
        <li><a href="{{ url('/') }}#faq">FAQ</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Kontak</h4>
      <ul>
        @php $p = app(\App\Models\Pengaturan::class)::ambil(); @endphp
        <li><a href="#">📍 {{ $p->alamat_sekretariat ?? 'Jl. Raya Compreng, Subang' }}</a></li>
        <li><a href="#">📞 {{ $p->no_hp_panitia ?? '-' }}</a></li>
        <li><a href="#">🕐 Sen–Jum, 08.00–16.00</a></li>
      </ul>
    </div>
  </div>

  <div class="footer-bottom">
    <p>© {{ date('Y') }} Paskibra Kecamatan Compreng. Hak Cipta Dilindungi.</p>
    <p class="built">Dibangun dengan <span>♥</span> untuk Indonesia</p>
  </div>
</footer>