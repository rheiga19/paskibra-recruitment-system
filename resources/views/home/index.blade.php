<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name') }} — Pasukan Pengibar Bendera</title>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>

@include('home.navbar')

{{-- HERO --}}
<section class="hero" id="hero">
  <div class="hero-bg"></div>
  <div class="hero-pattern"><div class="hero-pattern-inner"></div></div>
  <div class="hero-garuda">🦅</div>
  <div class="hero-flag">
    <div class="flag-merah"></div>
    <div class="flag-putih"></div>
    <div class="flag-shine"></div>
  </div>
  <div class="hero-content">
    <div class="hero-badge">
      <span class="badge-dot"></span>
      @if($rekrutmenAktif) Pendaftaran {{ $rekrutmenAktif->tahun }} Dibuka
      @else Paskibra Kecamatan Compreng @endif
    </div>
    <h1 class="hero-title">
      PASUKAN<br><span class="accent">PENGIBAR</span><br><span class="gold">BENDERA</span>
    </h1>
    <p class="hero-subtitle">
      Bergabunglah dengan generasi penerus kebanggaan Kecamatan Compreng yang terpilih untuk mengibarkan Sang Saka Merah Putih. Seleksi ketat, pembekalan intensif, kehormatan seumur hidup.
    </p>
    <div class="hero-cta">
      @auth
        <a href="#" class="btn-hero-primary">
          Daftar Sekarang <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      @else
        <a href="{{ route('register') }}" class="btn-hero-primary">
          Daftar Sekarang <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      @endauth
      <a href="#tentang" class="btn-hero-secondary">Pelajari Lebih Lanjut</a>
    </div>
    <div class="hero-stats">
      <div class="stat-item"><div class="stat-num">16<span class="stat-accent">+</span></div><div class="stat-label">Kuota Peserta</div></div>
      <div class="stat-item"><div class="stat-num">3</div><div class="stat-label">Tahap Seleksi</div></div>
      <div class="stat-item"><div class="stat-num">{{ date('Y') }}</div><div class="stat-label">Angkatan</div></div>
    </div>
  </div>
  <div class="hero-scroll"><div class="scroll-line"></div>Scroll untuk lebih</div>
</section>

{{-- MARQUEE --}}
<div class="marquee-section">
  <div class="marquee-track">
    @php $items = ['MERAH PUTIH','KEHORMATAN BANGSA','SELEKSI '.date('Y'),'PENGABDIAN TULUS','INDONESIA MERDEKA','KECAMATAN COMPRENG','UPACARA KENEGARAAN']; @endphp
    @foreach(array_merge($items,$items) as $item)
      <div class="marquee-item"><div class="marquee-dot"></div>{{ $item }}</div>
    @endforeach
  </div>
</div>

{{-- STATUS REKRUTMEN --}}
@if($rekrutmenAktif)
<div class="rekrutmen-bar">
  <div class="rekrutmen-bar-left">
    <div class="badge-buka"><span class="badge-buka-dot"></span> PENDAFTARAN DIBUKA</div>
    <strong>{{ $rekrutmenAktif->nama }}</strong>
    <span style="color:var(--teks-redup);font-size:13px;">
      {{ $rekrutmenAktif->tanggal_buka->format('d M') }} – {{ $rekrutmenAktif->tanggal_tutup->format('d M Y') }}
    </span>
  </div>
  @guest <a href="{{ route('register') }}" class="btn-primary-red">Daftar Sekarang →</a> @endguest
</div>
@endif

{{-- TENTANG --}}
<section class="section" id="tentang">
  <div class="about-grid">

    {{-- VISUAL: gambar paskibra.png menggantikan emoji --}}
    <div class="about-visual">
      <div class="about-card-main" style="padding:0;overflow:visible;background:none;border:none;">

        {{-- Frame foto utama --}}
        <div style="position:relative;border-radius:20px;overflow:hidden;aspect-ratio:4/3;box-shadow:0 24px 64px rgba(0,0,0,.5);">
          <img
            src="{{ asset('images/paskibra1.png') }}"
            alt="Paskibra Kecamatan Compreng"
            style="width:100%;height:100%;object-fit:cover;display:block;"
          >
          {{-- Overlay gradient bawah --}}
          <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.65) 0%,transparent 55%);pointer-events:none;"></div>
          {{-- Keterangan bawah foto --}}
          <div style="position:absolute;bottom:0;left:0;right:0;padding:20px 22px;">
            <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:3px;color:#fff;line-height:1.2;">PASKIBRA {{ date('Y') }}</div>
            <div style="font-size:12px;color:rgba(255,255,255,.65);margin-top:3px;">Kecamatan Compreng, Kabupaten Subang</div>
          </div>
          {{-- Badge resmi pojok kanan atas --}}
          <div style="position:absolute;top:14px;right:14px;background:rgba(0,0,0,.45);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.2);border-radius:20px;padding:5px 13px;font-size:12px;color:#fff;font-weight:700;letter-spacing:1px;">🇮🇩 RESMI</div>
        </div>

        {{-- Badge program resmi di bawah foto --}}
        <div class="about-card-badge" style="margin-top:14px;">
          <div class="badge-icon-wrap">🎖️</div>
          <div>
            <div class="badge-text-title">Program Resmi Kecamatan</div>
            <div class="badge-text-sub">Kecamatan Compreng, Subang</div>
          </div>
        </div>

      </div>

      {{-- Float card angka 17 --}}
      <div class="about-float-card">
        <div class="float-num">17</div>
        <div class="float-label">Agustus Setiap Tahun</div>
      </div>
    </div>

    {{-- TEKS TENTANG --}}
    <div>
      <div class="section-label">Tentang Kami</div>
      <h2 class="section-title">Menjaga Kehormatan<br>Sang Saka Merah Putih</h2>
      <p class="section-desc">Paskibra Kecamatan Compreng adalah program pembinaan generasi muda terpilih yang diberi kehormatan untuk mengibarkan Bendera Merah Putih dalam Upacara Peringatan Hari Kemerdekaan tingkat kecamatan.</p>
      <div class="about-features">
        <div class="feature-row"><div class="feature-icon">🎓</div><div><div class="feature-title">Pembekalan Intensif</div><div class="feature-desc">Pelatihan fisik, mental, wawasan kebangsaan, dan pembentukan karakter selama persiapan.</div></div></div>
        <div class="feature-row"><div class="feature-icon">🏅</div><div><div class="feature-title">Seleksi Berjenjang</div><div class="feature-desc">Seleksi administrasi, tes fisik, dan wawancara dengan penilaian ketat oleh panitia.</div></div></div>
        <div class="feature-row"><div class="feature-icon">🤝</div><div><div class="feature-title">Jaringan Alumni</div><div class="feature-desc">Bergabung dengan komunitas alumni Paskibra Compreng yang terus berkembang setiap tahunnya.</div></div></div>
      </div>
    </div>

  </div>
</section>

{{-- ALUR PENDAFTARAN --}}
<section class="section daftar-section" id="pendaftaran">
  <div class="daftar-header">
    <div class="section-label">Alur Pendaftaran</div>
    <h2 class="section-title">Tahapan Seleksi {{ date('Y') }}</h2>
    <p class="section-desc">Ikuti langkah-langkah berikut untuk mendaftarkan diri sebagai calon Paskibra Kecamatan Compreng.</p>
  </div>
  <div class="timeline">
    <div class="timeline-progress"></div>
    <div class="tl-step"><div class="tl-dot done">✓</div><div class="tl-date">Mar – Apr {{ date('Y') }}</div><div class="tl-title">Pendaftaran Online</div><div class="tl-desc">Buat akun, isi profil lengkap, dan upload semua dokumen persyaratan</div></div>
    <div class="tl-step"><div class="tl-dot active">2</div><div class="tl-date">Mei {{ date('Y') }}</div><div class="tl-title">Seleksi Administrasi</div><div class="tl-desc">Verifikasi berkas dan kelengkapan dokumen oleh panitia</div></div>
    <div class="tl-step"><div class="tl-dot">3</div><div class="tl-date">Mei {{ date('Y') }}</div><div class="tl-title">Seleksi Fisik</div><div class="tl-desc">Tes kesehatan, pengukuran fisik, dan kemampuan baris-berbaris</div></div>
    <div class="tl-step"><div class="tl-dot">4</div><div class="tl-date">Mei {{ date('Y') }}</div><div class="tl-title">Wawancara & TIU</div><div class="tl-desc">Tes wawancara, wawasan kebangsaan, dan Tes Intelegensi Umum</div></div>
    <div class="tl-step"><div class="tl-dot">17</div><div class="tl-date">17 Agt {{ date('Y') }}</div><div class="tl-title">Upacara HUT RI</div><div class="tl-desc">Momen kehormatan tertinggi bagi pejuang Compreng</div></div>
  </div>
</section>

{{-- SYARAT --}}
<section class="section" id="syarat">
  <div class="section-label">Persyaratan</div>
  <h2 class="section-title">Syarat & Ketentuan Pendaftar</h2>
  <div class="req-grid">
    <div class="req-card"><div class="req-card-icon">👤</div><div class="req-card-title">Persyaratan Umum</div><ul><li>Warga Negara Indonesia</li><li>Berdomisili di Kecamatan Compreng</li><li>Siswa aktif SMP/MTs/SMA/MA/SMK</li><li>Berbadan sehat jasmani & rohani</li><li>Belum pernah menjadi anggota Paskibra</li></ul></div>
    <div class="req-card"><div class="req-card-icon">📄</div><div class="req-card-title">Dokumen Wajib</div><ul><li>KTP Pelajar / Kartu Pelajar</li><li>Akta Kelahiran</li><li>Rapor semester terakhir</li><li>Surat keterangan sehat dari dokter</li><li>Pas foto terbaru 4×6</li><li>Surat izin orang tua / wali</li></ul></div>
    <div class="req-card"><div class="req-card-icon">🏃</div><div class="req-card-title">Kriteria Fisik</div><ul><li>Tinggi badan min. 163 cm (putra)</li><li>Tinggi badan min. 155 cm (putri)</li><li>Lulus tes kesehatan dasar</li><li>Kemampuan baris-berbaris dasar</li><li>Bebas narkoba dan tidak merokok</li></ul></div>
  </div>
</section>

{{-- BERITA --}}
<section class="section" id="berita">
  <div class="section-label">Berita & Info</div>
  <h2 class="section-title">Informasi Terkini</h2>
  <div class="news-grid">
    @forelse($berita as $i => $b)
    <a href="#" class="news-card {{ $i === 0 ? 'featured' : '' }}">
      <div class="news-img">
        @if($b->gambar) <img src="{{ asset('storage/'.$b->gambar) }}" alt="{{ $b->judul }}"> @else 🏛️ @endif
      </div>
      <div class="news-body">
        <div class="news-cat">Berita</div>
        <div class="news-title">{{ $b->judul }}</div>
        <div class="news-meta">{{ $b->created_at->translatedFormat('d F Y') }}</div>
      </div>
    </a>
    @empty
    <div class="news-card featured"><div class="news-img">🏛️</div><div class="news-body"><div class="news-cat">Pengumuman</div><div class="news-title">Pendaftaran Paskibra {{ date('Y') }} Resmi Dibuka</div><div class="news-meta">{{ date('d M Y') }}</div></div></div>
    @endforelse
  </div>
</section>

{{-- GALERI --}}
<section class="section" id="galeri">
  <div class="section-label">Galeri</div>
  <h2 class="section-title">Momen Bersejarah</h2>
  <div class="gallery-grid">
    @forelse($galeri as $g)
    <a href="#" class="gallery-item"><img src="{{ asset('storage/'.$g->foto) }}" alt="{{ $g->judul }}"></a>
    @empty
    <div class="gallery-item">🎖️</div>
    <div class="gallery-item">🏛️</div>
    <div class="gallery-item" style="font-size:80px;">🦅</div>
    <div class="gallery-item">🇮🇩</div>
    <div class="gallery-item">🏅</div>
    <div class="gallery-item">✊</div>
    @endforelse
  </div>
</section>

{{-- FAQ --}}
<section class="section" id="faq">
  <div style="text-align:center;margin-bottom:16px;">
    <div class="section-label" style="justify-content:center;">FAQ</div>
    <h2 class="section-title">Pertanyaan Umum</h2>
  </div>
  <div class="faq-list">
    <div class="faq-item open">
      <div class="faq-q" onclick="toggleFaq(this)">Apakah pendaftaran sudah dibuka? <span class="faq-toggle">+</span></div>
      <div class="faq-a">@if($rekrutmenAktif) Ya! Pendaftaran {{ $rekrutmenAktif->nama }} sudah dibuka mulai {{ $rekrutmenAktif->tanggal_buka->translatedFormat('d F Y') }} hingga {{ $rekrutmenAktif->tanggal_tutup->translatedFormat('d F Y') }}. @else Saat ini pendaftaran belum dibuka. Pantau terus website ini untuk informasi terbaru. @endif</div>
    </div>
    <div class="faq-item">
      <div class="faq-q" onclick="toggleFaq(this)">Apakah ada biaya pendaftaran? <span class="faq-toggle">+</span></div>
      <div class="faq-a">Tidak ada biaya pendaftaran. Seluruh proses seleksi Paskibra Kecamatan Compreng tidak dipungut biaya apapun.</div>
    </div>
    <div class="faq-item">
      <div class="faq-q" onclick="toggleFaq(this)">Berapa tinggi badan minimum yang diperlukan? <span class="faq-toggle">+</span></div>
      <div class="faq-a">Tinggi badan minimum adalah 163 cm untuk peserta putra dan 155 cm untuk peserta putri.</div>
    </div>
    <div class="faq-item">
      <div class="faq-q" onclick="toggleFaq(this)">Apakah siswa SMP bisa mendaftar? <span class="faq-toggle">+</span></div>
      <div class="faq-a">Ya! Siswa aktif SMP, MTs, SMA, MA, maupun SMK sederajat dapat mendaftar sepanjang memenuhi semua persyaratan yang ditentukan.</div>
    </div>
    <div class="faq-item">
      <div class="faq-q" onclick="toggleFaq(this)">Bagaimana cara mengetahui hasil seleksi? <span class="faq-toggle">+</span></div>
      <div class="faq-a">Hasil seleksi akan diumumkan melalui halaman pengumuman di website ini dan bisa dipantau melalui dashboard akun masing-masing peserta.</div>
    </div>
  </div>
</section>

{{-- CTA BANNER --}}
<div class="cta-banner">
  <h2>SIAP MENGHARUMKAN NAMA COMPRENG?</h2>
  <p>Jadilah bagian dari generasi penerus kebanggaan Kecamatan Compreng. Daftarkan diri sekarang sebelum pendaftaran ditutup.</p>
  <div class="cta-actions">
    @auth
      <a href="#" class="btn-hero-primary">Daftar Sekarang <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
    @else
      <a href="{{ route('register') }}" class="btn-hero-primary">Daftar Sekarang <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
    @endauth
    <a href="{{ route('pengumuman') }}" class="btn-hero-secondary">Lihat Pengumuman</a>
  </div>
</div>

@include('home.footer')

<button class="theme-toggle" id="themeToggle" title="Ganti tema" onclick="toggleTheme()">
  <span id="themeIcon">☀️</span>
</button>

<script>
  window.addEventListener('scroll', () => {
    const nav = document.getElementById('navbar');
    nav.style.borderBottomColor = window.scrollY > 50 ? 'rgba(204,0,0,0.25)' : 'rgba(204,0,0,0.15)';
  });
  function toggleMenu() { document.getElementById('navMobile').classList.toggle('open'); }
  document.addEventListener('click', (e) => {
    const menu = document.getElementById('navMobile');
    const btn  = document.getElementById('hamburger');
    if (!menu.contains(e.target) && !btn.contains(e.target)) menu.classList.remove('open');
  });
  function toggleFaq(el) {
    const item = el.parentElement;
    const wasOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
    if (!wasOpen) item.classList.add('open');
  }
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) { e.target.style.opacity='1'; e.target.style.transform='translateY(0)'; }
    });
  }, { threshold: 0.1 });
  document.querySelectorAll('.req-card,.news-card,.faq-item,.feature-row,.tl-step').forEach(el => {
    el.style.opacity='0'; el.style.transform='translateY(20px)';
    el.style.transition='opacity 0.5s ease, transform 0.5s ease';
    observer.observe(el);
  });
  const html=document.documentElement, icon=document.getElementById('themeIcon');
  function applyTheme(t){
    if(t==='light'){html.setAttribute('data-theme','light');icon.textContent='🌙';}
    else{html.removeAttribute('data-theme');icon.textContent='☀️';}
  }
  function toggleTheme(){
    const next=html.getAttribute('data-theme')==='light'?'dark':'light';
    localStorage.setItem('theme',next);applyTheme(next);
  }
  const stored=localStorage.getItem('theme');
  if(stored)applyTheme(stored);
  else if(window.matchMedia('(prefers-color-scheme: light)').matches)applyTheme('light');
  else applyTheme('dark');
  window.matchMedia('(prefers-color-scheme: light)').addEventListener('change',(e)=>{
    if(!localStorage.getItem('theme'))applyTheme(e.matches?'light':'dark');
  });
</script>
</body>
</html>