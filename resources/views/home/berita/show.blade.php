<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $beritum->judul }} — {{ config('app.name') }}</title>
  <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/home.css') }}">
  <style>
    .artikel-body { font-size:16px;line-height:1.8;color:var(--teks-utama,#e5e5e5); }
    .artikel-body p { margin-bottom:18px; }
    .artikel-body img { max-width:100%;border-radius:12px;margin:16px 0; }
    [data-theme="light"] .artikel-body { color:#222; }
  </style>
</head>
<body>
@include('home.navbar')

<div style="max-width:820px;margin:0 auto;padding:100px 24px 80px;">

  {{-- Breadcrumb --}}
  <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--teks-redup,#888);margin-bottom:28px;">
    <a href="{{ route('home') }}" style="color:inherit;text-decoration:none;">Home</a>
    <span>›</span>
    <a href="{{ route('berita.index') }}" style="color:inherit;text-decoration:none;">Berita</a>
    <span>›</span>
    <span style="color:var(--teks-utama,#fff);">{{ Str::limit($beritum->judul, 40) }}</span>
  </div>

  {{-- Header artikel --}}
  <div style="margin-bottom:28px;">
    <div style="display:inline-block;background:rgba(204,0,0,.15);color:#cc0000;border:1px solid rgba(204,0,0,.3);border-radius:20px;padding:5px 14px;font-size:12px;font-weight:700;letter-spacing:1px;margin-bottom:14px;">BERITA</div>
    <h1 style="font-family:'Bebas Neue',sans-serif;font-size:clamp(2rem,5vw,3rem);letter-spacing:2px;line-height:1.1;margin-bottom:14px;color:var(--teks-utama,#fff);">{{ $beritum->judul }}</h1>
    <div style="color:var(--teks-redup,#888);font-size:13px;">
      📅 {{ $beritum->created_at->translatedFormat('d F Y') }}
    </div>
  </div>

  {{-- Gambar utama --}}
  @if($beritum->gambar)
  <div style="border-radius:16px;overflow:hidden;margin-bottom:32px;max-height:420px;">
    <img src="{{ asset('storage/'.$beritum->gambar) }}" alt="{{ $beritum->judul }}" style="width:100%;height:100%;object-fit:cover;">
  </div>
  @endif

  {{-- Isi artikel --}}
  <div class="artikel-body">
    {!! $beritum->isi ?? nl2br(e($beritum->konten ?? '')) !!}
  </div>

  <hr style="border:none;border-top:1px solid rgba(255,255,255,.08);margin:40px 0;">

  {{-- Berita lainnya --}}
  @if($beritaLain->isNotEmpty())
  <div>
    <h3 style="font-family:'Bebas Neue',sans-serif;font-size:1.5rem;letter-spacing:2px;margin-bottom:20px;color:var(--teks-utama,#fff);">Berita Lainnya</h3>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;">
      @foreach($beritaLain as $b)
      <a href="{{ route('berita.show', $b) }}" class="news-card" style="text-decoration:none;">
        <div class="news-img">
          @if($b->gambar)<img src="{{ asset('storage/'.$b->gambar) }}" alt="{{ $b->judul }}" style="width:100%;height:100%;object-fit:cover;">@else🏛️@endif
        </div>
        <div class="news-body">
          <div class="news-cat">Berita</div>
          <div class="news-title">{{ $b->judul }}</div>
          <div class="news-meta">{{ $b->created_at->translatedFormat('d F Y') }}</div>
        </div>
      </a>
      @endforeach
    </div>
  </div>
  @endif

  <div style="margin-top:32px;">
    <a href="{{ route('berita.index') }}" style="display:inline-flex;align-items:center;gap:8px;color:#cc0000;font-weight:600;font-size:14px;text-decoration:none;">
      ← Kembali ke Semua Berita
    </a>
  </div>

</div>

@include('home.footer')
<button class="theme-toggle" id="themeToggle" title="Ganti tema" onclick="toggleTheme()"><span id="themeIcon">☀️</span></button>
<script>
  window.addEventListener('scroll',()=>{const n=document.getElementById('navbar');if(n)n.style.borderBottomColor=window.scrollY>50?'rgba(204,0,0,0.25)':'rgba(204,0,0,0.15)';});
  function toggleMenu(){document.getElementById('navMobile').classList.toggle('open');}
  document.addEventListener('click',(e)=>{const m=document.getElementById('navMobile'),b=document.getElementById('hamburger');if(m&&b&&!m.contains(e.target)&&!b.contains(e.target))m.classList.remove('open');});
  const html=document.documentElement,icon=document.getElementById('themeIcon');
  function applyTheme(t){if(t==='light'){html.setAttribute('data-theme','light');icon.textContent='🌙';}else{html.removeAttribute('data-theme');icon.textContent='☀️';}}
  function toggleTheme(){const n=html.getAttribute('data-theme')==='light'?'dark':'light';localStorage.setItem('theme',n);applyTheme(n);}
  const s=localStorage.getItem('theme');if(s)applyTheme(s);else if(window.matchMedia('(prefers-color-scheme:light)').matches)applyTheme('light');else applyTheme('dark');
</script>
</body>
</html>