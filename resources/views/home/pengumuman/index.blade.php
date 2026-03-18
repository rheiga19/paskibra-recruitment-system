<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pengumuman Kelulusan — {{ config('app.name') }}</title>
  <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/home.css') }}">
  <style>
    .pg-hero {
      background: linear-gradient(135deg, #cc0000 0%, #6b0000 100%);
      padding: 120px 24px 80px;
      text-align: center; position: relative; overflow: hidden;
    }
    .pg-hero::before {
      content: ''; position: absolute; inset: 0;
      background-image:
        repeating-linear-gradient(0deg, transparent, transparent 60px, rgba(255,255,255,.03) 60px, rgba(255,255,255,.03) 61px),
        repeating-linear-gradient(90deg, transparent, transparent 60px, rgba(255,255,255,.03) 60px, rgba(255,255,255,.03) 61px);
    }
    .pg-label { font-size:11px; letter-spacing:3px; opacity:.6; text-transform:uppercase; margin-bottom:12px; color:#fff; position:relative; }
    .pg-hero h1 { font-family:'Bebas Neue',sans-serif; font-size:clamp(3rem,8vw,5rem); letter-spacing:4px; color:#fff; line-height:1; margin-bottom:16px; position:relative; }
    .pg-hero p  { color:rgba(255,255,255,.75); font-size:15px; max-width:520px; margin:0 auto; position:relative; }
    .pg-meta {
      display:inline-flex; align-items:center; gap:8px; margin-top:16px;
      background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2);
      border-radius:100px; padding:8px 18px; font-size:13px; color:rgba(255,255,255,.85); position:relative;
    }

    .pg-wrap { max-width: 1000px; margin: 0 auto; padding: 48px 24px 80px; }

    /* ── Belum diumumkan ── */
    .pg-tunggu {
      text-align: center; padding: 80px 24px;
      background: var(--abu, #1a1a1a);
      border: 1px solid var(--card-border, rgba(255,255,255,.07));
      border-radius: 20px;
    }
    [data-theme="light"] .pg-tunggu { background: #fff; border-color: #e5e7eb; box-shadow: 0 2px 16px rgba(0,0,0,.06); }
    .pg-tunggu .nd-icon { font-size: 60px; display: block; margin-bottom: 20px; opacity: .4; }
    .pg-tunggu h2 { font-family:'Bebas Neue',sans-serif; font-size:2rem; letter-spacing:2px; margin-bottom:12px; color:var(--teks-utama,#fff); }
    [data-theme="light"] .pg-tunggu h2 { color: #111; }
    .pg-tunggu p { color:var(--teks-redup,#888); font-size:15px; max-width:420px; margin:0 auto; line-height:1.7; }

    /* ── Pesan admin ── */
    .pg-pesan {
      background: rgba(28,200,138,.08); border: 1px solid rgba(28,200,138,.25);
      border-radius: 14px; padding: 18px 24px; margin-bottom: 28px;
      display: flex; align-items: flex-start; gap: 14px;
    }
    .pg-pesan-icon { font-size: 24px; flex-shrink: 0; margin-top: 2px; }
    .pg-pesan strong { display: block; font-size: 14px; color: #1cc88a; margin-bottom: 4px; }
    .pg-pesan p { font-size: 14px; color: var(--teks-redup,#aaa); margin: 0; line-height: 1.6; }

    /* ── Filter ── */
    .hasil-filter { display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; }
    .hasil-filter select,
    .hasil-filter input {
      padding: 11px 14px;
      border: 1px solid var(--card-border, rgba(255,255,255,.12));
      border-radius: 10px; font-size: 14px; font-family: 'Outfit', sans-serif;
      background: var(--abu, #1a1a1a); color: var(--teks-utama, #fff);
      outline: none; transition: border-color .2s;
    }
    [data-theme="light"] .hasil-filter select,
    [data-theme="light"] .hasil-filter input { background: #fff; color: #111; border-color: #d1d5db; }
    .hasil-filter select:focus, .hasil-filter input:focus { border-color: #cc0000; }
    .hasil-filter input { flex: 1; min-width: 220px; }

    /* ── Tabel ── */
    .tabel-box {
      background: var(--abu, #1a1a1a);
      border: 1px solid var(--card-border, rgba(255,255,255,.07));
      border-radius: 16px; overflow: hidden;
    }
    [data-theme="light"] .tabel-box { background:#fff; border-color:#e5e7eb; box-shadow:0 2px 12px rgba(0,0,0,.05); }

    .tabel-head {
      padding: 18px 24px; display:flex; justify-content:space-between; align-items:center;
      border-bottom: 1px solid var(--card-border, rgba(255,255,255,.07));
    }
    [data-theme="light"] .tabel-head { border-color: #f0f0f0; }
    .tabel-head h3 { font-size:16px; font-weight:700; color:var(--teks-utama,#fff); margin:0; }
    [data-theme="light"] .tabel-head h3 { color: #111; }
    .tabel-head small { font-size:13px; color:var(--teks-redup,#888); }

    .tabel-scroll { overflow-x: auto; }
    table { width:100%; border-collapse:collapse; }
    thead tr { background: rgba(255,255,255,.03); }
    [data-theme="light"] thead tr { background: #f9fafb; }
    th {
      padding:12px 20px; text-align:left; font-size:11px; font-weight:700;
      letter-spacing:1px; text-transform:uppercase; color:var(--teks-redup,#888);
      border-bottom:1px solid var(--card-border, rgba(255,255,255,.07)); white-space:nowrap;
    }
    [data-theme="light"] th { border-color: #f0f0f0; }
    td {
      padding:16px 20px; font-size:14px;
      border-bottom:1px solid rgba(255,255,255,.04);
      color:var(--teks-utama,#fff);
    }
    [data-theme="light"] td { color:#333; border-color:#f5f5f5; }
    tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background: rgba(204,0,0,.04); }

    .no-daftar { font-weight:800; color:#cc0000; font-size:13px; }
    .nama-peserta { font-weight:600; display:block; }
    .jk-badge { font-size:11px; color:var(--teks-redup,#888); margin-top:2px; }
    .rekrutmen-label { font-size:12px; color:var(--teks-redup,#888); }

    .badge-lulus {
      display:inline-flex; align-items:center; gap:5px;
      background:rgba(28,200,138,.12); color:#1cc88a;
      border:1px solid rgba(28,200,138,.3);
      padding:5px 12px; border-radius:20px; font-size:12px; font-weight:700;
    }

    .pg-diumumkan-at {
      text-align:center; font-size:12px;
      color:var(--teks-redup,#888); margin-top:16px;
    }

    @media (max-width:640px) {
      th:nth-child(4), td:nth-child(4) { display:none; }
    }
  </style>
</head>
<body>

@include('home.navbar')

{{-- Hero --}}
<div class="pg-hero">
  <div class="pg-label">Paskibra Kecamatan Compreng</div>
  <h1>Pengumuman<br>Kelulusan</h1>
  <p>Daftar peserta yang dinyatakan lulus seleksi Paskibra</p>
  @if($rekrutmenAktif)
  <div class="pg-meta">
    📅 {{ $rekrutmenAktif->nama }}
    &nbsp;·&nbsp;
    {{ $rekrutmenAktif->tanggal_buka->format('d M Y') }} – {{ $rekrutmenAktif->tanggal_tutup->format('d M Y') }}
  </div>
  @endif
</div>

<div class="pg-wrap">

  @if(!$pengaturan->pengumuman_aktif)
  {{-- Belum diumumkan --}}
  <div class="pg-tunggu">
    <h2>Pengumuman Belum Tersedia</h2>
    <p>Panitia belum mengumumkan hasil kelulusan seleksi.<br>Pantau terus halaman ini untuk informasi terbaru.</p>
    @if($rekrutmenAktif)
    <p style="margin-top:12px;font-size:13px;">
      Rekrutmen aktif: <strong style="color:#cc0000;">{{ $rekrutmenAktif->nama }}</strong>
    </p>
    @endif
  </div>

  @else
  {{-- Sudah diumumkan --}}

  @if($pengaturan->pesan_pengumuman)
  <div class="pg-pesan">
    <div class="pg-pesan-icon">📢</div>
    <div>
      <strong>Pengumuman dari Panitia</strong>
      <p>{{ $pengaturan->pesan_pengumuman }}</p>
    </div>
  </div>
  @endif

  @if($lulusList->isEmpty())
  <div class="pg-tunggu">
    <span class="nd-icon">🏆</span>
    <h2>Belum Ada Data</h2>
    <p>Pengumuman sudah diaktifkan namun belum ada peserta dengan status lulus final.</p>
  </div>

  @else

  {{-- Filter --}}
  <div class="hasil-filter">
    <select id="filterJK" onchange="filterHasil()">
      <option value="">Semua Gender</option>
      <option value="L">♂ Putra</option>
      <option value="P">♀ Putri</option>
    </select>
    <input type="text" id="searchNama"
           placeholder="🔍  Cari nama / no. pendaftaran..."
           oninput="filterHasil()">
  </div>

  <div class="tabel-box">
    <div class="tabel-head">
      <h3>Daftar Peserta Lulus Seleksi</h3>
      <small id="jumlahTampil">{{ $lulusList->count() }} peserta</small>
    </div>
    <div class="tabel-scroll">
      <table id="tabelLulus">
        <thead>
          <tr>
            <th style="width:40px;">#</th>
            <th>No. Pendaftaran</th>
            <th>Nama Peserta</th>
            <th>Asal Sekolah</th>
            <th>Rekrutmen</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($lulusList as $i => $p)
          <tr
            data-jk="{{ $p->jenis_kelamin }}"
            data-nama="{{ strtolower($p->nama_lengkap) }}"
            data-nodaftar="{{ strtolower($p->no_pendaftaran ?? '') }}"
          >
            <td style="color:var(--teks-redup);font-size:12px;">{{ $i + 1 }}</td>
            <td><span class="no-daftar">{{ $p->no_pendaftaran ?? '-' }}</span></td>
            <td>
              <span class="nama-peserta">{{ $p->nama_lengkap }}</span>
            </td>
            <td>{{ $p->nama_sekolah ?? '-' }}</td>
            <td><span class="rekrutmen-label">{{ $p->rekrutmen->nama ?? '-' }}</span></td>
            <td><span class="badge-lulus">✓ LULUS</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="pg-diumumkan-at">
    Diumumkan: {{ $pengaturan->pengumuman_diaktifkan_at?->translatedFormat('d F Y, H:i') ?? '-' }}
  </div>

  @endif
  @endif

</div>

@include('home.footer')

<button class="theme-toggle" id="themeToggle" title="Ganti tema" onclick="toggleTheme()">
  <span id="themeIcon">☀️</span>
</button>

<script>
  function filterHasil() {
    const jk     = document.getElementById('filterJK').value;
    const search = document.getElementById('searchNama').value.toLowerCase().trim();
    const rows   = document.querySelectorAll('#tabelLulus tbody tr');
    let visible  = 0;
    rows.forEach(row => {
      const ok =
        (!jk     || row.dataset.jk === jk) &&
        (!search || row.dataset.nama.includes(search) || row.dataset.nodaftar.includes(search));
      row.style.display = ok ? '' : 'none';
      if (ok) visible++;
    });
    const el = document.getElementById('jumlahTampil');
    if (el) el.textContent = visible + ' peserta';
  }

  window.addEventListener('scroll', () => {
    const nav = document.getElementById('navbar');
    if (nav) nav.style.borderBottomColor = window.scrollY > 50 ? 'rgba(204,0,0,0.25)' : 'rgba(204,0,0,0.15)';
  });
  function toggleMenu() { document.getElementById('navMobile').classList.toggle('open'); }
  document.addEventListener('click', e => {
    const menu = document.getElementById('navMobile');
    const btn  = document.getElementById('hamburger');
    if (menu && btn && !menu.contains(e.target) && !btn.contains(e.target)) menu.classList.remove('open');
  });

  const html = document.documentElement;
  const icon = document.getElementById('themeIcon');
  function applyTheme(t) {
    if (t === 'light') { html.setAttribute('data-theme','light'); if(icon) icon.textContent='🌙'; }
    else               { html.removeAttribute('data-theme');       if(icon) icon.textContent='☀️'; }
  }
  function toggleTheme() {
    const next = html.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
    localStorage.setItem('theme', next); applyTheme(next);
  }
  const stored = localStorage.getItem('theme');
  if (stored) applyTheme(stored);
  else if (window.matchMedia('(prefers-color-scheme: light)').matches) applyTheme('light');
  else applyTheme('dark');
  window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', e => {
    if (!localStorage.getItem('theme')) applyTheme(e.matches ? 'light' : 'dark');
  });
</script>
</body>
</html>