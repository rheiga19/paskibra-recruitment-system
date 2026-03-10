<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name') }} — Pasukan Pengibar Bendera</title>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --merah: #CC0000;
      --merah-terang: #FF1A1A;
      --putih: #FFFFFF;
      --hitam: #0A0A0A;
      --abu: #1A1A1A;
      --abu-muda: #2A2A2A;
      --emas: #C8970A;
      --emas-terang: #FFD700;
      --teks-redup: #9CA3AF;
      --bg: #080808;
    }
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body { font-family: 'Outfit', sans-serif; background: var(--bg); color: var(--putih); overflow-x: hidden; }
    ::-webkit-scrollbar { width: 4px; }
    ::-webkit-scrollbar-track { background: var(--hitam); }
    ::-webkit-scrollbar-thumb { background: var(--merah); border-radius: 2px; }

    /* ══ NAVBAR ══ */
    nav {
      position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
      display: flex; align-items: center; justify-content: space-between;
      padding: 20px 60px;
      background: rgba(8,8,8,0.85); backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(204,0,0,0.15); transition: all .3s;
    }
    .nav-logo { display: flex; align-items: center; gap: 14px; text-decoration: none; }
    .nav-logo-emblem {
      width: 44px; height: 44px;
      background: linear-gradient(135deg, var(--merah), #800000);
      border-radius: 10px; display: flex; align-items: center; justify-content: center;
      font-family: 'Bebas Neue', sans-serif; font-size: 18px; color: white;
      box-shadow: 0 0 20px rgba(204,0,0,.4);
    }
    .nav-logo-text { font-family: 'Bebas Neue', sans-serif; font-size: 22px; letter-spacing: 2px; color: white; }
    .nav-logo-sub { font-size: 10px; color: var(--teks-redup); letter-spacing: 1px; text-transform: uppercase; }
    .nav-links { display: flex; align-items: center; gap: 36px; list-style: none; }
    .nav-links a {
      color: var(--teks-redup); text-decoration: none;
      font-size: 13px; font-weight: 500; letter-spacing: 1px; text-transform: uppercase;
      transition: color .2s; position: relative;
    }
    .nav-links a::after { content: ''; position: absolute; bottom: -4px; left: 0; width: 0; height: 1px; background: var(--merah); transition: width .3s; }
    .nav-links a:hover { color: white; }
    .nav-links a:hover::after { width: 100%; }
    .nav-actions { display: flex; gap: 12px; }
    .btn-outline {
      padding: 9px 20px; border: 1px solid rgba(255,255,255,.2);
      background: transparent; color: white; border-radius: 8px;
      font-family: 'Outfit', sans-serif; font-size: 13px; font-weight: 500;
      cursor: pointer; transition: all .2s; text-decoration: none; display: inline-block;
    }
    .btn-outline:hover { border-color: var(--merah); color: var(--merah); }
    .btn-primary-red {
      padding: 9px 22px; background: var(--merah); color: white; border: none; border-radius: 8px;
      font-family: 'Outfit', sans-serif; font-size: 13px; font-weight: 600;
      cursor: pointer; transition: all .2s; text-decoration: none; display: inline-block;
      box-shadow: 0 0 20px rgba(204,0,0,.3);
    }
    .btn-primary-red:hover { background: var(--merah-terang); transform: translateY(-1px); color: white; }
    .nav-hamburger { display: none; flex-direction: column; gap: 5px; cursor: pointer; background: none; border: none; padding: 4px; }
    .nav-hamburger span { display: block; width: 24px; height: 2px; background: white; transition: all .3s; }
    .nav-mobile {
      display: none; position: fixed; top: 80px; left: 0; right: 0;
      background: rgba(8,8,8,0.97); backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(204,0,0,0.2); padding: 20px; z-index: 999;
    }
    .nav-mobile.open { display: block; }
    .nav-mobile ul { list-style: none; display: flex; flex-direction: column; gap: 4px; margin-bottom: 16px; }
    .nav-mobile ul a { display: block; padding: 12px 16px; color: var(--teks-redup); text-decoration: none; font-size: 14px; font-weight: 500; border-radius: 8px; transition: all .2s; }
    .nav-mobile ul a:hover { color: white; background: rgba(204,0,0,.1); }
    .nav-mobile-actions { display: flex; flex-direction: column; gap: 8px; }
    .nav-mobile-actions a { text-align: center; }

    /* ══ HERO ══ */
    .hero { min-height: 100vh; position: relative; display: flex; align-items: center; overflow: hidden; }
    .hero-bg {
      position: absolute; inset: 0;
      background:
        radial-gradient(ellipse 80% 60% at 70% 50%, rgba(204,0,0,0.12) 0%, transparent 70%),
        radial-gradient(ellipse 40% 40% at 20% 80%, rgba(200,151,10,0.06) 0%, transparent 60%),
        linear-gradient(180deg, #080808 0%, #0f0404 100%);
    }
    .hero-pattern { position: absolute; right: -80px; top: 0; bottom: 0; width: 55%; clip-path: polygon(15% 0%, 100% 0%, 100% 100%, 0% 100%); overflow: hidden; }
    .hero-pattern-inner { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(204,0,0,0.08) 0%, rgba(204,0,0,0.02) 100%); }
    .hero-pattern::before {
      content: ''; position: absolute; inset: 0;
      background-image:
        repeating-linear-gradient(0deg, transparent, transparent 60px, rgba(204,0,0,0.04) 60px, rgba(204,0,0,0.04) 61px),
        repeating-linear-gradient(90deg, transparent, transparent 60px, rgba(204,0,0,0.04) 60px, rgba(204,0,0,0.04) 61px);
    }
    .hero-flag {
      position: absolute; right: 8%; top: 50%; transform: translateY(-50%);
      width: 320px; height: 213px; border-radius: 4px; overflow: hidden;
      box-shadow: 0 40px 120px rgba(0,0,0,.8), 0 0 0 1px rgba(255,255,255,.05);
      animation: flagWave 3s ease-in-out infinite alternate;
    }
    .flag-merah { height: 50%; background: var(--merah); }
    .flag-putih { height: 50%; background: white; }
    .flag-shine { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(255,255,255,.15) 0%, transparent 50%, rgba(0,0,0,.1) 100%); }
    @keyframes flagWave {
      0%   { transform: translateY(-50%) perspective(300px) rotateY(0deg); }
      100% { transform: translateY(-50%) perspective(300px) rotateY(2deg); }
    }
    .hero-garuda { position: absolute; right: 18%; top: 10%; font-size: 200px; opacity: 0.03; line-height: 1; user-select: none; }
    .hero-content { position: relative; z-index: 2; padding: 0 60px; max-width: 680px; animation: fadeUp .8s ease both; }
    @keyframes fadeUp { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }
    .hero-badge {
      display: inline-flex; align-items: center; gap: 8px;
      background: rgba(204,0,0,.12); border: 1px solid rgba(204,0,0,.3);
      color: #FF6B6B; padding: 6px 14px; border-radius: 100px;
      font-size: 11px; font-weight: 600; letter-spacing: 2px; text-transform: uppercase;
      margin-bottom: 28px; animation: fadeUp .8s .1s ease both;
    }
    .hero-badge .badge-dot { width: 6px; height: 6px; background: var(--merah); border-radius: 50%; animation: pulse 2s infinite; flex-shrink: 0; }
    @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.4)} }
    .hero-title { font-family: 'Bebas Neue', sans-serif; font-size: clamp(52px, 7vw, 90px); line-height: .95; letter-spacing: 2px; margin-bottom: 10px; animation: fadeUp .8s .2s ease both; }
    .hero-title .accent { color: var(--merah); }
    .hero-title .gold { color: var(--emas-terang); }
    .hero-subtitle { font-size: 15px; color: var(--teks-redup); line-height: 1.7; margin-bottom: 36px; max-width: 480px; animation: fadeUp .8s .3s ease both; }
    .hero-cta { display: flex; gap: 14px; flex-wrap: wrap; animation: fadeUp .8s .4s ease both; }
    .btn-hero-primary {
      display: inline-flex; align-items: center; gap: 10px;
      padding: 16px 32px; background: var(--merah); color: white;
      border-radius: 10px; font-weight: 700; font-size: 15px;
      text-decoration: none; border: none; cursor: pointer; transition: all .2s;
      box-shadow: 0 8px 40px rgba(204,0,0,.35);
    }
    .btn-hero-primary:hover { background: var(--merah-terang); transform: translateY(-2px); box-shadow: 0 12px 50px rgba(204,0,0,.5); color: white; }
    .btn-hero-primary svg { transition: transform .2s; }
    .btn-hero-primary:hover svg { transform: translateX(4px); }
    .btn-hero-secondary {
      display: inline-flex; align-items: center; gap: 10px;
      padding: 16px 28px; background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.12);
      color: white; border-radius: 10px; font-weight: 500; font-size: 15px;
      text-decoration: none; cursor: pointer; transition: all .2s;
    }
    .btn-hero-secondary:hover { background: rgba(255,255,255,.1); border-color: rgba(255,255,255,.25); color: white; }
    .hero-stats { display: flex; gap: 40px; margin-top: 60px; animation: fadeUp .8s .5s ease both; }
    .stat-num { font-family: 'Bebas Neue', sans-serif; font-size: 38px; line-height: 1; color: white; }
    .stat-num .stat-accent { color: var(--merah); }
    .stat-label { font-size: 11px; color: var(--teks-redup); letter-spacing: 1px; text-transform: uppercase; margin-top: 2px; }
    .hero-scroll { position: absolute; bottom: 40px; left: 60px; display: flex; align-items: center; gap: 10px; font-size: 11px; color: var(--teks-redup); letter-spacing: 2px; text-transform: uppercase; }
    .scroll-line { width: 40px; height: 1px; background: var(--teks-redup); animation: scrollLine 2s ease-in-out infinite; }
    @keyframes scrollLine { 0%,100%{width:40px;opacity:.5} 50%{width:60px;opacity:1} }

    /* ══ MARQUEE ══ */
    .marquee-section { background: var(--merah); padding: 14px 0; overflow: hidden; border-top: 1px solid rgba(255,255,255,.1); border-bottom: 1px solid rgba(0,0,0,.2); }
    .marquee-track { display: flex; gap: 60px; animation: marquee 20s linear infinite; white-space: nowrap; }
    @keyframes marquee { from{transform:translateX(0)} to{transform:translateX(-50%)} }
    .marquee-item { display: flex; align-items: center; gap: 10px; font-family: 'Bebas Neue', sans-serif; font-size: 16px; letter-spacing: 3px; color: rgba(255,255,255,.9); flex-shrink: 0; }
    .marquee-dot { width: 5px; height: 5px; background: rgba(255,255,255,.5); border-radius: 50%; }

    /* ══ REKRUTMEN BAR ══ */
    .rekrutmen-bar { background: rgba(204,0,0,.08); border-top: 1px solid rgba(204,0,0,.2); border-bottom: 1px solid rgba(204,0,0,.2); padding: 14px 60px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .rekrutmen-bar-left { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .badge-buka { display: inline-flex; align-items: center; gap: 6px; background: rgba(204,0,0,.2); border: 1px solid rgba(204,0,0,.4); color: #FF6B6B; padding: 4px 12px; border-radius: 100px; font-size: 11px; font-weight: 700; letter-spacing: 1px; }
    .badge-buka-dot { width: 6px; height: 6px; background: var(--merah); border-radius: 50%; animation: pulse 2s infinite; }

    /* ══ SECTION BASE ══ */
    .section { padding: 100px 60px; }
    .section-label { display: inline-flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 600; letter-spacing: 3px; text-transform: uppercase; color: var(--merah); margin-bottom: 16px; }
    .section-label::before { content: ''; width: 20px; height: 2px; background: var(--merah); }
    .section-title { font-family: 'Bebas Neue', sans-serif; font-size: clamp(36px, 4.5vw, 56px); line-height: 1.05; letter-spacing: 1px; margin-bottom: 20px; }
    .section-desc { font-size: 15px; color: var(--teks-redup); line-height: 1.75; max-width: 520px; }

    /* ══ ABOUT ══ */
    .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
    .about-visual { position: relative; }
    .about-card-main { background: var(--abu); border-radius: 20px; overflow: hidden; aspect-ratio: 4/3; position: relative; border: 1px solid rgba(255,255,255,.06); }
    .about-card-main-bg { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(204,0,0,.15) 0%, transparent 60%); }
    .about-card-main-content { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; }
    .about-icon { font-size: 80px; opacity: .15; }
    .about-card-badge { position: absolute; bottom: 20px; left: 20px; right: 20px; background: rgba(8,8,8,.8); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,.08); border-radius: 12px; padding: 16px; display: flex; align-items: center; gap: 14px; }
    .badge-icon-wrap { width: 40px; height: 40px; background: var(--merah); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
    .badge-text-title { font-weight: 700; font-size: 14px; }
    .badge-text-sub { font-size: 11px; color: var(--teks-redup); }
    .about-float-card { position: absolute; top: -20px; right: -20px; background: var(--abu-muda); border: 1px solid rgba(255,255,255,.08); border-radius: 16px; padding: 16px 20px; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,.5); }
    .float-num { font-family: 'Bebas Neue', sans-serif; font-size: 36px; color: var(--emas-terang); line-height: 1; }
    .float-label { font-size: 10px; color: var(--teks-redup); letter-spacing: 1px; text-transform: uppercase; }
    .about-features { margin-top: 36px; display: flex; flex-direction: column; gap: 16px; }
    .feature-row { display: flex; align-items: flex-start; gap: 14px; padding: 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,.05); background: rgba(255,255,255,.02); transition: all .2s; }
    .feature-row:hover { background: rgba(204,0,0,.06); border-color: rgba(204,0,0,.2); }
    .feature-icon { width: 36px; height: 36px; background: rgba(204,0,0,.15); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
    .feature-title { font-weight: 600; font-size: 14px; margin-bottom: 2px; }
    .feature-desc { font-size: 12px; color: var(--teks-redup); line-height: 1.5; }

    /* ══ TIMELINE ══ */
    .daftar-section { background: rgba(255,255,255,.02); border-top: 1px solid rgba(255,255,255,.05); border-bottom: 1px solid rgba(255,255,255,.05); }
    .daftar-header { text-align: center; margin-bottom: 60px; }
    .daftar-header .section-label { justify-content: center; }
    .daftar-header .section-desc { margin: 0 auto; }
    .timeline { display: flex; position: relative; max-width: 900px; margin: 0 auto; }
    .timeline::before { content: ''; position: absolute; top: 28px; left: 28px; right: 28px; height: 2px; background: rgba(255,255,255,.08); z-index: 0; }
    .timeline-progress { position: absolute; top: 28px; left: 28px; height: 2px; background: var(--merah); width: 20%; z-index: 1; box-shadow: 0 0 10px rgba(204,0,0,.5); }
    .tl-step { flex: 1; text-align: center; position: relative; z-index: 2; }
    .tl-dot { width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 18px; font-weight: 700; border: 2px solid rgba(255,255,255,.1); background: var(--abu); transition: all .3s; }
    .tl-dot.active { background: var(--merah); border-color: var(--merah); box-shadow: 0 0 20px rgba(204,0,0,.5); }
    .tl-dot.done { background: var(--abu-muda); border-color: rgba(200,151,10,.4); color: var(--emas-terang); }
    .tl-date { font-size: 11px; color: var(--merah); font-weight: 600; letter-spacing: 1px; margin-bottom: 6px; text-transform: uppercase; }
    .tl-title { font-weight: 700; font-size: 14px; margin-bottom: 4px; }
    .tl-desc { font-size: 12px; color: var(--teks-redup); line-height: 1.5; }

    /* ══ SYARAT ══ */
    .req-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 50px; }
    .req-card { background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.07); border-radius: 16px; padding: 28px; transition: all .3s; position: relative; overflow: hidden; }
    .req-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, var(--merah), transparent); opacity: 0; transition: opacity .3s; }
    .req-card:hover { border-color: rgba(204,0,0,.25); background: rgba(204,0,0,.04); transform: translateY(-3px); }
    .req-card:hover::before { opacity: 1; }
    .req-card-icon { width: 48px; height: 48px; background: rgba(204,0,0,.12); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 16px; }
    .req-card-title { font-weight: 700; font-size: 16px; margin-bottom: 10px; }
    .req-card ul { list-style: none; display: flex; flex-direction: column; gap: 8px; }
    .req-card li { font-size: 13px; color: var(--teks-redup); display: flex; align-items: flex-start; gap: 8px; line-height: 1.5; }
    .req-card li::before { content: '▸'; color: var(--merah); flex-shrink: 0; font-size: 10px; margin-top: 3px; }

    /* ══ BERITA ══ */
    .news-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 20px; margin-top: 50px; }
    .news-card { background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.07); border-radius: 16px; overflow: hidden; cursor: pointer; transition: all .3s; text-decoration: none; color: var(--putih); display: block; }
    .news-card:hover { transform: translateY(-5px); border-color: rgba(204,0,0,.2); color: var(--putih); }
    .news-img { aspect-ratio: 16/9; overflow: hidden; position: relative; background: linear-gradient(135deg, rgba(204,0,0,.2), rgba(0,0,0,.5)); display: flex; align-items: center; justify-content: center; font-size: 60px; opacity: .3; }
    .news-img img { width: 100%; height: 100%; object-fit: cover; opacity: 1; }
    .news-card.featured .news-img { font-size: 100px; }
    .news-body { padding: 20px; }
    .news-cat { font-size: 10px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--merah); margin-bottom: 8px; }
    .news-title { font-weight: 700; font-size: 15px; line-height: 1.4; margin-bottom: 8px; }
    .news-card.featured .news-title { font-size: 20px; }
    .news-meta { font-size: 11px; color: var(--teks-redup); }

    /* ══ GALERI ══ */
    .gallery-grid { display: grid; grid-template-columns: repeat(4, 1fr); grid-template-rows: 200px 200px; gap: 12px; margin-top: 50px; }
    .gallery-item { border-radius: 12px; overflow: hidden; cursor: pointer; background: var(--abu); position: relative; transition: transform .3s; display: flex; align-items: center; justify-content: center; font-size: 60px; opacity: .2; }
    .gallery-item img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 1; }
    .gallery-item:hover { transform: scale(1.03); opacity: .9; }
    .gallery-item:nth-child(1) { grid-column: 1 / 3; background: rgba(204,0,0,.15); }
    .gallery-item:nth-child(4) { grid-row: 1 / 3; grid-column: 4; background: rgba(200,151,10,.1); }
    .gallery-item::after { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(204,0,0,.1), transparent); }

    /* ══ FAQ ══ */
    .faq-list { margin-top: 50px; max-width: 700px; margin-left: auto; margin-right: auto; display: flex; flex-direction: column; gap: 12px; }
    .faq-item { background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.07); border-radius: 14px; overflow: hidden; }
    .faq-q { padding: 20px 24px; font-weight: 600; font-size: 15px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: background .2s; }
    .faq-q:hover { background: rgba(204,0,0,.05); }
    .faq-toggle { color: var(--merah); font-size: 20px; transition: transform .3s; flex-shrink: 0; }
    .faq-item.open .faq-toggle { transform: rotate(45deg); }
    .faq-a { padding: 0 24px 20px; font-size: 14px; color: var(--teks-redup); line-height: 1.7; display: none; }
    .faq-item.open .faq-a { display: block; }

    /* ══ CTA ══ */
    .cta-banner { margin: 0 60px 100px; background: linear-gradient(135deg, rgba(204,0,0,.15) 0%, rgba(204,0,0,.05) 100%); border: 1px solid rgba(204,0,0,.25); border-radius: 24px; padding: 60px; text-align: center; position: relative; overflow: hidden; }
    .cta-banner::before { content: ''; position: absolute; top: -100px; left: 50%; transform: translateX(-50%); width: 400px; height: 400px; background: radial-gradient(circle, rgba(204,0,0,.15) 0%, transparent 70%); }
    .cta-banner h2 { font-family: 'Bebas Neue', sans-serif; font-size: clamp(36px, 5vw, 60px); letter-spacing: 2px; margin-bottom: 16px; }
    .cta-banner p { color: var(--teks-redup); max-width: 500px; margin: 0 auto 36px; font-size: 15px; line-height: 1.7; }
    .cta-actions { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }

    /* ══ FOOTER ══ */
    footer { background: rgba(255,255,255,.02); border-top: 1px solid rgba(255,255,255,.06); padding: 60px 60px 30px; }
    .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 40px; margin-bottom: 50px; }
    .footer-brand p { font-size: 13px; color: var(--teks-redup); line-height: 1.7; max-width: 260px; margin-top: 12px; }
    .footer-social { display: flex; gap: 10px; margin-top: 20px; }
    .social-btn { width: 36px; height: 36px; background: rgba(255,255,255,.07); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; text-decoration: none; color: white; transition: all .2s; }
    .social-btn:hover { background: var(--merah); }
    .footer-col h4 { font-weight: 700; font-size: 13px; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 18px; color: rgba(255,255,255,.9); }
    .footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 10px; }
    .footer-col li a { font-size: 13px; color: var(--teks-redup); text-decoration: none; transition: color .2s; }
    .footer-col li a:hover { color: white; }
    .footer-bottom { border-top: 1px solid rgba(255,255,255,.06); padding-top: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; }
    .footer-bottom p { font-size: 12px; color: var(--teks-redup); }
    .footer-bottom .built span { color: var(--merah); }

    /* ══ RESPONSIVE ══ */
    @media (max-width: 900px) {
      nav { padding: 16px 20px; }
      .nav-links, .nav-actions { display: none; }
      .nav-hamburger { display: flex; }
      .hero-content { padding: 0 24px; margin-top: 80px; }
      .hero-flag, .hero-pattern, .hero-garuda { display: none; }
      .hero-stats { gap: 24px; flex-wrap: wrap; }
      .hero-scroll { left: 24px; }
      .section { padding: 60px 24px; }
      .rekrutmen-bar { padding: 14px 24px; }
      .about-grid { grid-template-columns: 1fr; gap: 40px; }
      .about-float-card { display: none; }
      .req-grid { grid-template-columns: 1fr; }
      .news-grid { grid-template-columns: 1fr; }
      .timeline { flex-direction: column; gap: 30px; }
      .timeline::before, .timeline-progress { display: none; }
      .gallery-grid { grid-template-columns: 1fr 1fr; grid-template-rows: unset; }
      .gallery-item:nth-child(1) { grid-column: 1; }
      .gallery-item:nth-child(4) { grid-row: unset; grid-column: unset; }
      .footer-grid { grid-template-columns: 1fr 1fr; gap: 30px; }
      .cta-banner { margin: 0 20px 60px; padding: 40px 24px; }
      footer { padding: 40px 24px 24px; }
    }
    @media (max-width: 480px) {
      .hero-title { font-size: 46px; }
      .footer-grid { grid-template-columns: 1fr; }
      .hero-stats { gap: 20px; }
      .hero-cta { flex-direction: column; }
      .cta-actions { flex-direction: column; align-items: center; }
    }
  </style>
</head>
<body>

{{-- ── NAVBAR (komponen terpisah) ── --}}
@include('home.navbar')

{{-- ── HERO ── --}}
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
      <div class="stat-item">
        <div class="stat-num">16<span class="stat-accent">+</span></div>
        <div class="stat-label">Kuota Peserta</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">3</div>
        <div class="stat-label">Tahap Seleksi</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">{{ date('Y') }}</div>
        <div class="stat-label">Angkatan</div>
      </div>
    </div>
  </div>
  <div class="hero-scroll"><div class="scroll-line"></div>Scroll untuk lebih</div>
</section>

{{-- ── MARQUEE ── --}}
<div class="marquee-section">
  <div class="marquee-track">
    @php $items = ['MERAH PUTIH','KEHORMATAN BANGSA','SELEKSI '.date('Y'),'PENGABDIAN TULUS','INDONESIA MERDEKA','KECAMATAN COMPRENG','UPACARA KENEGARAAN']; @endphp
    @foreach(array_merge($items,$items) as $item)
      <div class="marquee-item"><div class="marquee-dot"></div>{{ $item }}</div>
    @endforeach
  </div>
</div>

{{-- ── STATUS REKRUTMEN ── --}}
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

{{-- ── TENTANG ── --}}
<section class="section" id="tentang">
  <div class="about-grid">
    <div class="about-visual">
      <div class="about-card-main">
        <div class="about-card-main-bg"></div>
        <div class="about-card-main-content"><div class="about-icon">🏛️</div></div>
        <div class="about-card-badge">
          <div class="badge-icon-wrap">🎖️</div>
          <div>
            <div class="badge-text-title">Program Resmi Kecamatan</div>
            <div class="badge-text-sub">Kecamatan Compreng, Subang</div>
          </div>
        </div>
      </div>
      <div class="about-float-card">
        <div class="float-num">17</div>
        <div class="float-label">Agustus Setiap Tahun</div>
      </div>
    </div>
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

{{-- ── ALUR PENDAFTARAN ── --}}
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

{{-- ── SYARAT ── --}}
<section class="section" id="syarat">
  <div class="section-label">Persyaratan</div>
  <h2 class="section-title">Syarat & Ketentuan Pendaftar</h2>
  <div class="req-grid">
    <div class="req-card"><div class="req-card-icon">👤</div><div class="req-card-title">Persyaratan Umum</div><ul><li>Warga Negara Indonesia</li><li>Berdomisili di Kecamatan Compreng</li><li>Siswa aktif SMP/MTs/SMA/MA/SMK</li><li>Berbadan sehat jasmani & rohani</li><li>Belum pernah menjadi anggota Paskibra</li></ul></div>
    <div class="req-card"><div class="req-card-icon">📄</div><div class="req-card-title">Dokumen Wajib</div><ul><li>KTP Pelajar / Kartu Pelajar</li><li>Akta Kelahiran</li><li>Rapor semester terakhir</li><li>Surat keterangan sehat dari dokter</li><li>Pas foto terbaru 4×6</li><li>Surat izin orang tua / wali</li></ul></div>
    <div class="req-card"><div class="req-card-icon">🏃</div><div class="req-card-title">Kriteria Fisik</div><ul><li>Tinggi badan min. 163 cm (putra)</li><li>Tinggi badan min. 155 cm (putri)</li><li>Lulus tes kesehatan dasar</li><li>Kemampuan baris-berbaris dasar</li><li>Bebas narkoba dan tidak merokok</li></ul></div>
  </div>
</section>

{{-- ── BERITA ── --}}
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

{{-- ── GALERI ── --}}
<section class="section" id="galeri">
  <div class="section-label">Galeri</div>
  <h2 class="section-title">Momen Bersejarah</h2>
  <div class="gallery-grid">
    @forelse($galeri as $g)
    <a href="{{ route('galeri.index') }}" class="gallery-item"><img src="{{ asset('storage/'.$g->foto) }}" alt="{{ $g->judul }}"></a>
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

{{-- ── FAQ ── --}}
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

{{-- ── CTA BANNER ── --}}
<div class="cta-banner">
  <h2>SIAP MENGHARUMKAN NAMA COMPRENG?</h2>
  <p>Jadilah bagian dari generasi penerus kebanggaan Kecamatan Compreng. Daftarkan diri sekarang sebelum pendaftaran ditutup.</p>
  <div class="cta-actions">
    @auth
      <a href="#" class="btn-hero-primary">Daftar Sekarang <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
    @else
      <a href="{{ route('register') }}" class="btn-hero-primary">Daftar Sekarang <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
    @endauth
    <a href="#" class="btn-hero-secondary">Lihat Pengumuman</a>
  </div>
</div>

{{-- ── FOOTER (komponen terpisah) ── --}}
@include('home.footer')

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
    entries.forEach(e => { if (e.isIntersecting) { e.target.style.opacity='1'; e.target.style.transform='translateY(0)'; } });
  }, { threshold: 0.1 });
  document.querySelectorAll('.req-card, .news-card, .faq-item, .feature-row, .tl-step').forEach(el => {
    el.style.opacity = '0'; el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    observer.observe(el);
  });
</script>
</body>
</html>