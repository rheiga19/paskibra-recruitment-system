<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $galeri->judul }} — {{ config('app.name') }}</title>
  <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/home.css') }}">
  <style>
    .gal-thumb { position:relative;border-radius:12px;overflow:hidden;aspect-ratio:4/3;cursor:pointer;transition:transform .2s; }
    .gal-thumb:hover { transform:scale(1.03); }
    .gal-thumb img { width:100%;height:100%;object-fit:cover; }
    .gal-thumb-overlay { position:absolute;inset:0;background:rgba(0,0,0,.4);opacity:0;transition:opacity .2s;display:flex;align-items:center;justify-content:center; }
    .gal-thumb:hover .gal-thumb-overlay { opacity:1; }
    /* Lightbox */
    .lb { display:none;position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:9999;align-items:center;justify-content:center;padding:20px; }
    .lb.open { display:flex; }
    .lb img { max-width:100%;max-height:90vh;border-radius:12px;object-fit:contain; }
    .lb-close { position:fixed;top:20px;right:24px;color:#fff;font-size:32px;cursor:pointer;line-height:1;opacity:.8; }
    .lb-close:hover { opacity:1; }
  </style>
</head>
<body>
@include('home.navbar')

<div style="max-width:900px;margin:0 auto;padding:100px 24px 80px;">

  {{-- Breadcrumb --}}
  <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--teks-redup,#888);margin-bottom:28px;">
    <a href="{{ route('home') }}" style="color:inherit;text-decoration:none;">Home</a>
    <span>›</span>
    <a href="{{ route('galeri.index') }}" style="color:inherit;text-decoration:none;">Galeri</a>
    <span>›</span>
    <span style="color:var(--teks-utama,#fff);">{{ Str::limit($galeri->judul, 40) }}</span>
  </div>

  {{-- Foto utama -- klik untuk lightbox --}}
  <div style="border-radius:20px;overflow:hidden;margin-bottom:24px;cursor:zoom-in;max-height:560px;" onclick="openLightbox('{{ asset('storage/'.$galeri->path) }}')">
    <img src="{{ asset('storage/'.$galeri->path) }}" alt="{{ $galeri->judul }}" style="width:100%;height:100%;object-fit:cover;display:block;">
  </div>

  <h1 style="font-family:'Bebas Neue',sans-serif;font-size:2rem;letter-spacing:2px;margin-bottom:8px;color:var(--teks-utama,#fff);">{{ $galeri->judul }}</h1>
  @if($galeri->deskripsi)
  <p style="color:var(--teks-redup,#aaa);font-size:15px;line-height:1.7;">{{ $galeri->deskripsi }}</p>
  @endif
  <div style="font-size:13px;color:var(--teks-redup,#888);margin-top:8px;">📅 {{ $galeri->created_at->translatedFormat('d F Y') }}</div>

  <hr style="border:none;border-top:1px solid rgba(255,255,255,.08);margin:36px 0;">

  {{-- Foto lainnya --}}
  @if($galeriLain->isNotEmpty())
  <h3 style="font-family:'Bebas Neue',sans-serif;font-size:1.4rem;letter-spacing:2px;margin-bottom:18px;color:var(--teks-utama,#fff);">Foto Lainnya</h3>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
    @foreach($galeriLain as $g)
    <a href="{{ route('galeri.show', $g) }}" class="gal-thumb">
      <img src="{{ asset('storage/'.$g->path) }}" alt="{{ $g->judul }}">
      <div class="gal-thumb-overlay"><span style="color:#fff;font-size:20px;">🔍</span></div>
    </a>
    @endforeach
  </div>
  @endif

  <div style="margin-top:32px;">
    <a href="{{ route('galeri.index') }}" style="display:inline-flex;align-items:center;gap:8px;color:#cc0000;font-weight:600;font-size:14px;text-decoration:none;">
      ← Kembali ke Galeri
    </a>
  </div>
</div>

{{-- Lightbox --}}
<div class="lb" id="lightbox" onclick="closeLightbox()">
  <div class="lb-close" onclick="closeLightbox()">✕</div>
  <img id="lbImg" src="" alt="">
</div>

@include('home.footer')
<button class="theme-toggle" id="themeToggle" title="Ganti tema" onclick="toggleTheme()"><span id="themeIcon">☀️</span></button>
<script>
  function openLightbox(src){document.getElementById('lbImg').src=src;document.getElementById('lightbox').classList.add('open');document.body.style.overflow='hidden';}
  function closeLightbox(){document.getElementById('lightbox').classList.remove('open');document.body.style.overflow='';}
  document.addEventListener('keydown',e=>{if(e.key==='Escape')closeLightbox();});
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