<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFinance — Keuangan Instansi, Lebih Cerdas</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600&family=Outfit:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollToPlugin.min.js"></script>

    <style>
        /* ══════════════════════════════════════════════
           RESET & DESIGN TOKENS
        ══════════════════════════════════════════════ */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --white:     #ffffff;
            --offwhite:  #f7f8fc;
            --light:     #eef1f8;
            --blue:      #1246a0;
            --blue-mid:  #1a5bc4;
            --blue-lt:   #3b82f6;
            --blue-pale: #dde9ff;
            --ink:       #0d1b35;
            --muted:     #6b7a99;
            --border:    rgba(18,70,160,.10);
            --border-lt: rgba(18,70,160,.06);

            --f-display: 'Cormorant Garamond', Georgia, serif;
            --f-body:    'Outfit', system-ui, sans-serif;
            --f-mono:    'DM Mono', monospace;

            --ease-out:    cubic-bezier(.16,1,.3,1);
            --ease-in-out: cubic-bezier(.45,0,.55,1);
            --ease-spring: cubic-bezier(.34,1.56,.64,1);
        }

        html {
            scroll-behavior: auto;
            overflow-x: hidden;
            font-size: clamp(12px, 2vw, 16px);
        }
        body {
            font-family: var(--f-body);
            background: var(--white);
            color: var(--ink);
            line-height: 1.6;
            overflow-x: hidden;
            cursor: none;
        }
        @media (max-width: 768px) {
            body { font-size: 14px; }
        }
        @media (max-width: 480px) {
            body { font-size: 13px; }
        }

        /* ── CURSOR ──────────────────────────────────────────────── */
        .cursor {
            position: fixed; width: 8px; height: 8px;
            background: var(--blue); border-radius: 50%;
            pointer-events: none; z-index: 99999;
            transform: translate(-50%,-50%);
            transition: width .3s var(--ease-out), height .3s var(--ease-out);
            mix-blend-mode: multiply;
        }
        .cursor-ring {
            position: fixed; width: 32px; height: 32px;
            border: 1.5px solid var(--blue); border-radius: 50%;
            pointer-events: none; z-index: 99998;
            transform: translate(-50%,-50%);
            transition: width .4s var(--ease-out), height .4s var(--ease-out), opacity .3s;
            opacity: .5;
        }
        .cursor.hovered      { width: 16px; height: 16px; }
        .cursor-ring.hovered { width: 54px; height: 54px; opacity: .25; }
        a, button { cursor: none; }

        /* ── LOADER ──────────────────────────────────────────────── */
        #loader {
            position: fixed; inset: 0; background: var(--blue);
            z-index: 90000; display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 32px; overflow: hidden;
        }
        .loader-icon {
            width: 72px; height: 72px; opacity: 0; transform: scale(.7);
            border-radius: 50%; background: transparent;
            border: none;
            display: flex; align-items: center; justify-content: center; overflow: hidden;
        }
        .loader-icon img { width: 64px; height: 64px; object-fit: contain; border-radius: 50%; filter: brightness(0) invert(1); }
        .loader-logo {
            font-family: var(--f-display); font-size: clamp(32px,6vw,64px);
            font-weight: 600; color: white; letter-spacing: -.02em;
            line-height: 1; opacity: 0; transform: translateY(24px);
        }
        .loader-sub {
            font-family: var(--f-mono); font-size: 11px; letter-spacing: .28em;
            color: rgba(255,255,255,.45); text-transform: uppercase; opacity: 0;
        }
        .loader-progress-wrap { width: min(240px,55vw); display: flex; flex-direction: column; gap: 10px; align-items: center; }
        .loader-bar-track { width: 100%; height: 1px; background: rgba(255,255,255,.15); position: relative; overflow: hidden; }
        .loader-bar { position: absolute; left: 0; top: 0; height: 100%; width: 0; background: rgba(255,255,255,.7); transition: width .08s linear; }
        .loader-pct { font-family: var(--f-mono); font-size: 10px; letter-spacing: .2em; color: rgba(255,255,255,.3); opacity: 0; }
        .loader-curtain { position: absolute; inset: 0; background: var(--white); transform: scaleY(0); transform-origin: bottom; pointer-events: none; }

        /* ── NAV ─────────────────────────────────────────────────── */
        nav {
            position: fixed; top: 16px; left: 50%; transform: translateX(-50%);
            z-index: 8000; width: calc(100% - 80px); max-width: 1100px;
            height: 58px;
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.16);
            backdrop-filter: blur(20px) saturate(1.6);
            border-radius: 100px;
            padding: 0 8px 0 20px;
            transition: background .4s, border-color .4s, box-shadow .4s, top .4s, width .4s;
            box-shadow: 0 4px 32px rgba(0,0,0,.15);
        }
        nav.scrolled {
            background: rgba(255,255,255,.94);
            border-color: rgba(18,70,160,.12);
            box-shadow: 0 4px 40px rgba(18,70,160,.10);
            top: 12px;
        }
        .nav-logo { display: flex; align-items: center; gap: 8px; text-decoration: none; flex-shrink: 0; }
        .nav-logo-img { height: 26px; width: auto; filter: brightness(0) invert(1); transition: filter .4s; }
        nav.scrolled .nav-logo-img { filter: brightness(0); }
        .nav-logo-text {
            font-family: var(--f-display); font-size: 17px; font-weight: 700;
            color: white; letter-spacing: -.02em; transition: color .4s;
        }
        nav.scrolled .nav-logo-text { color: var(--ink); }

        /* nav links container — sits inside the pill nav itself */
        .nav-links {
            display: flex; align-items: center; gap: 2px;
        }

        /* individual nav links */
        .nav-links a {
            font-size: 13px; color: rgba(255,255,255,.72); text-decoration: none;
            letter-spacing: .01em; padding: 7px 13px; border-radius: 100px;
            transition: color .25s, background .25s;
            white-space: nowrap;
        }
        .nav-links a:hover { color: white; background: rgba(255,255,255,.12); }
        nav.scrolled .nav-links a { color: var(--muted); }
        nav.scrolled .nav-links a:hover { color: var(--ink); background: rgba(18,70,160,.07); }

        /* WA link in nav */
        .nav-wa {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 13px; color: rgba(255,255,255,.72); text-decoration: none;
            padding: 7px 13px; border-radius: 100px;
            letter-spacing: .01em;
            transition: color .25s, background .25s;
        }
        .nav-wa:hover { color: white; background: rgba(255,255,255,.12); }
        nav.scrolled .nav-wa { color: var(--muted); }
        nav.scrolled .nav-wa:hover { color: var(--ink); background: rgba(18,70,160,.07); }
        .nav-wa svg { flex-shrink: 0; }

        /* nav divider */
        .nav-divider {
            width: 1px; height: 18px;
            background: rgba(255,255,255,.18);
            margin: 0 4px; flex-shrink: 0;
            transition: background .4s;
        }
        nav.scrolled .nav-divider { background: rgba(18,70,160,.15); }

        /* CTA pill button */
        .nav-cta {
            display: inline-flex !important; align-items: center; gap: 7px;
            background: white !important; color: var(--blue) !important;
            padding: 9px 22px !important; border-radius: 100px !important;
            font-weight: 600 !important; font-size: 13px !important;
            letter-spacing: .01em;
            box-shadow: 0 2px 14px rgba(0,0,0,.15);
            transition: background .3s, transform .3s var(--ease-out), box-shadow .3s !important;
        }
        .nav-cta::after {
            content: '→'; font-size: 12px;
            transition: transform .3s var(--ease-out);
            display: inline-block;
        }
        .nav-cta:hover {
            background: var(--blue-pale) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 20px rgba(18,70,160,.22) !important;
        }
        .nav-cta:hover::after { transform: translateX(3px); }
        nav.scrolled .nav-cta {
            background: var(--blue) !important; color: white !important;
            box-shadow: 0 4px 16px rgba(18,70,160,.3) !important;
        }
        nav.scrolled .nav-cta:hover { background: var(--blue-mid) !important; }

        /* ── MOBILE MENU TOGGLE ─────────────────────────────────── */
        .nav-toggle {
            display: none; flex-direction: column; gap: 5px; cursor: pointer;
            background: none; border: none; padding: 8px;
            margin-right: 8px;
        }
        .nav-toggle span {
            width: 22px; height: 2px; background: white;
            border-radius: 2px; transition: all .3s ease;
        }
        nav.scrolled .nav-toggle span { background: var(--ink); }
        .nav-toggle.active span:nth-child(1) { transform: rotate(45deg) translate(8px, 8px); }
        .nav-toggle.active span:nth-child(2) { opacity: 0; }
        .nav-toggle.active span:nth-child(3) { transform: rotate(-45deg) translate(7px, -7px); }

        /* ── MOBILE MENU PANEL ──────────────────────────────────── */
        .nav-mobile {
            display: none; position: fixed; top: 70px; left: 0; right: 0;
            z-index: 7999; background: rgba(255,255,255,.95);
            backdrop-filter: blur(10px);
            flex-direction: column; gap: 0;
            border-bottom: 1px solid rgba(18,70,160,.1);
            max-height: calc(100vh - 70px);
            overflow-y: auto;
        }
        .nav-mobile.active { display: flex; }
        .nav-mobile a {
            padding: 16px 24px; border-bottom: 1px solid rgba(18,70,160,.08);
            font-size: 15px; color: var(--ink); text-decoration: none;
            transition: background .2s;
        }
        .nav-mobile a:hover { background: rgba(18,70,160,.05); }
        .nav-mobile-cta {
            padding: 12px 24px !important; margin: 12px 24px !important;
            background: var(--blue) !important; color: white !important;
            border-radius: 8px !important; text-align: center;
        }

        /* ── HERO ────────────────────────────────────────────────── */
        .hero {
            height: 100vh; min-height: 720px; position: relative;
            display: flex; flex-direction: column; justify-content: flex-end;
            padding: 0 52px 80px; overflow: hidden; background: var(--ink);
        }
        .hero-bg {
            position: absolute; inset: 0; pointer-events: none;
            background:
                radial-gradient(ellipse 80% 60% at 70% 20%, rgba(18,70,160,.6) 0%, transparent 60%),
                radial-gradient(ellipse 50% 50% at 20% 80%, rgba(59,130,246,.25) 0%, transparent 50%),
                linear-gradient(160deg, #0d1b35 0%, #0a1528 100%);
        }
        .hero-grid {
            position: absolute; inset: 0; pointer-events: none;
            background-image: linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);
            background-size: 80px 80px;
        }
        .hero-orb { position: absolute; width: 700px; height: 700px; right: -150px; top: -200px; border-radius: 50%; background: radial-gradient(circle,rgba(59,130,246,.18) 0%,transparent 70%); pointer-events: none; will-change: transform; }
        .hero-badge {
            position: absolute; top: 104px; left: 52px;
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.12);
            backdrop-filter: blur(12px); padding: 6px 14px; border-radius: 100px;
            font-family: var(--f-mono); font-size: 10.5px; letter-spacing: .15em;
            color: rgba(255,255,255,.6); text-transform: uppercase;
            opacity: 0; transform: translateY(-12px);
        }
        .hero-badge-dot { width: 6px; height: 6px; background: #4ade80; border-radius: 50%; box-shadow: 0 0 8px #4ade80; flex-shrink: 0; }
        .hero-floating-num { position: absolute; top: 100px; right: 52px; text-align: right; opacity: 0; transform: translateY(-12px); }
        .hero-floating-n { font-family: var(--f-display); font-size: 88px; font-weight: 600; color: rgba(255,255,255,.05); line-height: 1; letter-spacing: -.04em; }
        .hero-floating-l { font-family: var(--f-mono); font-size: 10px; letter-spacing: .2em; color: rgba(255,255,255,.2); text-transform: uppercase; }
        .hero-main { position: relative; z-index: 2; }
        .hero-h1 { font-family: var(--f-display); font-size: clamp(56px,9.5vw,136px); font-weight: 600; color: white; line-height: .92; letter-spacing: -.03em; margin-bottom: 48px; }
        .hero-h1 em { font-style: italic; font-weight: 300; background: linear-gradient(135deg,#93c5fd 0%,#60a5fa 40%,#3b82f6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .hero-h1-line  { display: block; overflow: hidden; }
        .hero-h1-inner { display: block; transform: translateY(110%); }
        .hero-bottom { display: flex; align-items: flex-end; justify-content: space-between; gap: 40px; opacity: 0; }
        .hero-desc { max-width: 440px; font-size: 16px; color: rgba(255,255,255,.45); line-height: 1.85; font-weight: 300; }
        .hero-actions { display: flex; align-items: center; gap: 20px; flex-shrink: 0; }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 10px;
            background: white; color: var(--blue); padding: 14px 28px;
            text-decoration: none; font-size: 14px; font-weight: 600;
            letter-spacing: .01em; border-radius: 8px;
            transition: background .3s, transform .3s var(--ease-out), box-shadow .3s;
            box-shadow: 0 4px 20px rgba(0,0,0,.25);
        }
        .btn-primary:hover { background: var(--blue-pale); transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,.3); }
        .btn-primary svg         { transition: transform .3s var(--ease-out); }
        .btn-primary:hover svg   { transform: translateX(3px); }
        .btn-ghost-hero { font-size: 14px; color: rgba(255,255,255,.5); text-decoration: none; padding-bottom: 2px; border-bottom: 1px solid rgba(255,255,255,.2); transition: color .3s, border-color .3s; }
        .btn-ghost-hero:hover { color: white; border-color: rgba(255,255,255,.6); }
        .hero-scroll { position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%); display: flex; flex-direction: column; align-items: center; gap: 8px; opacity: 0; z-index: 3; }
        .hero-scroll span { font-family: var(--f-mono); font-size: 9.5px; letter-spacing: .22em; color: rgba(255,255,255,.25); text-transform: uppercase; }
        .scroll-line { width: 1px; height: 44px; background: linear-gradient(180deg,rgba(255,255,255,.3),transparent); animation: scrollPulse 2.2s ease-in-out infinite; }
        @keyframes scrollPulse {
            0%  { transform: scaleY(0); transform-origin: top;    opacity: 1; }
            45% { transform: scaleY(1); transform-origin: top;    opacity: 1; }
            46% {                        transform-origin: bottom; }
            90% { transform: scaleY(0); transform-origin: bottom; opacity: 1; }
            100%{ transform: scaleY(0); transform-origin: bottom; opacity: 0; }
        }

        /* ── STATS BAR ───────────────────────────────────────────── */
        .stats-bar { background: var(--blue); display: flex; align-items: stretch; }
        .stat-block { flex: 1; padding: 36px 52px; display: flex; flex-direction: column; gap: 6px; border-right: 1px solid rgba(255,255,255,.1); opacity: 0; transform: translateY(24px); }
        .stat-block:last-child { border-right: none; }
        .stat-n { font-family: var(--f-display); font-size: clamp(36px,4.5vw,60px); font-weight: 600; color: white; line-height: 1; letter-spacing: -.03em; }
        .stat-n sup { font-size: .45em; vertical-align: super; font-weight: 400; }
        .stat-l { font-family: var(--f-mono); font-size: 10.5px; letter-spacing: .18em; color: rgba(255,255,255,.45); text-transform: uppercase; }

        /* ── MARQUEE ─────────────────────────────────────────────── */
        .marquee-section { background: var(--blue-pale); padding: 14px 0; overflow: hidden; display: flex; border-top: 1px solid rgba(18,70,160,.1); border-bottom: 1px solid rgba(18,70,160,.1); }
        .marquee-track { display: flex; animation: marqueeFwd 22s linear infinite; white-space: nowrap; flex-shrink: 0; will-change: transform; }
        @keyframes marqueeFwd { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        .marquee-item { font-family: var(--f-mono); font-size: 11px; letter-spacing: .14em; text-transform: uppercase; color: var(--blue); opacity: .6; flex-shrink: 0; padding: 0 32px; }
        .marquee-sep  { color: var(--blue); opacity: .25; flex-shrink: 0; padding-right: 32px; }

        /* ── SECTION SHARED ──────────────────────────────────────── */
        section { position: relative; }
        .section-tag { display: inline-flex; align-items: center; gap: 10px; font-family: var(--f-mono); font-size: 10.5px; letter-spacing: .28em; text-transform: uppercase; color: var(--blue); margin-bottom: 28px; }
        .section-tag::before { content: ''; width: 28px; height: 1px; background: var(--blue); opacity: .4; }
        .section-h2 { font-family: var(--f-display); font-size: clamp(42px,5.5vw,80px); font-weight: 600; line-height: 1; letter-spacing: -.03em; color: var(--ink); }
        .section-h2 em { font-style: italic; font-weight: 300; color: var(--blue-mid); }

        /* ── FEATURES ────────────────────────────────────────────── */
        #fitur { padding: 120px 52px; background: var(--white); overflow: hidden; }
        .features-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 72px; gap: 40px; }
        .features-sub { max-width: 320px; font-size: 15px; color: var(--muted); line-height: 1.85; font-weight: 300; }
        .features-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1px; background: var(--border); border: 1px solid var(--border); overflow: hidden; border-radius: 12px; }
        .feat { background: var(--white); padding: 48px 40px; display: flex; flex-direction: column; gap: 18px; transition: background .45s var(--ease-out); position: relative; overflow: hidden; }
        .feat::after { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg,var(--blue) 0%,var(--blue-mid) 100%); transform: scaleY(0); transform-origin: bottom; transition: transform .5s var(--ease-out); z-index: 0; }
        .feat:hover::after { transform: scaleY(1); }
        .feat > * { position: relative; z-index: 1; }
        .feat-num { font-family: var(--f-mono); font-size: 10.5px; letter-spacing: .2em; color: var(--muted); transition: color .4s; }
        .feat:hover .feat-num { color: rgba(255,255,255,.35); }
        .feat-icon-wrap { width: 48px; height: 48px; background: var(--blue-pale); border-radius: 10px; display: flex; align-items: center; justify-content: center; transition: background .4s; }
        .feat:hover .feat-icon-wrap       { background: rgba(255,255,255,.12); }
        .feat-icon-wrap svg               { color: var(--blue); transition: color .4s; }
        .feat:hover .feat-icon-wrap svg   { color: white; }
        .feat-title { font-family: var(--f-display); font-size: 22px; font-weight: 600; color: var(--ink); line-height: 1.25; letter-spacing: -.01em; transition: color .4s; }
        .feat:hover .feat-title { color: white; }
        .feat-desc { font-size: 14px; color: var(--muted); line-height: 1.75; font-weight: 300; transition: color .4s; }
        .feat:hover .feat-desc { color: rgba(255,255,255,.55); }
        .feat-arrow { margin-top: auto; width: 32px; height: 32px; border: 1px solid var(--border); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--muted); transition: all .4s var(--ease-out); }
        .feat:hover .feat-arrow { border-color: rgba(255,255,255,.3); background: rgba(255,255,255,.12); color: white; transform: rotate(45deg); }

        /* ── SCHOOLS ─────────────────────────────────────────────── */
        #kolaborasi { padding: 120px 0; background: var(--offwhite); overflow: hidden; }
        .schools-header { padding: 0 52px; margin-bottom: 64px; }
        .schools-strips { display: flex; flex-direction: column; gap: 20px; }
        .schools-strip { display: flex; gap: 20px; animation: schoolsFwd 38s linear infinite; will-change: transform; }
        .schools-strip-r { animation-name: schoolsBwd; animation-duration: 42s; }
        @keyframes schoolsFwd { from { transform: translateX(0); }    to { transform: translateX(-50%); } }
        @keyframes schoolsBwd { from { transform: translateX(-50%); } to { transform: translateX(0); } }
        .school-card { flex-shrink: 0; width: 220px; background: white; border: 1px solid var(--border-lt); border-radius: 12px; padding: 20px; display: flex; flex-direction: column; gap: 10px; transition: border-color .3s, box-shadow .3s, transform .3s var(--ease-out); }
        .school-card:hover { border-color: rgba(18,70,160,.2); box-shadow: 0 8px 32px rgba(18,70,160,.08); transform: translateY(-3px); }
        .school-logo-wrap { width: 48px; height: 48px; border-radius: 10px; background: var(--blue-pale); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
        .school-logo-wrap img { width: 100%; height: 100%; object-fit: contain; }
        .school-initial { font-family: var(--f-display); font-size: 18px; font-weight: 700; color: var(--blue); }
        .school-name { font-size: 12.5px; font-weight: 600; color: var(--ink); line-height: 1.35; }
        .school-city { font-family: var(--f-mono); font-size: 10px; letter-spacing: .12em; color: var(--muted); text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* ── TESTIMONIALS ────────────────────────────────────────── */
        #testimonial { padding: 120px 52px; background: var(--ink); overflow: hidden; }
        .testimonial-header { margin-bottom: 72px; }
        .testimonial-header .section-tag         { color: rgba(255,255,255,.35); }
        .testimonial-header .section-tag::before { background: rgba(255,255,255,.2); }
        .testimonial-header .section-h2          { color: white; }
        .testimonial-header .section-h2 em       { color: #60a5fa; }
        .testi-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1px; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.06); border-radius: 12px; overflow: hidden; }
        .testi { background: var(--ink); padding: 48px 40px; display: flex; flex-direction: column; gap: 24px; transition: background .4s; }
        .testi:hover { background: #111d30; }
        .testi-mark { font-family: var(--f-display); font-size: 48px; color: var(--blue-lt); opacity: .5; line-height: .8; }
        .testi-text { font-size: 15px; color: rgba(255,255,255,.55); line-height: 1.85; font-weight: 300; font-style: italic; font-family: var(--f-display); flex: 1; }
        .testi-meta { border-top: 1px solid rgba(255,255,255,.07); padding-top: 24px; display: flex; align-items: center; gap: 14px; }
        .testi-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg,var(--blue) 0%,var(--blue-lt) 100%); display: flex; align-items: center; justify-content: center; font-family: var(--f-display); font-size: 16px; font-weight: 700; color: white; flex-shrink: 0; }
        .testi-info { display: flex; flex-direction: column; gap: 2px; }
        .testi-name { font-size: 13.5px; font-weight: 500; color: rgba(255,255,255,.8); }
        .testi-role { font-family: var(--f-mono); font-size: 10px; letter-spacing: .12em; color: rgba(255,255,255,.25); }

        /* ── PRICING ─────────────────────────────────────────────── */
        #pricing { padding: 120px 52px; background: var(--offwhite); overflow: hidden; }
        .pricing-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 72px; gap: 40px; }
        .pricing-note { font-size: 14px; color: var(--muted); max-width: 280px; line-height: 1.75; font-weight: 300; }
        .pricing-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 20px; }
        .price-card { background: white; border: 1px solid var(--border-lt); border-radius: 14px; padding: 44px 36px; display: flex; flex-direction: column; position: relative; overflow: hidden; transition: transform .4s var(--ease-out), box-shadow .4s, border-color .3s; }
        .price-card:hover { transform: translateY(-6px); box-shadow: 0 32px 64px rgba(18,70,160,.10); border-color: rgba(18,70,160,.18); }
        .price-card.featured { background: linear-gradient(145deg,var(--blue) 0%,#0f3a88 100%); border-color: transparent; }
        .price-card.featured:hover { box-shadow: 0 32px 64px rgba(18,70,160,.3); }
        .price-badge { position: absolute; top: 20px; right: 20px; font-family: var(--f-mono); font-size: 9.5px; letter-spacing: .15em; text-transform: uppercase; background: rgba(255,255,255,.18); color: white; padding: 4px 10px; border-radius: 100px; }
        .price-tier { font-family: var(--f-mono); font-size: 10.5px; letter-spacing: .22em; text-transform: uppercase; color: var(--muted); margin-bottom: 28px; }
        .featured .price-tier { color: rgba(255,255,255,.4); }
        .price-amount { font-family: var(--f-display); font-size: clamp(38px,4.5vw,56px); font-weight: 600; color: var(--ink); letter-spacing: -.03em; line-height: 1; margin-bottom: 6px; }
        .featured .price-amount { color: white; }
        .price-period { font-size: 13px; color: var(--muted); margin-bottom: 36px; }
        .featured .price-period { color: rgba(255,255,255,.4); }
        .price-divider { height: 1px; background: var(--border-lt); margin-bottom: 28px; }
        .featured .price-divider { background: rgba(255,255,255,.1); }
        .price-features { list-style: none; display: flex; flex-direction: column; gap: 14px; margin-bottom: 36px; flex: 1; }
        .price-features li { display: flex; align-items: flex-start; gap: 10px; font-size: 14px; color: var(--muted); line-height: 1.5; }
        .featured .price-features li { color: rgba(255,255,255,.55); }
        .price-features li::before { content: ''; width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; background: url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='8' cy='8' r='7.5' stroke='%231246a0' stroke-opacity='0.2'/%3E%3Cpath d='M5 8l2 2 4-4' stroke='%231246a0' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") center/contain no-repeat; }
        .featured .price-features li::before { background: url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='8' cy='8' r='7.5' stroke='white' stroke-opacity='0.3'/%3E%3Cpath d='M5 8l2 2 4-4' stroke='white' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") center/contain no-repeat; }
        .btn-price { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 13px; text-decoration: none; font-size: 14px; font-weight: 500; letter-spacing: .01em; border-radius: 8px; border: 1.5px solid var(--border); color: var(--blue); background: transparent; transition: all .3s var(--ease-out); }
        .btn-price:hover { background: var(--blue); border-color: var(--blue); color: white; transform: translateY(-1px); }
        .btn-price-featured { background: white; border-color: transparent; color: var(--blue); }
        .btn-price-featured:hover { background: var(--blue-pale); transform: translateY(-1px); }

        /* ── FAQ ─────────────────────────────────────────────────── */
        #faq { padding: 120px 52px; background: var(--white); }
        .faq-layout { display: grid; grid-template-columns: 1fr 1.2fr; gap: 100px; align-items: start; }
        .faq-sticky { position: sticky; top: 88px; }
        .faq-list   { display: flex; flex-direction: column; }
        .faq-item { border-top: 1px solid var(--border-lt); padding: 28px 0; cursor: pointer; }
        .faq-item:last-child { border-bottom: 1px solid var(--border-lt); }
        .faq-q { display: flex; align-items: flex-start; justify-content: space-between; gap: 24px; }
        .faq-q-text { font-family: var(--f-display); font-size: 18px; font-weight: 600; color: var(--ink); line-height: 1.3; letter-spacing: -.01em; transition: color .3s; }
        .faq-item:hover .faq-q-text { color: var(--blue); }
        .faq-toggle { width: 26px; height: 26px; flex-shrink: 0; border: 1.5px solid var(--border); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--muted); transition: all .3s var(--ease-out); margin-top: 2px; font-size: 16px; }
        .faq-item.open .faq-toggle { background: var(--blue); border-color: var(--blue); color: white; transform: rotate(45deg); }
        .faq-a { font-size: 14.5px; color: var(--muted); line-height: 1.85; font-weight: 300; max-height: 0; overflow: hidden; transition: max-height .5s var(--ease-out), padding .3s; }
        .faq-item.open .faq-a { max-height: 300px; padding-top: 18px; }

        /* ── CTA FINALE ──────────────────────────────────────────── */
        #cta-finale { height: 80vh; min-height: 560px; background: linear-gradient(145deg,var(--blue) 0%,#0b2d78 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 80px 52px; position: relative; overflow: hidden; }
        .cta-grid-bg { position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px); background-size: 60px 60px; pointer-events: none; }
        .cta-orb { position: absolute; width: 600px; height: 600px; border-radius: 50%; background: radial-gradient(circle,rgba(255,255,255,.06) 0%,transparent 70%); top: 50%; left: 50%; transform: translate(-50%,-50%); pointer-events: none; }
        .cta-tag { font-family: var(--f-mono); font-size: 10.5px; letter-spacing: .28em; text-transform: uppercase; color: rgba(255,255,255,.4); margin-bottom: 32px; opacity: 0; transform: translateY(20px); position: relative; }
        .cta-h2 { font-family: var(--f-display); font-size: clamp(44px,7vw,96px); font-weight: 600; color: white; line-height: 1; letter-spacing: -.03em; margin-bottom: 48px; opacity: 0; transform: translateY(28px); position: relative; }
        .cta-h2 em { font-style: italic; font-weight: 300; color: #93c5fd; }
        .cta-actions { display: flex; align-items: center; gap: 28px; opacity: 0; transform: translateY(20px); position: relative; }
        .btn-white { background: white; color: var(--blue); display: inline-flex; align-items: center; gap: 10px; padding: 15px 32px; text-decoration: none; font-size: 14px; font-weight: 600; border-radius: 8px; transition: all .3s var(--ease-out); box-shadow: 0 4px 24px rgba(0,0,0,.2); }
        .btn-white:hover { background: var(--blue-pale); transform: translateY(-2px); box-shadow: 0 8px 32px rgba(0,0,0,.25); }
        .btn-ghost-cta { color: rgba(255,255,255,.55); text-decoration: none; font-size: 14px; padding-bottom: 2px; border-bottom: 1px solid rgba(255,255,255,.2); transition: color .3s, border-color .3s; }
        .btn-ghost-cta:hover { color: white; border-color: rgba(255,255,255,.6); }

        /* ── FOOTER ──────────────────────────────────────────────── */
        footer { background: var(--ink); padding: 64px 52px 36px; }
        .footer-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 48px; padding-bottom: 48px; border-bottom: 1px solid rgba(255,255,255,.06); margin-bottom: 36px; }
        .footer-brand { max-width: 280px; }
        .footer-logo { font-family: var(--f-display); font-size: 26px; font-weight: 600; color: white; letter-spacing: -.02em; margin-bottom: 12px; }
        .footer-tagline { font-size: 13.5px; color: rgba(255,255,255,.28); line-height: 1.7; }
        .footer-links { display: flex; gap: 64px; }
        .footer-col { display: flex; flex-direction: column; gap: 14px; }
        .footer-col-title { font-family: var(--f-mono); font-size: 10px; letter-spacing: .22em; text-transform: uppercase; color: rgba(255,255,255,.25); margin-bottom: 4px; }
        .footer-col a { font-size: 13.5px; color: rgba(255,255,255,.45); text-decoration: none; transition: color .3s; }
        .footer-col a:hover { color: rgba(255,255,255,.85); }
        .footer-bottom { display: flex; justify-content: space-between; align-items: center; gap: 20px; }
        .footer-copy, .footer-love { font-family: var(--f-mono); font-size: 10.5px; letter-spacing: .1em; color: rgba(255,255,255,.18); }

        /* ── WA FLOATING BUTTON ─────────────────────────────────── */
        .wa-float {
            position: fixed; bottom: 32px; right: 32px; z-index: 7000;
            display: flex; align-items: center; gap: 12px;
            text-decoration: none;
            animation: waFloat 3s ease-in-out infinite;
        }
        @keyframes waFloat {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-6px); }
        }
        .wa-float-label {
            background: white; color: var(--ink); font-size: 12.5px; font-weight: 500;
            padding: 8px 16px; border-radius: 100px;
            box-shadow: 0 4px 20px rgba(0,0,0,.12);
            opacity: 0; transform: translateX(12px);
            transition: opacity .3s, transform .3s var(--ease-out);
            white-space: nowrap; pointer-events: none;
        }
        .wa-float:hover .wa-float-label { opacity: 1; transform: translateX(0); }
        .wa-float-btn {
            width: 56px; height: 56px; border-radius: 50%;
            background: linear-gradient(135deg,#25D366 0%,#128C7E 100%);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 6px 24px rgba(37,211,102,.4);
            transition: transform .3s var(--ease-spring), box-shadow .3s;
            flex-shrink: 0;
        }
        .wa-float:hover .wa-float-btn {
            transform: scale(1.08);
            box-shadow: 0 10px 36px rgba(37,211,102,.5);
        }
        .wa-float-btn svg { color: white; }
        .wa-pulse {
            position: absolute; top: 0; right: 0; width: 14px; height: 14px;
            background: #4ade80; border-radius: 50%;
            border: 2px solid white;
            animation: waPulse 2s ease-out infinite;
        }
        @keyframes waPulse {
            0%   { box-shadow: 0 0 0 0 rgba(74,222,128,.6); }
            70%  { box-shadow: 0 0 0 8px rgba(74,222,128,0); }
            100% { box-shadow: 0 0 0 0 rgba(74,222,128,0); }
        }

        /* ── PRICING WARNING ─────────────────────────────────────── */
        .pricing-warning {
            display: flex; align-items: flex-start; gap: 14px;
            background: linear-gradient(135deg,#fff8e1 0%,#fff3cd 100%);
            border: 1px solid rgba(245,158,11,.25);
            border-left: 3px solid #f59e0b;
            border-radius: 10px; padding: 18px 22px;
            margin-bottom: 48px;
        }
        .pricing-warning-icon {
            width: 36px; height: 36px; flex-shrink: 0;
            background: rgba(245,158,11,.12); border-radius: 8px;
            display: flex; align-items: center; justify-content: center; margin-top: 1px;
        }
        .pricing-warning-icon svg { color: #d97706; }
        .pricing-warning-content { display: flex; flex-direction: column; gap: 4px; }
        .pricing-warning-title { font-size: 13.5px; font-weight: 600; color: #92400e; letter-spacing: .01em; }
        .pricing-warning-text { font-size: 13px; color: #a16207; line-height: 1.6; font-weight: 300; }

        /* ── PRICING SAME FEATURES BADGE ────────────────────────── */
        .price-equal-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--blue-pale); color: var(--blue);
            font-family: var(--f-mono); font-size: 9.5px; letter-spacing: .16em;
            text-transform: uppercase; padding: 4px 10px; border-radius: 100px;
            margin-bottom: 14px;
        }
        .price-equal-badge::before { content: '✦'; font-size: 8px; }

        /* ── TRUST BADGES ────────────────────────────────────────── */
        .trust-strip {
            display: flex; align-items: center; justify-content: center;
            gap: 48px; flex-wrap: wrap;
            padding: 36px 52px;
            background: var(--offwhite);
            border-top: 1px solid var(--border-lt);
            border-bottom: 1px solid var(--border-lt);
        }
        .trust-item {
            display: flex; align-items: center; gap: 10px;
            font-size: 12.5px; color: var(--muted); font-weight: 400;
        }
        .trust-item svg { color: var(--blue); opacity: .6; flex-shrink: 0; }

        /* ── SECTION DIVIDER ─────────────────────────────────────── */
        .section-divider {
            width: 1px; height: 80px; background: linear-gradient(180deg,transparent,var(--border),transparent);
            margin: 0 auto;
        }

        /* ── PRICE CARD ENHANCEMENTS ─────────────────────────────── */
        .price-highlight-row {
            display: flex; align-items: center; gap: 8px;
            background: rgba(18,70,160,.04); border: 1px solid rgba(18,70,160,.08);
            border-radius: 8px; padding: 12px 16px; margin-bottom: 20px;
        }
        .price-highlight-row svg { color: var(--blue); flex-shrink: 0; }
        .price-highlight-row span { font-size: 12.5px; color: var(--blue); font-weight: 500; }
        .featured .price-highlight-row { background: rgba(255,255,255,.1); border-color: rgba(255,255,255,.15); }
        .featured .price-highlight-row svg, .featured .price-highlight-row span { color: white; }

        /* ── REVEAL ANIMATIONS ───────────────────────────────────── */
        .reveal      { opacity: 0; transform: translateY(36px); }
        .reveal-left { opacity: 0; transform: translateX(-36px); }
        .reveal.visible, .reveal-left.visible { opacity: 1; transform: translate(0); transition: opacity .85s var(--ease-out), transform .85s var(--ease-out); }

        /* ── RESPONSIVE ──────────────────────────────────────────── */
        html, body { overflow-x: hidden; max-width: 100%; }

        @media (max-width: 1024px) {
            nav { width: calc(100% - 48px); }
            #fitur, #testimonial, #pricing, #faq { padding: 80px 32px; }
            .hero { padding: 0 32px 60px; }
            .hero-badge { left: 32px; }
            .hero-floating-num { right: 32px; }
            .schools-header { padding: 0 32px; }
            .features-grid, .testi-grid, .pricing-grid { grid-template-columns: 1fr 1fr; }
            .stat-block { flex: 1; padding: 32px 24px; }
            footer { padding: 48px 32px 28px; }
            #cta-finale { padding: 60px 32px; }
            .trust-strip { padding: 28px 32px; }
        }

        @media (max-width: 768px) {
            nav {
                width: calc(100% - 24px);
                height: 56px;
                border-radius: 50px;
            }
            .nav-toggle { display: flex; }
            .nav-links { display: none; }
            .nav-logo-text { font-size: 14px; }
            .nav-wa { display: none; }
            .nav-divider { display: none; }
            
            .hero {
                padding: 100px 20px 48px;
                min-height: 100vh;
                justify-content: center;
            }
            .hero-h1 {
                font-size: clamp(36px, 9vw, 64px);
                margin-bottom: 24px;
                word-break: break-word;
            }
            .hero-bg { background: linear-gradient(160deg, #0d1b35 0%, #0a1528 100%); } /* simplify on mobile */
            .hero-orb { display: none; } /* Remove large absolute element on mobile to prevent overflow */
            .hero-badge {
                position: relative;
                top: auto;
                left: auto;
                transform: none !important;
                display: inline-flex;
                margin-bottom: 24px;
            }
            .hero-floating-num { display: none; }
            .hero-desc {
                font-size: 14.5px;
                max-width: 100%;
                margin-bottom: 24px;
            }
            .hero-bottom {
                flex-direction: column;
                align-items: flex-start;
                opacity: 1 !important; /* GSAP fallback */
            }
            .hero-actions {
                width: 100%;
                flex-direction: column;
                gap: 16px;
            }
            .btn-primary, .btn-ghost-hero {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
            .btn-primary { padding: 14px 20px; font-size: 14.5px; }
            .hero-scroll { display: none; }

            .features-grid, .testi-grid, .pricing-grid { 
                grid-template-columns: 1fr; 
                gap: 16px; 
                background: transparent; 
                border: none; 
            }
            .feat, .testi, .price-card { 
                padding: 32px 24px; 
                border-radius: 12px; 
                border: 1px solid var(--border);
            }
            .feat-title { font-size: 20px; }

            .feat { background: var(--white); }
            .testi { background: #0f1c34; border-color: rgba(255,255,255,.06); }
            
            .stats-bar { flex-direction: column; }
            .stat-block { border-right: none; border-bottom: 1px solid rgba(255,255,255,.1); padding: 32px 24px; }
            .stat-block:last-child { border-bottom: none; }
            .stat-n { font-size: clamp(36px, 8vw, 48px); }

            .schools-header { padding: 0 20px; }
            .school-card { width: 180px; }

            #fitur, #testimonial, #pricing, #faq { padding: 80px 20px; }
            #kolaborasi { padding: 80px 0; }
            #cta-finale { padding: 80px 20px; }

            .section-h2 { font-size: clamp(32px, 8vw, 52px); }
            .cta-h2 { font-size: clamp(32px, 8vw, 56px); }

            .features-header, .pricing-header { flex-direction: column; gap: 20px; align-items: flex-start; }
            .features-sub, .pricing-note { max-width: 100%; }
            .faq-layout { grid-template-columns: 1fr; gap: 40px; }
            .faq-sticky { position: static; }

            .footer-top { flex-direction: column; gap: 32px; }
            .footer-links { gap: 40px; flex-wrap: wrap; flex-direction: row; }
            footer { padding: 48px 20px 24px; }

            .trust-strip { padding: 24px 20px; gap: 16px; flex-direction: column; align-items: flex-start; }
            .trust-item { font-size: 13px; }

            .wa-float { bottom: 20px; right: 20px; }
        }

        @media (max-width: 480px) {
            nav {
                width: calc(100% - 16px);
                height: 52px;
                padding: 0 12px;
                top: 8px;
            }
            .nav-logo-img { height: 20px; }
            .nav-logo-text { font-size: 15px; }
            .nav-cta { padding: 8px 16px !important; font-size: 12px !important; }

            .hero {
                padding: 100px 16px 40px;
            }
            .hero-h1 {
                font-size: clamp(34px, 10vw, 48px);
                margin-bottom: 20px;
                line-height: 1.15;
            }
            .hero-badge {
                font-size: 9.5px;
                padding: 6px 14px;
                margin-bottom: 24px;
            }
            .hero-desc { font-size: 14px; margin-bottom: 24px; line-height: 1.7; }
            .btn-primary { padding: 14px; font-size: 14px; }

            .stat-block { padding: 24px 16px; }
            .stat-n { font-size: clamp(32px, 9vw, 40px); }
            .stat-l { font-size: 10px; }

            #fitur, #testimonial, #pricing, #faq { padding: 64px 16px; }
            #cta-finale { padding: 64px 16px; }

            .section-tag { font-size: 10px; margin-bottom: 16px; }
            .section-h2 { font-size: clamp(28px, 8.5vw, 40px); }

            .feat, .testi, .price-card {
                padding: 24px 20px;
            }
            .feat-icon-wrap { width: 44px; height: 44px; margin-bottom: 8px; }
            .feat-title { font-size: 18px; }
            .feat-desc { font-size: 13.5px; }

            .testi { background: #0f1c34; border-color: rgba(255,255,255,.06); }
            .testi-mark { font-size: 36px; margin-bottom: -10px; }
            .testi-text { font-size: 14px; }

            .price-amount { font-size: clamp(32px, 9vw, 36px); }
            .price-features li { font-size: 13.5px; }
            .btn-price { padding: 14px; font-size: 14px; }

            .faq-layout { gap: 28px; }
            .faq-q-text { font-size: 16px; }
            .faq-a { font-size: 13.5px; }

            .cta-h2 { font-size: clamp(32px, 8.5vw, 44px); margin-bottom: 24px; }
            .cta-actions { gap: 12px; flex-direction: column; width: 100%; }
            .btn-white, .btn-ghost-cta { width: 100%; text-align: center; justify-content: center; }

            .schools-header { padding: 0 16px; }
            .school-card { width: 160px; }
            .school-name { font-size: 12px; }

            footer { padding: 40px 16px 20px; }
            .footer-links { gap: 32px; flex-direction: row; }

            .trust-strip { padding: 20px 16px; gap: 12px; }

            .wa-float { bottom: 16px; right: 16px; }
            .wa-float-btn { width: 48px; height: 48px; }
            .wa-float-label { font-size: 11px; }

            .marquee-item { font-size: 10.5px; }
            .pricing-warning { margin-bottom: 24px; padding: 16px; }
        }

        @media (max-width: 360px) {
            .hero-h1 { font-size: clamp(28px, 10vw, 40px); }
            .section-h2 { font-size: clamp(24px, 9vw, 36px); }
            .hero { padding: 80px 12px 32px; }
            #fitur, #testimonial, #pricing, #faq { padding: 48px 12px; }
            .feat, .testi, .price-card { padding: 20px 16px; }
            .feat-title { font-size: 17px; }
            .nav-logo-text { display: none; } 
            .nav-cta { font-size: 11px !important; padding: 8px 12px !important; }
        }
    </style>
</head>
<body>

{{-- ══ CURSOR ══════════════════════════════════════════════════════════ --}}
<div class="cursor" id="cursor"></div>
<div class="cursor-ring" id="cursorRing"></div>

{{-- ══ WA FLOATING BUTTON ══════════════════════════════════════════════ --}}
<a href="https://wa.me/628953567535000" target="_blank" class="wa-float" rel="noopener noreferrer" title="Hubungi via WhatsApp">
    <span class="wa-float-label">💬 Hubungi Kami via WA</span>
    <div class="wa-float-btn" style="position:relative;">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        <div class="wa-pulse"></div>
    </div>
</a>

{{-- ══ LOADER ══════════════════════════════════════════════════════════ --}}
<div id="loader">
    <div class="loader-icon" id="loaderIcon">
        <img src="{{ asset('assets/images/EDUFINANCE1.png') }}" alt="EduFinance Logo">
    </div>
    <div class="loader-logo" id="loaderLogo">EduFinance</div>
    <div class="loader-sub"  id="loaderSub">Pencatatan Keuangan · Lebih Cerdas</div>
    <div class="loader-progress-wrap">
        <div class="loader-bar-track"><div class="loader-bar" id="loaderBar"></div></div>
        <div class="loader-pct" id="loaderPct">0%</div>
    </div>
    <div class="loader-curtain" id="loaderCurtain"></div>
</div>

{{-- ══ NAV ═════════════════════════════════════════════════════════════ --}}
<nav id="mainNav">
    <a href="/" class="nav-logo">
        <img src="{{ asset('assets/images/EDUFINANCE1.png') }}" alt="EduFinance Logo" class="nav-logo-img">
        <span class="nav-logo-text">EduFinance</span>
    </a>
    <div class="nav-links">
        <a href="#fitur">Fitur</a>
        <a href="#kolaborasi">Instansi</a>
        <a href="#testimonial">Testimonial</a>
        <a href="#pricing">Harga</a>
        <a href="https://wa.me/62895356753500" target="_blank" rel="noopener" class="nav-wa">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            WA
        </a>
        <div class="nav-divider"></div>
        @auth
            <a href="{{ route('dashboard') }}" class="nav-cta">Dashboard</a>
        @else
            <a href="{{ route('login') }}" style="margin-right:2px;">Masuk</a>
            <a href="{{ route('pricing') }}" class="nav-cta">Mulai Sekarang</a>
        @endauth
    </div>
    <button class="nav-toggle" id="navToggle">
        <span></span>
        <span></span>
        <span></span>
    </button>
</nav>

{{-- Mobile Navigation Menu --}}
<div class="nav-mobile" id="navMobile">
    <a href="#fitur" onclick="closeMobileMenu()">📱 Fitur</a>
    <a href="#kolaborasi" onclick="closeMobileMenu()">🏫 Instansi</a>
    <a href="#testimonial" onclick="closeMobileMenu()">⭐ Testimonial</a>
    <a href="#pricing" onclick="closeMobileMenu()">💰 Harga</a>
    <a href="https://wa.me/62895356753500" target="_blank" rel="noopener" onclick="closeMobileMenu()">📞 WhatsApp</a>
    @auth
        <a href="{{ route('dashboard') }}" class="nav-mobile-cta" onclick="closeMobileMenu()">Dashboard</a>
    @else
        <a href="{{ route('login') }}" onclick="closeMobileMenu()">Masuk</a>
        <a href="{{ route('pricing') }}" class="nav-mobile-cta" onclick="closeMobileMenu()">Mulai Sekarang</a>
    @endauth
</div>

{{-- ══ HERO ════════════════════════════════════════════════════════════ --}}
@php
    use App\Models\User;
    use App\Models\MutasiKas;
    $totalSekolah   = User::where('role', 'admin')->count();
    $totalTransaksi = MutasiKas::count();
    $totalBulanIni  = User::where('role', 'admin')->where('created_at', '>=', now()->startOfMonth())->count();
@endphp

<section class="hero" id="hero">
    <div class="hero-bg"></div>
    <div class="hero-grid"></div>
    <div class="hero-orb" id="heroOrb"></div>

    <div class="hero-badge" id="heroBadge">
        <div class="hero-badge-dot"></div>
        Dipercaya {{ number_format($totalSekolah) }}+ Instansi di Indonesia
    </div>
    <div class="hero-floating-num" id="heroFloatingNum">
        <div class="hero-floating-n">{{ number_format($totalTransaksi) }}</div>
        <div class="hero-floating-l">Total Transaksi</div>
    </div>

    <div class="hero-main">
        <h1 class="hero-h1">
            <span class="hero-h1-line"><span class="hero-h1-inner">Keuangan</span></span>
            <span class="hero-h1-line"><span class="hero-h1-inner"><em>Instansi,</em></span></span>
            <span class="hero-h1-line"><span class="hero-h1-inner">Lebih Cerdas.</span></span>
        </h1>
        <div class="hero-bottom" id="heroBottom">
            <p class="hero-desc">
                Catat pemasukan &amp; pengeluaran, buat laporan otomatis, pantau saldo real-time —
                semua dalam satu platform yang dirancang untuk administrasi instansi Indonesia.
            </p>
            <div class="hero-actions">
                <a href="#fitur" class="btn-ghost-hero">Lihat Fitur</a>
                <a href="{{ route('pricing') }}" class="btn-primary">
                    Mulai Sekarang!
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    <div class="hero-scroll" id="heroScroll">
        <span>Scroll</span>
        <div class="scroll-line"></div>
    </div>
</section>

{{-- ══ STATS BAR ═══════════════════════════════════════════════════════ --}}
<div class="stats-bar">
    <div class="stat-block" id="sb1">
        <div class="stat-n"><span id="cnt-sekolah">{{ number_format($totalSekolah) }}</span><sup>+</sup></div>
        <div class="stat-l">Instansi Aktif</div>
    </div>
    <div class="stat-block" id="sb2">
        <div class="stat-n"><span id="cnt-transaksi">{{ number_format(intval($totalTransaksi / 1000)) }}</span><sup>K+</sup></div>
        <div class="stat-l">Transaksi Tercatat</div>
    </div>
    <div class="stat-block" id="sb3">
        <div class="stat-n"><span id="cnt-bulan">{{ number_format($totalBulanIni) }}</span></div>
        <div class="stat-l">Bergabung Bulan Ini</div>
    </div>
</div>

{{-- ══ MARQUEE ══════════════════════════════════════════════════════════ --}}
{{-- Trust Strip --}}
<div class="trust-strip">
    <div class="trust-item">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Data Terenkripsi Penuh
    </div>
    <div class="trust-item">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Aktif dalam 5 Menit
    </div>
    <div class="trust-item">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
        Support via WhatsApp
    </div>
    <div class="trust-item">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
        Tanpa Biaya Tersembunyi
    </div>
    <div class="trust-item">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        Berjalan di Semua Perangkat
    </div>
    <div class="trust-item">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Laporan Siap Cetak
    </div>
</div>
<div class="marquee-section" aria-hidden="true">
    <div class="marquee-track">
        @for ($i = 0; $i < 8; $i++)
            <span class="marquee-item">Mutasi Kas Real-time</span><span class="marquee-sep">·</span>
            <span class="marquee-item">Laporan PDF &amp; Excel</span><span class="marquee-sep">·</span>
            <span class="marquee-item">Data Terisolasi</span><span class="marquee-sep">·</span>
            <span class="marquee-item">Kategori Kustom</span><span class="marquee-sep">·</span>
            <span class="marquee-item">Single Device Login</span><span class="marquee-sep">·</span>
            <span class="marquee-item">Notifikasi WhatsApp</span><span class="marquee-sep">·</span>
        @endfor
    </div>
</div>

{{-- ══ FEATURES ═════════════════════════════════════════════════════════ --}}
<section id="fitur">
    <div class="features-header">
        <div>
            <div class="section-tag reveal">Fitur Platform</div>
            <h2 class="section-h2 reveal" style="transition-delay:.08s">Semua yang Anda<br><em>Butuhkan.</em></h2>
        </div>
        <p class="features-sub reveal" style="transition-delay:.16s">
            Dirancang khusus untuk memudahkan administrasi instansi — tidak perlu keahlian akuntansi apapun. Semua fitur tersedia di setiap paket.
        </p>
    </div>
    <div class="features-grid">
        @php
            $features = [
                ['num'=>'01','icon'=>'<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M12 6v6l4 2"/>','title'=>'Mutasi Kas Real-time','desc'=>'Catat pemasukan & pengeluaran dengan mudah. Saldo otomatis terhitung akurat setiap saat.','delay'=>'0s'],
                ['num'=>'02','icon'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>','title'=>'Laporan PDF &amp; Excel','desc'=>'Ekspor laporan siap cetak berkop surat ke PDF atau Excel. Laporan bulanan, triwulan, hingga tahunan.','delay'=>'.06s'],
                ['num'=>'03','icon'=>'<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>','title'=>'Data Terisolasi','desc'=>'Data setiap instansi terisolasi penuh dengan enkripsi. Instansi lain tidak dapat melihat data Anda.','delay'=>'.12s'],
                ['num'=>'04','icon'=>'<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>','title'=>'Kategori Kustom','desc'=>'Buat kategori pemasukan & pengeluaran sesuai kebutuhan instansi. Fleksibel dan mudah dikelola.','delay'=>'.04s'],
                ['num'=>'05','icon'=>'<rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>','title'=>'Single Device Login','desc'=>'Satu akun hanya aktif di satu perangkat dalam satu waktu. Keamanan ekstra untuk data keuangan Anda.','delay'=>'.10s'],
                ['num'=>'06','icon'=>'<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>','title'=>'Notifikasi WhatsApp','desc'=>'Terima notifikasi transaksi & pengingat laporan langsung ke WhatsApp. Tetap terhubung di mana saja.','delay'=>'.16s'],
            ];
        @endphp
        @foreach($features as $f)
            <div class="feat reveal" style="transition-delay:{{ $f['delay'] }}">
                <div class="feat-num">{{ $f['num'] }}</div>
                <div class="feat-icon-wrap">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">{!! $f['icon'] !!}</svg>
                </div>
                <div class="feat-title">{!! $f['title'] !!}</div>
                <p class="feat-desc">{{ $f['desc'] }}</p>
                <div class="feat-arrow"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg></div>
            </div>
        @endforeach
    </div>
</section>

{{-- ══ SCHOOLS SHOWCASE ════════════════════════════════════════════════ --}}
<section id="kolaborasi">
    <div class="schools-header">
        <div class="section-tag reveal">Jaringan Instansi</div>
        <h2 class="section-h2 reveal" style="transition-delay:.08s">Dipercaya Instansi<br><em>Seluruh Indonesia.</em></h2>
    </div>

    @php
        $schoolsData = \App\Models\SchoolSetting::latest()->take(20)->get();
        $strip1      = $schoolsData->take(10);
        $combined1   = $strip1->concat($strip1);
        $strip2      = $schoolsData->skip(10)->take(10);
        $combined2   = $strip2->concat($strip2);
    @endphp

    <div class="schools-strips">
        {{-- Strip 1 — maju --}}
        <div class="schools-strip">
            @foreach($combined1 as $school)
                <div class="school-card">
                    <div class="school-logo-wrap">
                        @if($school->logo_sekolah)
                            <img src="{{ Storage::url($school->logo_sekolah) }}"
                                 alt="{{ $school->nama_sekolah }}"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                            <div class="school-initial" style="display:none">{{ strtoupper(substr($school->nama_sekolah ?? 'S', 0, 1)) }}</div>
                        @else
                            <div class="school-initial">{{ strtoupper(substr($school->nama_sekolah ?? 'S', 0, 1)) }}</div>
                        @endif
                    </div>
                    <div class="school-name">{{ Str::limit($school->nama_sekolah ?? 'Nama Instansi', 32) }}</div>
                    <div class="school-city">{{ $school->kota ?? Str::limit($school->alamat ?? 'Indonesia', 30) }}</div>
                </div>
            @endforeach
        </div>

        {{-- Strip 2 — mundur --}}
        <div class="schools-strip schools-strip-r">
            @foreach($combined2 as $school)
                <div class="school-card">
                    <div class="school-logo-wrap">
                        @if($school->logo_sekolah)
                            <img src="{{ Storage::url($school->logo_sekolah) }}"
                                 alt="{{ $school->nama_sekolah }}"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                            <div class="school-initial" style="display:none">{{ strtoupper(substr($school->nama_sekolah ?? 'S', 0, 1)) }}</div>
                        @else
                            <div class="school-initial">{{ strtoupper(substr($school->nama_sekolah ?? 'S', 0, 1)) }}</div>
                        @endif
                    </div>
                    <div class="school-name">{{ Str::limit($school->nama_sekolah ?? 'Nama Instansi', 32) }}</div>
                    <div class="school-city">{{ $school->kota ?? Str::limit($school->alamat ?? 'Indonesia', 30) }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ TESTIMONIALS ════════════════════════════════════════════════════ --}}
<section id="testimonial">
    <div class="testimonial-header">
        <div class="section-tag reveal">Testimonial</div>
        <h2 class="section-h2 reveal" style="transition-delay:.08s">Kata Mereka yang<br><em>Telah Bergabung.</em></h2>
    </div>
    <div class="testi-grid">
        @php
            $testimonials = [
                ['i'=>'S','t'=>'EduFinance benar-benar mengubah cara kami mengelola keuangan. Laporan yang dulu memakan waktu berjam-jam kini selesai dalam hitungan menit.','n'=>'Siti Rahayu','r'=>'Bendahara · SDN 04 Depok','d'=>'0s'],
                ['i'=>'A','t'=>'Sangat mudah digunakan walau saya tidak berlatar belakang akuntansi. Export PDF-nya rapi dan langsung bisa dikirim ke dinas pendidikan.','n'=>'Ahmad Fauzi','r'=>'Kepala Instansi · SMP Harapan Bangsa','d'=>'.06s'],
                ['i'=>'D','t'=>'Fitur notifikasi Email dengan pengiriman invoice sangat membantu. Setiap pembelian lisensi langsung ada pemberitahuan, jadi tidak ada yang terlewat.','n'=>'Dewi Kusuma','r'=>'Bendahara · MTs Nurul Iman Bogor','d'=>'.12s'],
                ['i'=>'R','t'=>'Data kami aman dan terisolasi. Instansi lain tidak bisa melihat keuangan kami. Ini sangat penting untuk menjaga kepercayaan orang tua siswa.','n'=>'Rizky Pratama','r'=>'Bendahara · SMA Al-Azhar Bekasi','d'=>'.18s'],
                ['i'=>'N','t'=>'Proses setup sangat cepat, tidak sampai 5 menit sudah bisa langsung digunakan. Tim support juga responsif dan ramah sekali.','n'=>'Nurul Hidayah','r'=>'Tata Usaha · SDN 12 Tangerang','d'=>'.24s'],
                ['i'=>'H','t'=>'Paket lifetime-nya worth it banget. Sekali bayar, update gratis selamanya. Sudah dua tahun pakai dan fiturnya makin lengkap terus.','n'=>'Hendra Wijaya','r'=>'Kepala TU · SMK Nusantara Bandung','d'=>'.30s'],
            ];
        @endphp
        @foreach($testimonials as $t)
            <div class="testi reveal" style="transition-delay:{{ $t['d'] }}">
                <div class="testi-mark">"</div>
                <p class="testi-text">{{ $t['t'] }}</p>
                <div class="testi-meta">
                    <div class="testi-avatar">{{ $t['i'] }}</div>
                    <div class="testi-info">
                        <div class="testi-name">{{ $t['n'] }}</div>
                        <div class="testi-role">{{ $t['r'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- ══ PRICING ══════════════════════════════════════════════════════════ --}}
<section id="pricing">
    <div class="pricing-header">
        <div>
            <div class="section-tag reveal">Harga</div>
            <h2 class="section-h2 reveal" style="transition-delay:.08s">Pilih Durasi,<br><em>Bukan Fitur.</em></h2>
        </div>
        <p class="pricing-note reveal" style="transition-delay:.16s">Semua paket menyertakan akses penuh ke seluruh fitur — tanpa batasan, tanpa biaya tersembunyi.</p>
    </div>

    {{-- Warning: sistem lisensi, bukan berlangganan --}}
    <div class="pricing-warning reveal" style="transition-delay:.20s">
        <div class="pricing-warning-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div class="pricing-warning-content">
            <div class="pricing-warning-title">⚠ Perhatian — Sistem Lisensi, Bukan Berlangganan Otomatis</div>
            <div class="pricing-warning-text">EduFinance menggunakan sistem <strong>lisensi satu waktu</strong>. Pembayaran <strong>tidak diproses secara otomatis</strong> dan tidak ada auto-renewal. Setiap pembelian menghasilkan kode lisensi unik yang dikirim via WhatsApp &amp; Email. Perpanjangan dilakukan secara manual sesuai kebutuhan Anda.</div>
        </div>
    </div>

    <div class="pricing-grid">
        {{-- Bulanan --}}
        <div class="price-card reveal">
            <div class="price-equal-badge">Akses Penuh</div>
            <div class="price-tier">Bulanan</div>
            <div class="price-amount">Rp 100K</div>
            <div class="price-period">per bulan · bayar sekali, aktif 30 hari</div>
            <div class="price-highlight-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span>Cocok untuk uji coba platform</span>
            </div>
            <div class="price-divider"></div>
            <ul class="price-features">
                <li>Mutasi Kas Real-time</li>
                <li>Laporan PDF &amp; Excel berkop surat</li>
                <li>Kategori kustom tak terbatas</li>
                <li>Notifikasi WhatsApp</li>
                <li>Single Device Login</li>
                <li>Data terisolasi &amp; terenkripsi</li>
                <li>Support via WhatsApp</li>
                <li>Update otomatis</li>
            </ul>
            <a href="{{ route('checkout.page', 'monthly') }}" class="btn-price">Pilih Bulanan →</a>
        </div>

        {{-- Tahunan (featured) --}}
        <div class="price-card featured reveal" style="transition-delay:.08s">
            <div class="price-badge">Terpopuler</div>
            <div class="price-equal-badge" style="background:rgba(255,255,255,.12);color:rgba(255,255,255,.7);">Akses Penuh</div>
            <div class="price-tier">Tahunan</div>
            <div class="price-amount">Rp 1 Jt</div>
            <div class="price-period">per tahun · hemat Rp 200.000 vs bulanan</div>
            <div class="price-highlight-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <span>Hemat 2 bulan dibanding Bulanan</span>
            </div>
            <div class="price-divider"></div>
            <ul class="price-features">
                <li>Mutasi Kas Real-time</li>
                <li>Laporan PDF &amp; Excel berkop surat</li>
                <li>Kategori kustom tak terbatas</li>
                <li>Notifikasi WhatsApp</li>
                <li>Single Device Login</li>
                <li>Data terisolasi &amp; terenkripsi</li>
                <li>Support prioritas via WhatsApp</li>
                <li>Update otomatis</li>
            </ul>
            <a href="{{ route('checkout.page', 'yearly') }}" class="btn-price btn-price-featured">Pilih Tahunan →</a>
        </div>

        {{-- Lifetime --}}
        <div class="price-card reveal" style="transition-delay:.16s">
            <div class="price-equal-badge">Akses Penuh</div>
            <div class="price-tier">Lifetime</div>
            <div class="price-amount">Rp 5 Jt</div>
            <div class="price-period">sekali bayar · aktif selamanya</div>
            <div class="price-highlight-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <span>Nilai terbaik — bayar sekali, selamanya</span>
            </div>
            <div class="price-divider"></div>
            <ul class="price-features">
                <li>Mutasi Kas Real-time</li>
                <li>Laporan PDF &amp; Excel berkop surat</li>
                <li>Kategori kustom tak terbatas</li>
                <li>Notifikasi WhatsApp</li>
                <li>Single Device Login</li>
                <li>Data terisolasi &amp; terenkripsi</li>
                <li>Support prioritas seumur hidup</li>
                <li>Update gratis selamanya</li>
            </ul>
            <a href="{{ route('checkout.page', 'lifetime') }}" class="btn-price">Pilih Lifetime →</a>
        </div>
    </div>
</section>

{{-- ══ FAQ ══════════════════════════════════════════════════════════════ --}}
<section id="faq">
    <div class="faq-layout">
        <div class="faq-sticky">
            <div class="section-tag reveal">FAQ</div>
            <h2 class="section-h2 reveal" style="transition-delay:.08s">Pertanyaan<br><em>Umum.</em></h2>
            <p class="reveal" style="transition-delay:.16s;margin-top:20px;font-size:15px;color:var(--muted);line-height:1.85;font-weight:300;max-width:300px;">
                Masih ada pertanyaan? Hubungi kami via WhatsApp dan kami siap membantu.
            </p>
            <a href="https://wa.me/62895356753500"
               target="_blank" class="reveal" rel="noopener"
               style="transition-delay:.24s;display:inline-flex;align-items:center;gap:8px;margin-top:28px;font-size:14px;color:var(--blue);text-decoration:none;font-weight:500;border-bottom:1px solid rgba(18,70,160,.2);padding-bottom:2px;">
                Hubungi via WhatsApp →
            </a>
        </div>
        <div class="faq-list">
            @php
                $faqs = [
                    ['q'=>'Bagaimana cara memulai?','a'=>'Pilih paket yang sesuai, lakukan pembayaran via Midtrans (transfer bank, QRIS, e-wallet), terima kode lisensi otomatis via WhatsApp & Email, lalu daftar akun dengan kode tersebut. Proses tidak lebih dari 5 menit.','d'=>'0s'],
                    ['q'=>'Apakah data instansi saya aman?','a'=>'Ya, data setiap instansi terisolasi penuh. Instansi lain tidak dapat melihat atau mengakses data Anda. Semua data dienkripsi dan disimpan aman di server kami.','d'=>'.05s'],
                    ['q'=>'Bisakah laporan diexport ke Excel?','a'=>'Ya, semua laporan bisa diexport ke format Excel (.xlsx) maupun PDF berkop surat. Laporan siap cetak dan dikirim ke dinas pendidikan.','d'=>'.10s'],
                    ['q'=>'Support tersedia kapan?','a'=>'Tim support kami siap membantu via WhatsApp, email, dan telepon selama jam kerja (08.00–17.00 WIB, Senin–Jumat). Paket Tahunan dan Lifetime mendapat akses support prioritas.','d'=>'.15s'],
                    ['q'=>'Apa itu Single Device Login?','a'=>'Fitur keamanan yang memastikan satu akun hanya aktif di satu perangkat dalam satu waktu. Jika login dari perangkat lain, sesi sebelumnya akan otomatis berakhir.','d'=>'.20s'],
                ];
            @endphp
            @foreach($faqs as $faq)
                <div class="faq-item reveal" style="transition-delay:{{ $faq['d'] }}">
                    <div class="faq-q">
                        <div class="faq-q-text">{{ $faq['q'] }}</div>
                        <div class="faq-toggle">+</div>
                    </div>
                    <div class="faq-a">{{ $faq['a'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ CTA FINALE ══════════════════════════════════════════════════════ --}}
<section id="cta-finale">
    <div class="cta-grid-bg"></div>
    <div class="cta-orb"></div>
    <div class="cta-tag" id="ctaTag">Siap Bergabung?</div>
    <h2 class="cta-h2" id="ctaH2">Kelola Keuangan<br><em>Lebih Baik.</em></h2>
    <div class="cta-actions" id="ctaActions">
        <a href="{{ route('pricing') }}" class="btn-white">Mulai Sekarang →</a>
        <a href="https://wa.me/62895356753500"
           target="_blank" class="btn-ghost-cta" rel="noopener">Hubungi via WhatsApp</a>
    </div>
</section>

{{-- ══ FOOTER ══════════════════════════════════════════════════════════ --}}
<footer>
    <div class="footer-top">
        <div class="footer-brand">
            <div class="footer-logo">EduFinance</div>
            <p class="footer-tagline">Aplikasi manajemen keuangan instansi modern &amp; terpercaya untuk Indonesia.</p>
        </div>
        <div class="footer-links">
            <div class="footer-col">
                <div class="footer-col-title">Platform</div>
                <a href="#fitur">Fitur</a>
                <a href="#pricing">Harga</a>
                <a href="{{ route('login') }}">Masuk</a>
                <a href="{{ route('pricing') }}">Daftar</a>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Bantuan</div>
                <a href="#faq">FAQ</a>
                <a href="https://wa.me/62895356753500" target="_blank" rel="noopener">WhatsApp</a>
                <a href="/cdn-cgi/l/email-protection#c6bdbde6a5a9a8a0afa1eee1a5a9a8b2a7a5b2e8a3aba7afaae1eae6e1b5b3b6b6a9b4b286a3a2b3a0afa8a7a8a5a3e8afa2e1efe6bbbb">Email</a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="footer-copy">© {{ date('Y') }} EduFinance. All rights reserved.</div>
        <div class="footer-love">Made with ❤ for Indonesian Administration</div>
    </div>
</footer>

<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
/* ══════════════════════════════════════════════
   GSAP SETUP
══════════════════════════════════════════════ */
gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);

/* ── CURSOR ─────────────────────────────────────────────────────────── */
const cursor = document.getElementById('cursor');
const ring   = document.getElementById('cursorRing');
let mx = 0, my = 0, rx = 0, ry = 0;

document.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; });
(function animCursor() {
    cursor.style.left = mx + 'px'; cursor.style.top = my + 'px';
    rx += (mx - rx) * .11; ry += (my - ry) * .11;
    ring.style.left = rx + 'px'; ring.style.top = ry + 'px';
    requestAnimationFrame(animCursor);
})();
document.querySelectorAll('a, button, .faq-item, .feat, .price-card').forEach(el => {
    el.addEventListener('mouseenter', () => { cursor.classList.add('hovered'); ring.classList.add('hovered'); });
    el.addEventListener('mouseleave', () => { cursor.classList.remove('hovered'); ring.classList.remove('hovered'); });
});

/* ── LOADER ─────────────────────────────────────────────────────────── */
const loaderBar  = document.getElementById('loaderBar');
const loaderPct  = document.getElementById('loaderPct');
const loaderIcon = document.getElementById('loaderIcon');
const loaderLogo = document.getElementById('loaderLogo');
const loaderSub  = document.getElementById('loaderSub');
const curtain    = document.getElementById('loaderCurtain');
const loader     = document.getElementById('loader');

const introTl = gsap.timeline({ paused: true });
introTl
    .to(loaderIcon, { opacity: 1, scale: 1, duration: .7, ease: 'back.out(1.4)', delay: .1 })
    .to(loaderLogo, { opacity: 1, y: 0,     duration: .7, ease: 'power3.out' }, '-=.3')
    .to(loaderSub,  { opacity: 1,            duration: .5, ease: 'power2.out' }, '-=.3')
    .to(loaderPct,  { opacity: 1,            duration: .3 }, '-=.2');

let pct = 0;
const ticker = setInterval(() => {
    pct += Math.random() * 14 + 4;
    if (pct >= 100) { pct = 100; clearInterval(ticker); }
    loaderBar.style.width = pct + '%';
    loaderPct.textContent = Math.floor(pct) + '%';
}, 75);

setTimeout(() => {
    clearInterval(ticker);
    loaderBar.style.width = '100%';
    loaderPct.textContent = '100%';
    setTimeout(exitLoader, 300);
}, 2400);

introTl.play();

function exitLoader() {
    gsap.to(curtain, {
        scaleY: 1, duration: .65, ease: 'power3.inOut',
        onComplete: () => { loader.style.display = 'none'; heroEntrance(); }
    });
}

/* ── HERO ENTRANCE ──────────────────────────────────────────────────── */
function heroEntrance() {
    gsap.to('#heroBadge',         { opacity: 1, y: 0, duration: .8, ease: 'power3.out', delay: .05 });
    gsap.to('#heroFloatingNum',   { opacity: 1, y: 0, duration: .8, ease: 'power3.out', delay: .15 });
    gsap.to('.hero-h1-inner',     { y: 0, duration: 1.05, ease: 'power3.out', stagger: .1, delay: .1 });
    gsap.to('#heroBottom',        { opacity: 1, duration: .9, ease: 'power3.out', delay: .75 });
    gsap.to('#heroScroll',        { opacity: 1, duration: .7, ease: 'power2.out', delay: 1.2 });
    setTimeout(initStats, 900);
}

/* ── STATS COUNTER ──────────────────────────────────────────────────── */
function initStats() {
    const raw = {
        sekolah:   parseInt('{{ $totalSekolah }}')                  || 0,
        transaksi: parseInt('{{ intval($totalTransaksi / 1000) }}') || 0,
        bulan:     parseInt('{{ $totalBulanIni }}')                 || 0,
    };
    ['sb1','sb2','sb3'].forEach((id, i) => {
        const el = document.getElementById(id);
        setTimeout(() => { el.style.cssText += 'opacity:1;transform:translateY(0);transition:opacity .7s ease,transform .7s ease'; }, i * 110);
    });
    animCount('cnt-sekolah',   raw.sekolah,   1400);
    animCount('cnt-transaksi', raw.transaksi,  1400);
    animCount('cnt-bulan',     raw.bulan,      1400);
}
function animCount(id, target, dur) {
    const el = document.getElementById(id);
    if (!el || !target) return;
    const start = performance.now();
    (function tick(now) {
        const t = Math.min(1, (now - start) / dur);
        el.textContent = Math.floor((1 - Math.pow(1 - t, 4)) * target).toLocaleString('id');
        if (t < 1) requestAnimationFrame(tick);
    })(performance.now());
}

/* ── PARALLAX + NAV + INFINITE LOOP ────────────────────────────────── */
gsap.to('#heroOrb', { y: '25%', ease: 'none', scrollTrigger: { trigger: '#hero', start: 'top top', end: 'bottom top', scrub: 1.5 } });

window.addEventListener('scroll', () => {
    const y = window.scrollY;
    document.getElementById('mainNav').classList.toggle('scrolled', y > 60);
});

/* ── SCROLL REVEAL ──────────────────────────────────────────────────── */
new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); } });
}, { threshold: .12, rootMargin: '0px 0px -50px 0px' })
.observe = (() => {
    const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
    }, { threshold: .12, rootMargin: '0px 0px -50px 0px' });
    document.querySelectorAll('.reveal, .reveal-left').forEach(el => obs.observe(el));
    return obs.observe.bind(obs);
})();

/* ── CTA ENTRANCE ───────────────────────────────────────────────────── */
ScrollTrigger.create({
    trigger: '#cta-finale', start: 'top 72%',
    onEnter: () => {
        gsap.to('#ctaTag',     { opacity: 1, y: 0, duration: .7, ease: 'power3.out' });
        gsap.to('#ctaH2',      { opacity: 1, y: 0, duration: .9, ease: 'power3.out', delay: .12 });
        gsap.to('#ctaActions', { opacity: 1, y: 0, duration: .7, ease: 'power3.out', delay: .26 });
    }
});

/* ── SMOOTH ANCHOR SCROLL ───────────────────────────────────────────── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const t = document.querySelector(a.getAttribute('href'));
        if (!t) return;
        e.preventDefault();
        gsap.to(window, { duration: 1.1, scrollTo: { y: t, offsetY: 72 }, ease: 'power3.inOut' });
    });
});

/* ── FAQ ACCORDION ──────────────────────────────────────────────────── */
document.querySelectorAll('.faq-item').forEach(item => {
    item.querySelector('.faq-q').addEventListener('click', () => {
        const isOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item.open').forEach(o => o.classList.remove('open'));
        if (!isOpen) item.classList.add('open');
    });
});

/* ── DRAG-TO-SCROLL — JARINGAN INSTANSI ─────────────────────────────── */
(function initSchoolsDrag() {
    document.querySelectorAll('.schools-strip').forEach(strip => {
        let isDragging = false;
        let startX     = 0;
        let currentX   = 0;

        function getLiveTranslateX() {
            const mat = new DOMMatrix(window.getComputedStyle(strip).transform);
            return mat.m41;
        }

        function startDrag(pageX) {
            isDragging = true;
            const liveX = getLiveTranslateX();
            strip.style.animationPlayState = 'paused';
            strip.style.transform = 'translateX(' + liveX + 'px)';
            currentX = liveX;
            startX   = pageX;
            strip.style.cursor = 'grabbing';
        }

        function moveDrag(pageX) {
            if (!isDragging) return;
            const delta = pageX - startX;
            strip.style.transform = 'translateX(' + (currentX + delta) + 'px)';
        }

        function endDrag(pageX) {
            if (!isDragging) return;
            isDragging = false;
            currentX  += pageX - startX;
            strip.style.cursor = 'grab';
            const totalW   = strip.scrollWidth / 2;
            const duration = strip.classList.contains('schools-strip-r') ? 42000 : 38000;
            let delay;
            if (strip.classList.contains('schools-strip-r')) {
                delay = -(((-currentX) % totalW + totalW) % totalW) / totalW * duration / 1000;
            } else {
                delay = -(((currentX * -1) % totalW + totalW) % totalW) / totalW * duration / 1000;
            }
            strip.style.animationDelay     = delay + 's';
            strip.style.animationPlayState = 'running';
            strip.style.transform          = '';
        }

        strip.addEventListener('mousedown', e => { startDrag(e.pageX); e.preventDefault(); });
        document.addEventListener('mousemove', e => { if (isDragging) moveDrag(e.pageX); });
        document.addEventListener('mouseup',   e => { if (isDragging) endDrag(e.pageX); });

        strip.addEventListener('touchstart', e => { startDrag(e.touches[0].pageX); }, { passive: true });
        strip.addEventListener('touchmove',  e => { moveDrag(e.touches[0].pageX); },  { passive: true });
        strip.addEventListener('touchend',   e => { endDrag(e.changedTouches[0].pageX); });

        strip.style.cursor = 'grab';
    });
})();

/* ── MOBILE MENU TOGGLE ────────────────────────────────────────────── */
const navToggle = document.getElementById('navToggle');
const navMobile = document.getElementById('navMobile');

navToggle.addEventListener('click', () => {
    navToggle.classList.toggle('active');
    navMobile.classList.toggle('active');
});

function closeMobileMenu() {
    navToggle.classList.remove('active');
    navMobile.classList.remove('active');
}

document.addEventListener('click', (e) => {
    if (!e.target.closest('nav') && !e.target.closest('.nav-mobile')) {
        closeMobileMenu();
    }
});
</script>
</body>
</html>