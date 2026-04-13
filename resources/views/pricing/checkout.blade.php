<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFinance — Checkout {{ $package['name'] ?? '' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600&family=Outfit:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        /* SweetAlert2 custom theme */
        .swal2-popup {
            font-family: 'Outfit', system-ui, sans-serif !important;
            border-radius: 14px !important;
            padding: 36px 32px 28px !important;
            box-shadow: 0 32px 64px rgba(18,70,160,.12) !important;
        }
        .swal2-title {
            font-family: 'Cormorant Garamond', Georgia, serif !important;
            font-size: 26px !important;
            font-weight: 600 !important;
            color: #0d1b35 !important;
            letter-spacing: -.02em !important;
        }
        .swal2-confirm {
            font-family: 'Outfit', system-ui, sans-serif !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            padding: 12px 28px !important;
            border-radius: 8px !important;
        }

        :root {
            --white:     #ffffff;
            --offwhite:  #f7f8fc;
            --blue:      #1246a0;
            --blue-mid:  #1a5bc4;
            --blue-lt:   #3b82f6;
            --blue-pale: #dde9ff;
            --ink:       #0d1b35;
            --muted:     #6b7a99;
            --border:    rgba(18,70,160,0.10);
            --border-lt: rgba(18,70,160,0.06);
            --success:   #15803d;
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

        /* CURSOR */
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

        /* NAV */
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

        /* HERO */
        .checkout-hero {
            background: linear-gradient(135deg, #1e3a8a, #1d4ed8 60%, #0284c7);
            padding: 110px 52px 80px; text-align: center; position: relative; overflow: hidden;
        }
        .checkout-hero::before {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(ellipse 60% 60% at 70% 40%, rgba(255,255,255,.05) 0%, transparent 70%);
            pointer-events: none;
        }
        .checkout-hero .badge-top {
            display: inline-block; background: rgba(255,255,255,.15); backdrop-filter: blur(5px);
            color: #fff; font-size: .8rem; font-weight: 500; padding: 6px 16px; border-radius: 50px;
            letter-spacing: .5px; margin-bottom: 24px; border: 1px solid rgba(255,255,255,.2);
            font-family: var(--f-mono);
        }
        .checkout-hero h1 {
            font-family: var(--f-display); font-size: clamp(44px, 6vw, 72px); font-weight: 600;
            color: white; line-height: .95; letter-spacing: -.03em; margin-bottom: 16px;
        }
        .checkout-hero h1 em {
            font-style: italic; font-weight: 300;
            background: linear-gradient(135deg, #93c5fd 0%, #60a5fa 40%, #3b82f6 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .checkout-hero p { color: rgba(255,255,255,.75); font-size: 1rem; font-weight: 300; }

        /* BREADCRUMB */
        .checkout-breadcrumb {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 20px; background: var(--offwhite); border-bottom: 1px solid var(--border-lt);
            font-family: var(--f-mono); font-size: 11px; letter-spacing: .15em; text-transform: uppercase; color: var(--muted);
        }
        .checkout-breadcrumb .step { color: var(--muted); }
        .checkout-breadcrumb .step.active { color: var(--blue); font-weight: 500; }
        .checkout-breadcrumb .sep { color: var(--border); }

        /* BODY */
        .checkout-body { padding: 64px 52px 80px; background: var(--white); }
        .checkout-grid {
            max-width: 1020px; margin: 0 auto;
            display: grid; grid-template-columns: 1fr 420px; gap: 48px; align-items: start;
        }

        /* SUMMARY */
        .summary-panel { position: sticky; top: 96px; }
        .summary-tag {
            display: inline-flex; align-items: center; gap: 10px;
            font-family: var(--f-mono); font-size: 10px; letter-spacing: .28em;
            text-transform: uppercase; color: var(--blue); margin-bottom: 20px;
        }
        .summary-tag::before { content: ''; width: 24px; height: 1px; background: var(--blue); opacity: .4; }
        .summary-card {
            border: 1px solid var(--border-lt); border-radius: 14px; overflow: hidden;
            background: white; transition: box-shadow .4s var(--ease-out);
        }
        .summary-card:hover { box-shadow: 0 24px 56px rgba(18,70,160,.08); }
        .summary-card-head { background: linear-gradient(145deg, var(--blue) 0%, #0f3a88 100%); padding: 32px 32px 28px; }
        .summary-package-type { font-family: var(--f-mono); font-size: 9.5px; letter-spacing: .22em; text-transform: uppercase; color: rgba(255,255,255,.4); margin-bottom: 12px; }
        .summary-package-name { font-family: var(--f-display); font-size: clamp(32px,3.5vw,44px); font-weight: 600; color: white; letter-spacing: -.02em; line-height: 1; margin-bottom: 8px; }
        .summary-price { font-family: var(--f-display); font-size: clamp(38px,4vw,52px); font-weight: 600; color: white; letter-spacing: -.03em; line-height: 1; }
        .summary-period { font-size: 13px; color: rgba(255,255,255,.4); margin-top: 4px; }
        .summary-card-body { padding: 28px 32px 32px; }
        .summary-features { list-style: none; display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px; }
        .summary-features li { display: flex; align-items: flex-start; gap: 10px; font-size: 13.5px; color: var(--muted); }
        .summary-features li::before {
            content: ''; width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px;
            background: url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='8' cy='8' r='7.5' stroke='%231246a0' stroke-opacity='0.2'/%3E%3Cpath d='M5 8l2 2 4-4' stroke='%231246a0' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") center/contain no-repeat;
        }
        .summary-divider { height: 1px; background: var(--border-lt); margin-bottom: 20px; }
        .summary-secure { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); }
        .summary-secure .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--success); flex-shrink: 0; }
        .summary-secure strong { color: var(--ink); }
        .back-link {
            display: inline-flex; align-items: center; gap: 6px; margin-top: 20px;
            font-size: 13px; color: var(--muted); text-decoration: none;
            font-family: var(--f-mono); letter-spacing: .05em; transition: color .3s;
        }
        .back-link:hover { color: var(--blue); }
        .back-link::before { content: '←'; }

        /* FORM */
        .form-title-tag {
            display: inline-flex; align-items: center; gap: 10px;
            font-family: var(--f-mono); font-size: 10px; letter-spacing: .28em;
            text-transform: uppercase; color: var(--blue); margin-bottom: 20px;
        }
        .form-title-tag::before { content: ''; width: 24px; height: 1px; background: var(--blue); opacity: .4; }
        .form-heading { font-family: var(--f-display); font-size: clamp(32px,4vw,48px); font-weight: 600; letter-spacing: -.03em; line-height: 1; color: var(--ink); margin-bottom: 36px; }
        .form-heading em { font-style: italic; font-weight: 300; color: var(--blue-mid); }
        .field-group { margin-bottom: 24px; }
        .field-label { display: block; font-size: 12px; font-family: var(--f-mono); letter-spacing: .15em; text-transform: uppercase; color: var(--muted); margin-bottom: 8px; }
        .field-label .req { color: #e55; margin-left: 2px; }
        .field-input {
            width: 100%; padding: 14px 18px; border: 1.5px solid var(--border); border-radius: 8px;
            font-family: var(--f-body); font-size: 15px; color: var(--ink); background: white;
            outline: none; transition: border-color .3s, box-shadow .3s; -webkit-appearance: none;
        }
        .field-input::placeholder { color: var(--muted); opacity: .6; }
        .field-input:focus { border-color: var(--blue-lt); box-shadow: 0 0 0 3px rgba(59,130,246,.12); }
        .field-hint { margin-top: 7px; font-size: 12px; color: var(--muted); display: flex; align-items: center; gap: 5px; }
        .btn-submit {
            width: 100%; padding: 16px 24px; background: var(--blue); color: white;
            border: none; border-radius: 8px; font-family: var(--f-body); font-size: 15px; font-weight: 500;
            cursor: none; display: flex; align-items: center; justify-content: center; gap: 10px;
            transition: background .3s, transform .3s var(--ease-out), box-shadow .3s; margin-top: 36px;
        }
        .btn-submit:hover { background: var(--blue-mid); transform: translateY(-2px); box-shadow: 0 12px 32px rgba(18,70,160,.28); }
        .btn-submit .arrow { transition: transform .3s var(--ease-spring); }
        .btn-submit:hover .arrow { transform: translateX(4px); }
        .form-error { margin-top: 20px; padding: 14px 18px; background: rgba(220,38,38,.06); border: 1px solid rgba(220,38,38,.15); border-radius: 8px; font-size: 13.5px; color: #b91c1c; display: none; }
        .form-error.visible { display: block; }

        /* REVEAL */
        .reveal { opacity: 0; transform: translateY(22px); transition: opacity .7s var(--ease-out), transform .7s var(--ease-out); }
        .reveal.visible { opacity: 1; transform: none; }

        @media (max-width: 900px) {
            .checkout-grid { grid-template-columns: 1fr; gap: 32px; }
            .summary-panel { position: static; }
            .checkout-body { padding: 40px 24px 60px; }
            .checkout-hero { padding: 100px 24px 60px; }
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

<div class="checkout-hero">
    <div class="badge-top">CHECKOUT</div>
    <h1>Satu Langkah<br><em>Lagi</em></h1>
    <p>Lengkapi data di bawah untuk melanjutkan pembayaran</p>
</div>

<div class="checkout-breadcrumb">
    <span class="step">01 Pilih Paket</span>
    <span class="sep">——</span>
    <span class="step active">02 Isi Data</span>
    <span class="sep">——</span>
    <span class="step">03 Bayar</span>
    <span class="sep">——</span>
    <span class="step">04 Selesai</span>
</div>

<div class="checkout-body">
    <div class="checkout-grid">

        {{-- KIRI: FORM --}}
        <div class="form-panel reveal">
            <div class="form-title-tag">Data Pembeli</div>
            <h2 class="form-heading">Isi <em>Informasi</em><br>Instansi Anda</h2>

            <form id="checkoutForm">
                @csrf
                <input type="hidden" name="package_type" value="{{ $package['type'] }}">

                <div class="field-group">
                    <label class="field-label">Nama Lengkap <span class="req">*</span></label>
                    <input type="text" class="field-input" name="buyer_name" placeholder="Nama perwakilan sekolah" required>
                </div>
                <div class="field-group">
                    <label class="field-label">Nama Instansi <span class="req">*</span></label>
                    <input type="text" class="field-input" name="school_name" placeholder="SDN / SMPN / SMAN ..." required>
                </div>
                <div class="field-group">
                    <label class="field-label">Alamat Email <span class="req">*</span></label>
                    <input type="email" class="field-input" name="email" placeholder="email@sekolah.sch.id" required>
                    <div class="field-hint"><span>📧</span> Kode lisensi akan dikirim ke email ini</div>
                </div>
                <div class="field-group">
                    <label class="field-label">No. WhatsApp <span class="req">*</span></label>
                    <input type="text" class="field-input" name="phone" placeholder="08123456789" required>
                    <div class="field-hint"><span>💬</span> Kode lisensi juga dikirim ke WhatsApp ini</div>
                </div>

                <div id="error-msg" class="form-error"></div>

                <button type="submit" class="btn-submit">
                    Lanjutkan ke Pembayaran <span class="arrow">→</span>
                </button>
            </form>
        </div>

        {{-- KANAN: SUMMARY --}}
        <div class="summary-panel reveal" style="transition-delay:.1s;">
            <div class="summary-tag">Paket Dipilih</div>
            <div class="summary-card">
                <div class="summary-card-head">
                    <div class="summary-package-type">{{ $package['type'] }}</div>
                    <div class="summary-package-name">{{ $package['name'] }}</div>
                    <div class="summary-price">Rp {{ number_format($package['price'], 0, ',', '.') }}</div>
                    <div class="summary-period">{{ $package['duration'] }}</div>
                </div>
                <div class="summary-card-body">
                    <ul class="summary-features">
                        @if(isset($package['features']) && count($package['features']))
                            @foreach($package['features'] as $f)<li>{{ $f }}</li>@endforeach
                        @else
                            <li>Akses penuh semua fitur</li>
                            <li>Support prioritas via WhatsApp</li>
                            <li>Update gratis</li>
                            <li>Backup data otomatis</li>
                            <li>Lisensi 1 perangkat aktif</li>
                        @endif
                    </ul>
                    <div class="summary-divider"></div>
                    <div class="summary-secure">
                        <span class="dot"></span>
                        Pembayaran aman diproses oleh <strong>Midtrans</strong>
                    </div>
                </div>
            </div>
            <a href="{{ route('pricing') }}" class="back-link">Kembali ke halaman paket</a>
        </div>

    </div>
</div>

<script>
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll', () => nav.classList.toggle('scrolled', window.scrollY > 60));

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
    document.querySelectorAll('a, button, .summary-card').forEach(el => {
        el.addEventListener('mouseenter', () => { cursor.classList.add('hovered'); ring.classList.add('hovered'); });
        el.addEventListener('mouseleave', () => { cursor.classList.remove('hovered'); ring.classList.remove('hovered'); });
    });

    const revealObs = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); revealObs.unobserve(e.target); } });
    }, { threshold: .1, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.reveal').forEach(el => revealObs.observe(el));

    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const errorDiv = document.getElementById('error-msg');
        errorDiv.classList.remove('visible');
        Swal.fire({ title: 'Memproses...', text: 'Membuat transaksi pembayaran', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch('{{ route("checkout") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({
                package_type: this.querySelector('[name="package_type"]').value,
                buyer_name:   this.querySelector('[name="buyer_name"]').value,
                school_name:  this.querySelector('[name="school_name"]').value,
                email:        this.querySelector('[name="email"]').value,
                phone:        this.querySelector('[name="phone"]').value,
            }),
        })
        .then(r => r.json())
        .then(res => {
            Swal.close();
            if (res.success) {
                window.location.href = res.redirect_url;
                return;
            }

            // Tentukan ikon & judul berdasarkan konteks
            const isValidation = res.field === 'email' || res.field === 'phone';
            const icon    = isValidation ? 'warning' : 'error';
            const title   = isValidation ? 'Data Sudah Terdaftar' : 'Gagal Memproses';
            const message = res.message || 'Terjadi kesalahan. Silakan coba lagi.';

            // Highlight field yang bermasalah
            if (res.field) {
                const fieldMap = { email: '[name="email"]', phone: '[name="phone"]' };
                const el = document.querySelector(fieldMap[res.field]);
                if (el) {
                    el.style.borderColor = '#e55';
                    el.style.boxShadow   = '0 0 0 3px rgba(229,85,85,.15)';
                    el.addEventListener('input', () => {
                        el.style.borderColor = '';
                        el.style.boxShadow   = '';
                    }, { once: true });
                    setTimeout(() => el.focus(), 300);
                }
            }

            Swal.fire({
                icon,
                title,
                html: `<div style="font-size:14px;color:#6b7a99;line-height:1.7;">${message}</div>`,
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#1246a0',
                customClass: {
                    popup:   'swal-popup-custom',
                    title:   'swal-title-custom',
                },
            });
        })
        .catch(() => {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Bermasalah',
                html: '<div style="font-size:14px;color:#6b7a99;">Gagal terhubung ke server.<br>Periksa koneksi internet kamu lalu coba lagi.</div>',
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: '#1246a0',
            });
        });
    });
</script>

</body>
</html>