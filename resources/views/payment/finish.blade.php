<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFinance — Status Pembayaran</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600&family=Outfit:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --white:      #ffffff;
            --offwhite:   #f7f8fc;
            --blue:       #1246a0;
            --blue-mid:   #1a5bc4;
            --blue-lt:    #3b82f6;
            --blue-pale:  #dde9ff;
            --ink:        #0d1b35;
            --muted:      #6b7a99;
            --border:     rgba(18,70,160,0.10);
            --border-lt:  rgba(18,70,160,0.06);
            --success:    #15803d;
            --success-bg: #f0fdf4;
            --warn:       #92400e;
            --warn-bg:    #fffbeb;
            --danger:     #b91c1c;
            --danger-bg:  #fef2f2;
            --f-display: 'Cormorant Garamond', Georgia, serif;
            --f-body:    'Outfit', system-ui, sans-serif;
            --f-mono:    'DM Mono', monospace;
            --ease-out:    cubic-bezier(0.16, 1, 0.3, 1);
            --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        body {
            font-family: var(--f-body);
            background: var(--white);
            color: var(--ink);
            line-height: 1.6;
            overflow-x: hidden;
            cursor: none;
            min-height: 100vh;
        }

        /* ─── CURSOR ─────────────────────────────────────── */
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
        .cursor.hovered { width: 16px; height: 16px; }
        .cursor-ring.hovered { width: 54px; height: 54px; opacity: .25; }
        a, button { cursor: none; }

        /* ─── NAV ────────────────────────────────────────── */
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
        .nav-logo {
            display: flex; align-items: center; gap: 8px;
            text-decoration: none; flex-shrink: 0;
        }
        .nav-logo-text {
            font-family: var(--f-display); font-size: 17px; font-weight: 700;
            color: white; letter-spacing: -.02em; transition: color .4s;
        }
        nav.scrolled .nav-logo-text { color: var(--ink); }
        .nav-links { display: flex; align-items: center; gap: 2px; }
        .nav-links a {
            font-size: 13px; color: rgba(255,255,255,.72); text-decoration: none;
            letter-spacing: .01em; padding: 7px 13px; border-radius: 100px;
            transition: color .25s, background .25s; white-space: nowrap;
        }
        .nav-links a:hover { color: white; background: rgba(255,255,255,.12); }
        nav.scrolled .nav-links a { color: var(--muted); }
        nav.scrolled .nav-links a:hover { color: var(--ink); background: rgba(18,70,160,.07); }
        .nav-wa {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 13px; color: rgba(255,255,255,.72); text-decoration: none;
            padding: 7px 13px; border-radius: 100px; letter-spacing: .01em;
            transition: color .25s, background .25s;
        }
        .nav-wa:hover { color: white; background: rgba(255,255,255,.12); }
        nav.scrolled .nav-wa { color: var(--muted); }
        nav.scrolled .nav-wa:hover { color: var(--ink); background: rgba(18,70,160,.07); }
        .nav-wa svg { flex-shrink: 0; }
        .nav-divider {
            width: 1px; height: 18px; background: rgba(255,255,255,.18);
            margin: 0 4px; flex-shrink: 0; transition: background .4s;
        }
        nav.scrolled .nav-divider { background: rgba(18,70,160,.15); }
        .nav-cta {
            display: inline-flex !important; align-items: center; gap: 7px;
            background: white !important; color: var(--blue) !important;
            padding: 9px 22px !important; border-radius: 100px !important;
            font-weight: 600 !important; font-size: 13px !important; letter-spacing: .01em;
            box-shadow: 0 2px 14px rgba(0,0,0,.15);
            transition: background .3s, transform .3s var(--ease-out), box-shadow .3s !important;
        }
        .nav-cta::after { content: '→'; font-size: 12px; transition: transform .3s var(--ease-out); display: inline-block; }
        .nav-cta:hover { background: var(--blue-pale) !important; transform: translateY(-1px) !important; box-shadow: 0 6px 20px rgba(18,70,160,.22) !important; }
        .nav-cta:hover::after { transform: translateX(3px); }
        nav.scrolled .nav-cta { background: var(--blue) !important; color: white !important; box-shadow: 0 4px 16px rgba(18,70,160,.3) !important; }
        nav.scrolled .nav-cta:hover { background: var(--blue-mid) !important; }
        @media (max-width: 1024px) { nav { width: calc(100% - 48px); padding: 0 8px 0 16px; } }
        @media (max-width: 700px) {
            nav { width: calc(100% - 32px); top: 12px; padding: 0 6px 0 16px; }
            .nav-links a:not(.nav-cta):not(.nav-wa) { display: none; }
            .nav-divider { display: none; }
        }

        /* ─── HERO ───────────────────────────────────────── */
        .finish-hero {
            background: linear-gradient(135deg, #1e3a8a, #1d4ed8 60%, #0284c7);
            padding: 110px 52px 80px; text-align: center; position: relative; overflow: hidden;
        }
        .finish-hero::before {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(ellipse 60% 60% at 70% 40%, rgba(255,255,255,.05) 0%, transparent 70%);
            pointer-events: none;
        }
        .finish-hero .badge-top {
            display: inline-block; background: rgba(255,255,255,.15); backdrop-filter: blur(5px);
            color: #fff; font-size: .8rem; font-weight: 500; padding: 6px 16px; border-radius: 50px;
            letter-spacing: .5px; margin-bottom: 24px; border: 1px solid rgba(255,255,255,.2);
            font-family: var(--f-mono);
        }
        .finish-hero h1 {
            font-family: var(--f-display); font-size: clamp(44px,6vw,72px); font-weight: 600;
            color: white; line-height: .95; letter-spacing: -.03em; margin-bottom: 16px;
        }
        .finish-hero h1 em {
            font-style: italic; font-weight: 300;
            background: linear-gradient(135deg,#93c5fd 0%,#60a5fa 40%,#3b82f6 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .finish-hero p { color: rgba(255,255,255,.75); font-size: 1rem; font-weight: 300; }

        /* ─── BREADCRUMB ─────────────────────────────────── */
        .finish-breadcrumb {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 20px; background: var(--offwhite); border-bottom: 1px solid var(--border-lt);
            font-family: var(--f-mono); font-size: 11px; letter-spacing: .15em;
            text-transform: uppercase; color: var(--muted);
        }
        .finish-breadcrumb .step { color: var(--muted); }
        .finish-breadcrumb .step.done { color: var(--success); }
        .finish-breadcrumb .step.active { color: var(--blue); font-weight: 500; }
        .finish-breadcrumb .sep { color: var(--border); }

        /* ─── BODY ───────────────────────────────────────── */
        .finish-body {
            padding: 64px 52px 80px; background: var(--white);
            display: flex; flex-direction: column; align-items: center;
        }

        .order-tag {
            font-family: var(--f-mono); font-size: 11px; letter-spacing: .2em;
            text-transform: uppercase; color: var(--muted); margin-bottom: 40px; text-align: center;
        }
        .order-tag code {
            font-family: var(--f-mono); background: var(--offwhite); padding: 3px 10px;
            border-radius: 4px; font-size: 11px; border: 1px solid var(--border-lt); color: var(--ink);
        }

        .status-card {
            width: 100%; max-width: 640px;
            border: 1px solid var(--border-lt); border-radius: 16px;
            overflow: hidden; background: white; transition: box-shadow .4s var(--ease-out);
        }
        .status-card:hover { box-shadow: 0 24px 56px rgba(18,70,160,.07); }

        .state-inner { padding: 56px 48px; text-align: center; }

        /* Spinner */
        .spinner-wrap { margin-bottom: 32px; }
        .spinner-ring {
            display: inline-block; width: 64px; height: 64px;
            border: 3px solid var(--border-lt); border-top-color: var(--blue);
            border-radius: 50%; animation: spin .8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .state-heading {
            font-family: var(--f-display); font-size: clamp(28px,3.5vw,40px); font-weight: 600;
            letter-spacing: -.02em; line-height: 1.05; color: var(--ink); margin-bottom: 12px;
        }
        .state-heading em { font-style: italic; font-weight: 300; color: var(--blue-mid); }
        .state-sub { font-size: 14px; color: var(--muted); margin-bottom: 24px; font-weight: 300; }

        /* Progress */
        .progress-track { height: 3px; background: var(--border-lt); border-radius: 2px; overflow: hidden; margin-bottom: 12px; }
        .progress-fill {
            height: 100%; background: linear-gradient(90deg, var(--blue), var(--blue-lt));
            border-radius: 2px; animation: progress-pulse 1.6s ease-in-out infinite; width: 100%;
        }
        @keyframes progress-pulse { 0%,100% { opacity:.7; } 50% { opacity:1; } }
        .attempt-label { font-family: var(--f-mono); font-size: 10px; letter-spacing: .15em; text-transform: uppercase; color: var(--muted); }

        /* Status icons */
        .status-icon {
            display: inline-flex; align-items: center; justify-content: center;
            width: 88px; height: 88px; border-radius: 50%; margin-bottom: 28px; font-size: 2.6rem;
        }
        .status-icon.success { background: var(--success-bg); animation: popIn .5s var(--ease-spring) forwards; }
        .status-icon.warning { background: var(--warn-bg); }
        .status-icon.danger  { background: var(--danger-bg); }
        @keyframes popIn { from { transform: scale(.5); opacity:0; } to { transform: scale(1); opacity:1; } }

        /* Badge band */
        .badge-band { display: flex; align-items: center; justify-content: center; gap: 10px; flex-wrap: wrap; margin: 20px 0; }
        .badge-pill { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 100px; font-size: 12.5px; font-weight: 500; border: 1px solid; }
        .badge-pill.green { background: var(--success-bg); color: var(--success); border-color: rgba(21,128,61,.2); }

        /* Info box */
        .info-box { border-radius: 10px; padding: 20px 24px; text-align: left; margin: 24px 0; display: flex; gap: 16px; align-items: flex-start; }
        .info-box.green { background: var(--success-bg); border: 1px solid rgba(21,128,61,.12); }
        .info-box.amber { background: var(--warn-bg);    border: 1px solid rgba(146,64,14,.12); }
        .info-box.red   { background: var(--danger-bg);  border: 1px solid rgba(185,28,28,.12); }
        .info-box-icon { font-size: 1.6rem; flex-shrink: 0; margin-top: 2px; }
        .info-box-title { font-weight: 600; font-size: 14px; margin-bottom: 4px; }
        .info-box.green .info-box-title { color: var(--success); }
        .info-box.amber .info-box-title { color: var(--warn); }
        .info-box.red   .info-box-title { color: var(--danger); }
        .info-box-text { font-size: 13px; color: var(--muted); line-height: 1.6; }

        /* Steps */
        .steps-block { background: var(--offwhite); border: 1px solid var(--border-lt); border-radius: 10px; padding: 20px 24px; text-align: left; margin: 0 0 28px; }
        .steps-block-title { font-family: var(--f-mono); font-size: 9.5px; letter-spacing: .22em; text-transform: uppercase; color: var(--muted); margin-bottom: 14px; }
        .steps-list { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .steps-list li { display: flex; align-items: flex-start; gap: 12px; font-size: 13.5px; color: var(--muted); line-height: 1.5; }
        .steps-list li .num { display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 50%; background: var(--blue); color: white; font-size: 10px; font-family: var(--f-mono); flex-shrink: 0; margin-top: 1px; }

        /* Buttons */
        .btn-primary { display: inline-flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 16px 24px; background: var(--blue); color: white; border: none; border-radius: 8px; font-family: var(--f-body); font-size: 15px; font-weight: 500; text-decoration: none; transition: background .3s, transform .3s var(--ease-out), box-shadow .3s; margin-bottom: 16px; }
        .btn-primary:hover { background: var(--blue-mid); color: white; transform: translateY(-2px); box-shadow: 0 12px 32px rgba(18,70,160,.28); }
        .btn-primary .arrow { transition: transform .3s var(--ease-spring); }
        .btn-primary:hover .arrow { transform: translateX(4px); }

        .btn-outline { display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 13px 24px; border: 1.5px solid var(--border); border-radius: 8px; font-family: var(--f-body); font-size: 14px; font-weight: 500; text-decoration: none; color: var(--blue); background: transparent; transition: all .3s var(--ease-out); }
        .btn-outline:hover { background: var(--blue); border-color: var(--blue); color: white; transform: translateY(-1px); }

        .login-hint { text-align: center; font-size: 13px; color: var(--muted); }
        .login-hint a { color: var(--blue); font-weight: 500; text-decoration: none; }
        .login-hint a:hover { text-decoration: underline; }

        .divider { height: 1px; background: var(--border-lt); margin: 32px 0; }

        .status-label-box { background: var(--danger-bg); border: 1px solid rgba(185,28,28,.15); border-radius: 8px; padding: 12px 20px; font-family: var(--f-mono); font-size: 13px; color: var(--danger); text-align: center; margin: 0 0 28px; }

        /* Reveal */
        .reveal { opacity: 0; transform: translateY(20px); transition: opacity .7s var(--ease-out), transform .7s var(--ease-out); }
        .reveal.visible { opacity: 1; transform: none; }

        @media (max-width: 720px) {
            .finish-body { padding: 40px 20px 60px; }
            .finish-hero { padding: 100px 24px 60px; }
            .state-inner { padding: 40px 28px; }
        }

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
        .wa-float:hover .wa-float-btn { transform: scale(1.08); box-shadow: 0 10px 36px rgba(37,211,102,.5); }
        .wa-float-btn svg { color: white; }
        .wa-pulse {
            position: absolute; top: 0; right: 0; width: 14px; height: 14px;
            background: #4ade80; border-radius: 50%; border: 2px solid white;
            animation: waPulse 2s ease-out infinite;
        }
        @keyframes waPulse {
            0%   { box-shadow: 0 0 0 0 rgba(74,222,128,.6); }
            70%  { box-shadow: 0 0 0 8px rgba(74,222,128,0); }
            100% { box-shadow: 0 0 0 0 rgba(74,222,128,0); }
        }
        @media (max-width: 768px) { .wa-float { bottom: 20px; right: 20px; } .wa-float-btn { width: 48px; height: 48px; } }
    </style>
</head>
<body>

<div id="cursor" class="cursor"></div>
<div id="cursorRing" class="cursor-ring"></div>

<a href="https://wa.me/62895356753500" target="_blank" class="wa-float" rel="noopener noreferrer" title="Hubungi via WhatsApp">
    <span class="wa-float-label">💬 Hubungi Kami via WA</span>
    <div class="wa-float-btn" style="position:relative;">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        <div class="wa-pulse"></div>
    </div>
</a>

<nav id="mainNav">
    <a href="/" class="nav-logo">
        <span class="nav-logo-text">EduFinance</span>
    </a>
    <div class="nav-links">
        <a href="/#fitur">Fitur</a>
        <a href="/#kolaborasi">Instansi</a>
        <a href="/#testimonial">Testimonial</a>
        <a href="{{ route('pricing') }}">Harga</a>
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
</nav>

{{-- HERO --}}
<div class="finish-hero">
    <div class="badge-top" id="hero-badge">VERIFIKASI</div>
    <h1 id="hero-title">Mengecek<br><em>Pembayaran</em></h1>
    <p id="hero-sub">Sistem sedang mengkonfirmasi transaksi Anda</p>
</div>

{{-- BREADCRUMB --}}
<div class="finish-breadcrumb">
    <span class="step done">01 Pilih Paket</span>
    <span class="sep">——</span>
    <span class="step done">02 Isi Data</span>
    <span class="sep">——</span>
    <span class="step done">03 Bayar</span>
    <span class="sep">——</span>
    <span class="step active">04 Selesai</span>
</div>

{{-- BODY --}}
<div class="finish-body">

    <div class="order-tag reveal">
        Order ID: <code>{{ $order_id }}</code>
    </div>

    <div class="status-card reveal" style="transition-delay:.05s;">

        {{-- CHECKING --}}
        <div id="state-checking" class="state-inner">
            <div class="spinner-wrap"><span class="spinner-ring"></span></div>
            <h2 class="state-heading">Memverifikasi<br><em>Pembayaran…</em></h2>
            <p class="state-sub">Mohon tunggu, sistem sedang mengkonfirmasi<br>pembayaran Anda secara otomatis.</p>
            <div class="progress-track"><div class="progress-fill"></div></div>
            <div class="attempt-label">Cek ke-<span id="attempt-count">1</span> / 24</div>
        </div>

        {{-- SUCCESS --}}
        <div id="state-success" class="state-inner" style="display:none;">
            <div class="status-icon success">🎉</div>
            <h2 class="state-heading">Pembayaran<br><em>Berhasil!</em></h2>
            <p class="state-sub">Terima kasih — transaksi Anda telah terkonfirmasi.</p>
            <div class="badge-band">
                <span class="badge-pill green">📧 Email Terkirim</span>
                <span class="badge-pill green">💬 WhatsApp Terkirim</span>
            </div>
            <div class="info-box green">
                <span class="info-box-icon">📬</span>
                <div class="info-box-body">
                    <div class="info-box-title">Kode lisensi sudah dikirim!</div>
                    <div class="info-box-text">
                        Cek <strong>inbox atau folder spam</strong> email Anda.
                        Pesan WhatsApp dari Fonnte biasanya tiba dalam beberapa detik.
                    </div>
                </div>
            </div>
            <div class="divider"></div>
            <div class="steps-block">
                <div class="steps-block-title">📋 Langkah Selanjutnya</div>
                <ol class="steps-list">
                    <li><span class="num">1</span> Buka email / WhatsApp — cari kode lisensi</li>
                    <li><span class="num">2</span> Buka halaman <strong>Daftar Akun</strong> di bawah</li>
                    <li><span class="num">3</span> Isi Nama Instansi, Email, dan Password</li>
                    <li><span class="num">4</span> Masukkan <strong>Kode Lisensi</strong> dari email/WA</li>
                    <li><span class="num">5</span> Klik <strong>Buat Akun</strong> — selesai!</li>
                </ol>
            </div>
            <a href="{{ route('register') }}" class="btn-primary">
                Daftar Akun Sekarang <span class="arrow">→</span>
            </a>
            <div class="login-hint">
                Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
            </div>
        </div>

        {{-- PENDING --}}
        <div id="state-pending" class="state-inner" style="display:none;">
            <div class="status-icon warning">⏳</div>
            <h2 class="state-heading">Menunggu<br><em>Konfirmasi Bank</em></h2>
            <p class="state-sub">Pembayaran sedang diverifikasi oleh bank Anda.</p>
            <div class="info-box amber">
                <span class="info-box-icon">ℹ️</span>
                <div class="info-box-body">
                    <div class="info-box-title">Hampir selesai!</div>
                    <div class="info-box-text">
                        Begitu terkonfirmasi, kode lisensi akan <strong>otomatis dikirim</strong>
                        ke email & WhatsApp Anda. Biasanya 1–5 menit.
                    </div>
                </div>
            </div>
            <div class="divider"></div>
            <button onclick="restartPolling()" class="btn-outline" style="width:100%;">
                ↻ Cek Ulang Status
            </button>
        </div>

        {{-- FAILED --}}
        <div id="state-failed" class="state-inner" style="display:none;">
            <div class="status-icon danger">✕</div>
            <h2 class="state-heading">Pembayaran<br><em>Tidak Berhasil</em></h2>
            <p class="state-sub">Transaksi tidak dapat diproses.</p>
            <div class="status-label-box">
                Status: <strong id="failed-status-label">—</strong>
            </div>
            <div class="info-box red">
                <span class="info-box-icon">⚠️</span>
                <div class="info-box-body">
                    <div class="info-box-title">Transaksi gagal atau kadaluarsa</div>
                    <div class="info-box-text">
                        Tidak ada dana yang dibebankan. Silakan coba kembali dari halaman paket.
                    </div>
                </div>
            </div>
            <div class="divider"></div>
            <a href="{{ route('pricing') }}" class="btn-primary">
                ↩ Kembali ke Halaman Paket <span class="arrow">→</span>
            </a>
        </div>

    </div>
</div>

<script>
    // ─── NAV ─────────────────────────────────────────────
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll', () => nav.classList.toggle('scrolled', window.scrollY > 60));

    // ─── CURSOR ──────────────────────────────────────────
    const cursor = document.getElementById('cursor');
    const ring   = document.getElementById('cursorRing');
    let mx = 0, my = 0, rx = 0, ry = 0;
    document.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; });
    (function animCursor() {
        cursor.style.left = mx + 'px'; cursor.style.top = my + 'px';
        rx += (mx - rx) * 0.11; ry += (my - ry) * 0.11;
        ring.style.left = rx + 'px'; ring.style.top = ry + 'px';
        requestAnimationFrame(animCursor);
    })();
    document.querySelectorAll('a, button, .status-card').forEach(el => {
        el.addEventListener('mouseenter', () => { cursor.classList.add('hovered'); ring.classList.add('hovered'); });
        el.addEventListener('mouseleave', () => { cursor.classList.remove('hovered'); ring.classList.remove('hovered'); });
    });

    // ─── REVEAL ──────────────────────────────────────────
    const revealObs = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); revealObs.unobserve(e.target); } });
    }, { threshold: .1 });
    document.querySelectorAll('.reveal').forEach(el => revealObs.observe(el));

    // ─── POLLING ─────────────────────────────────────────
    (function () {
        'use strict';
        const ORDER_ID     = @json($order_id);
        const CHECK_URL    = @json(route('payment.check-status', ':id')).replace(':id', encodeURIComponent(ORDER_ID));
        const MAX_ATTEMPTS = 24;
        const INTERVAL_MS  = 5000;
        let attempts = 0, pollTimer = null;

        const heroTitle = document.getElementById('hero-title');
        const heroBadge = document.getElementById('hero-badge');
        const heroSub   = document.getElementById('hero-sub');

        function showState(name) {
            ['checking','success','pending','failed'].forEach(s => {
                document.getElementById('state-' + s).style.display = (s === name) ? '' : 'none';
            });
            if (name === 'success') {
                heroBadge.textContent = 'SELESAI';
                heroTitle.innerHTML = 'Pembayaran<br><em>Berhasil!</em>';
                heroSub.textContent = 'Kode lisensi telah dikirim ke email & WhatsApp Anda';
            } else if (name === 'pending') {
                heroBadge.textContent = 'MENUNGGU';
                heroTitle.innerHTML = 'Menunggu<br><em>Konfirmasi</em>';
                heroSub.textContent = 'Pembayaran Anda sedang diproses oleh bank';
            } else if (name === 'failed') {
                heroBadge.textContent = 'GAGAL';
                heroTitle.innerHTML = 'Transaksi<br><em>Tidak Berhasil</em>';
                heroSub.textContent = 'Tidak ada dana yang dibebankan';
            }
        }

        function checkStatus() {
            attempts++;
            const el = document.getElementById('attempt-count');
            if (el) el.textContent = attempts;
            fetch(CHECK_URL, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                .then(data => {
                    const s = (data.status || '').toLowerCase();
                    if (s === 'settlement' || s === 'capture') {
                        clearInterval(pollTimer); showState('success'); return;
                    }
                    if (['expire','cancel','deny'].includes(s)) {
                        clearInterval(pollTimer);
                        document.getElementById('failed-status-label').textContent =
                            { expire:'Kadaluarsa', cancel:'Dibatalkan', deny:'Ditolak' }[s] || s;
                        showState('failed'); return;
                    }
                    if (attempts >= MAX_ATTEMPTS) { clearInterval(pollTimer); showState('pending'); }
                })
                .catch(() => {
                    if (attempts >= MAX_ATTEMPTS) { clearInterval(pollTimer); showState('pending'); }
                });
        }

        window.restartPolling = function () {
            attempts = 0; showState('checking'); clearInterval(pollTimer);
            checkStatus(); pollTimer = setInterval(checkStatus, INTERVAL_MS);
        };

        restartPolling();
    })();
</script>

</body>
</html>