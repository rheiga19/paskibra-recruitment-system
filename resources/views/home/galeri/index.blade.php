<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Galeri — {{ config('app.name') }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/home.css') }}">
  <style>
    .gal-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 16px;
    }
    .gal-item {
      position: relative; border-radius: 14px; overflow: hidden;
      aspect-ratio: 4/3; background: var(--abu, #1a1a1a);
      cursor: pointer; transition: transform .25s;
    }
    .gal-item:hover { transform: scale(1.02); }
    .gal-item img { width:100%;height:100%;object-fit:cover;display:block; }
    .gal-overlay {
      position: absolute; inset: 0;
      background: linear-gradient(to top, rgba(0,0,0,.75) 0%, transparent 60%);
      opacity: 0; transition: opacity .25s;
      display: flex; align-items: flex-end; padding: 14px 16px;
    }
    .gal-item:hover .gal-overlay { opacity: 1; }
    .gal-overlay span { color: #fff; font-size: 13px; font-weight: 600; line-height: 1.3; }
  </style>
</head>
<body>
@include('home.navbar')

<div style="background:linear-gradient(135deg,#cc0000 0%,#6b0000 100%);padding:100px 24px 60px;text-align:center;">
  <div style="font-size:11px;letter-spacing:3px;color:rgba(255,255,255,.6);text-transform:uppercase;margin-bottom:10px;">Paskibra Compreng</div>
  <h1 style="font-family:'Bebas Neue',sans-serif;font-size:clamp(2.5rem,7vw,4.5rem);letter-spacing:4px;color:#fff;margin:0 0 12px;">Galeri Foto</h1>
  <p style="color:rgba(255,255,255,.75);font-size:15px;max-width:480px;margin:0 auto;">Dokumentasi momen bersejarah Paskibra Kecamatan Compreng</p>
</div>

<div style="max-width:1200px;margin:0 auto;padding:48px 24px 80px;">

  <div class="gal-grid">
    @forelse($galeri as $g)
    <a href="{{ route('galeri.show', $g) }}" class="gal-item">
      <img src="{{ asset('storage/'.$g->path) }}" alt="{{ $g->judul }}">
      <div class="gal-overlay"><span>{{ $g->judul }}</span></div>
    </a>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:60px 0;color:var(--teks-redup,#888);">
      <div style="font-size:48px;margin-bottom:16px;">📷</div>
      <p>Belum ada foto di galeri.</p>
    </div>
    @endforelse
  </div>

  @if($galeri->hasPages())
  <div style="margin-top:40px;display:flex;justify-content:center;">
    {{ $galeri->links() }}
  </div>
  @endif

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