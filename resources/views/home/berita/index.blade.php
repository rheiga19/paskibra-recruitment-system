<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Berita — {{ config('app.name') }}</title>
  <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
@include('home.navbar')

<div style="background:linear-gradient(135deg,#cc0000 0%,#6b0000 100%);padding:100px 24px 60px;text-align:center;">
  <div style="font-size:11px;letter-spacing:3px;color:rgba(255,255,255,.6);text-transform:uppercase;margin-bottom:10px;">Paskibra Compreng</div>
  <h1 style="font-family:'Bebas Neue',sans-serif;font-size:clamp(2.5rem,7vw,4.5rem);letter-spacing:4px;color:#fff;margin:0 0 12px;">Berita & Informasi</h1>
  <p style="color:rgba(255,255,255,.75);font-size:15px;max-width:480px;margin:0 auto;">Informasi terkini seputar kegiatan dan rekrutmen Paskibra Kecamatan Compreng</p>
</div>

<div style="max-width:1100px;margin:0 auto;padding:48px 24px 80px;">

  {{-- Grid berita --}}
  <div class="news-grid" style="grid-template-columns:repeat(auto-fill,minmax(300px,1fr));">
    @forelse($berita as $i => $b)
    <a href="{{ route('berita.show', $b) }}" class="news-card {{ $i === 0 ? 'featured' : '' }}">
      <div class="news-img">
        @if($b->gambar)<img src="{{ asset('storage/'.$b->gambar) }}" alt="{{ $b->judul }}" style="width:100%;height:100%;object-fit:cover;">@else🏛️@endif
      </div>
      <div class="news-body">
        <div class="news-cat">Berita</div>
        <div class="news-title">{{ $b->judul }}</div>
        <div class="news-meta">{{ $b->created_at->translatedFormat('d F Y') }}</div>
      </div>
    </a>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:60px 0;color:var(--teks-redup,#888);">
      <div style="font-size:48px;margin-bottom:16px;">📰</div>
      <p>Belum ada berita yang dipublikasikan.</p>
    </div>
    @endforelse
  </div>

  {{-- Pagination --}}
  @if($berita->hasPages())
  <div style="margin-top:40px;display:flex;justify-content:center;">
    {{ $berita->links() }}
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