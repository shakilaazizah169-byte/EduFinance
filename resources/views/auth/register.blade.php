<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFinance — Buat Akun</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600&family=Outfit:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollToPlugin.min.js"></script>

    <style>
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
            --gold:      #FFD700;
            --orange:    #FFA500;
            --success:   #10b981;
            --danger:    #ef4444;
            --warning:   #f59e0b;

            --f-display: 'Cormorant Garamond', Georgia, serif;
            --f-body:    'Outfit', system-ui, sans-serif;
            --f-mono:    'DM Mono', monospace;

            --ease-out:    cubic-bezier(.16,1,.3,1);
            --ease-in-out: cubic-bezier(.45,0,.55,1);
            --ease-spring: cubic-bezier(.34,1.56,.64,1);
        }

        html { overflow-x: hidden; }
        body {
            font-family: var(--f-body);
            background: var(--ink);
            color: var(--ink);
            line-height: 1.6;
            overflow-x: hidden;
            cursor: none;
            min-height: 100vh;
        }

        /* ── BACKGROUND ──────────────────────────────────────────── */
        .page-bg {
            position: fixed; inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 70% 20%, rgba(18,70,160,.6) 0%, transparent 60%),
                radial-gradient(ellipse 50% 50% at 20% 80%, rgba(59,130,246,.25) 0%, transparent 50%),
                linear-gradient(160deg, #0d1b35 0%, #0a1528 100%);
            z-index: -2;
        }
        .grid-overlay {
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 80px 80px;
            z-index: -1; pointer-events: none;
        }
        .orb-bg {
            position: fixed; width: 700px; height: 700px;
            right: -150px; top: -200px; border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,.18) 0%, transparent 70%);
            z-index: -1; pointer-events: none;
            animation: floatOrb 20s ease-in-out infinite;
        }
        @keyframes floatOrb {
            0%,100% { transform: translate(0,0); }
            50%      { transform: translate(30px,20px); }
        }

        /* ── CURSOR ─────────────────────────────────────────────── */
        .cursor {
            position: fixed; width: 8px; height: 8px;
            background: var(--blue-lt); border-radius: 50%;
            pointer-events: none; z-index: 99999;
            transform: translate(-50%,-50%);
            transition: width .3s var(--ease-out), height .3s var(--ease-out);
            mix-blend-mode: multiply;
        }
        .cursor-ring {
            position: fixed; width: 32px; height: 32px;
            border: 1.5px solid var(--blue-lt); border-radius: 50%;
            pointer-events: none; z-index: 99998;
            transform: translate(-50%,-50%);
            transition: width .4s var(--ease-out), height .4s var(--ease-out), opacity .3s;
            opacity: .5;
        }
        .cursor.hovered      { width: 16px; height: 16px; background: white; }
        .cursor-ring.hovered { width: 54px; height: 54px; opacity: .25; border-color: white; }
        a, button, .step-item { cursor: none; }

        /* ── NAV — pill, identik welcome ─────────────────────────── */
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
            transition: background .4s, border-color .4s, box-shadow .4s, top .4s;
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

        .nav-links { display: flex; align-items: center; gap: 2px; }
        .nav-links a {
            font-size: 13px; color: rgba(255,255,255,.72); text-decoration: none;
            letter-spacing: .01em; padding: 7px 13px; border-radius: 100px;
            transition: color .25s, background .25s; white-space: nowrap;
        }
        .nav-links a:hover { color: white; background: rgba(255,255,255,.12); }
        nav.scrolled .nav-links a { color: var(--muted); }
        nav.scrolled .nav-links a:hover { color: var(--ink); background: rgba(18,70,160,.07); }

        .nav-divider {
            width: 1px; height: 18px; background: rgba(255,255,255,.18);
            margin: 0 4px; flex-shrink: 0; transition: background .4s;
        }
        nav.scrolled .nav-divider { background: rgba(18,70,160,.15); }

        .nav-cta {
            display: inline-flex !important; align-items: center; gap: 7px;
            background: white !important; color: var(--blue) !important;
            padding: 9px 22px !important; border-radius: 100px !important;
            font-weight: 600 !important; font-size: 13px !important;
            letter-spacing: .01em;
            box-shadow: 0 2px 14px rgba(0,0,0,.15);
            transition: background .3s, transform .3s var(--ease-out), box-shadow .3s !important;
        }
        .nav-cta::after { content: '→'; font-size: 12px; transition: transform .3s var(--ease-out); display: inline-block; }
        .nav-cta:hover { background: var(--blue-pale) !important; transform: translateY(-1px) !important; box-shadow: 0 6px 20px rgba(18,70,160,.22) !important; }
        .nav-cta:hover::after { transform: translateX(3px); }
        nav.scrolled .nav-cta { background: var(--blue) !important; color: white !important; box-shadow: 0 4px 16px rgba(18,70,160,.3) !important; }
        nav.scrolled .nav-cta:hover { background: var(--blue-mid) !important; }

        /* ── MAIN ────────────────────────────────────────────────── */
        main {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 92px 52px 52px;
        }

        /* ── CARD ────────────────────────────────────────────────── */
        .register-card {
            max-width: 1100px; width: 100%;
            background: rgba(255,255,255,.95);
            backdrop-filter: blur(10px);
            border-radius: 32px; overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,.5);
            display: flex; flex-wrap: wrap;
            animation: fadeInUp .8s var(--ease-out);
            border: 1px solid rgba(255,255,255,.2);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── FORM PANEL ──────────────────────────────────────────── */
        .form-panel {
            flex: 1.2; min-width: 500px;
            padding: 48px; position: relative; background: transparent;
        }
        .logo-float {
            position: absolute; top: 48px; left: 48px;
            width: 48px; height: 48px; background: white; border-radius: 50%;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,.2);
            display: flex; align-items: center; justify-content: center;
            animation: floatBadge 3s ease-in-out infinite;
            border: 2px solid var(--blue); z-index: 10;
        }
        .logo-float img { width: 32px; height: 32px; object-fit: contain; }
        @keyframes floatBadge {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-5px); }
        }

        .form-header { margin-top: 60px; margin-bottom: 24px; }
        .form-header h1 {
            font-family: var(--f-display); font-size: 2.5rem;
            font-weight: 600; color: var(--ink); letter-spacing: -.02em; margin-bottom: 8px;
        }
        .form-header p { color: var(--muted); font-size: 1rem; }

        /* Step Bar */
        .step-bar {
            display: flex; gap: 8px; margin-bottom: 28px;
            background: rgba(0,0,0,.03); padding: 4px; border-radius: 50px;
        }
        .step-bar .step {
            flex: 1; height: 6px; border-radius: 50px;
            background: #e2e8f0; transition: all .4s;
        }
        .step-bar .step.active { background: linear-gradient(90deg, var(--blue), var(--blue-lt)); }
        .step-bar .step.done   { background: linear-gradient(90deg, var(--success), #34d399); }

        /* License Status */
        .license-status {
            display: none; margin-top: 8px; padding: 10px 14px;
            border-radius: 12px; font-size: .85rem; font-weight: 500;
            animation: slideDown .3s ease-out;
        }
        .license-status.valid    { background: rgba(16,185,129,.1);  color: #065f46; border: 1px solid rgba(16,185,129,.3); }
        .license-status.invalid  { background: rgba(239,68,68,.1);   color: #991b1b; border: 1px solid rgba(239,68,68,.3); }
        .license-status.checking { background: rgba(59,130,246,.1);  color: #1e40af; border: 1px solid rgba(59,130,246,.3); }

        .license-info-card {
            background: rgba(219,234,254,.5); border: 1px solid rgba(59,130,246,.3);
            border-radius: 12px; padding: 12px 16px; margin-top: 8px;
            display: none; animation: slideDown .3s ease-out; backdrop-filter: blur(5px);
        }
        .license-info-card .pkg-badge {
            background: var(--blue); color: #fff;
            border-radius: 50px; padding: 2px 12px;
            font-size: .75rem; font-weight: 600;
        }

        /* Form Elements */
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block; font-size: .85rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .5px;
            color: var(--muted); margin-bottom: 6px;
        }
        .form-label i { margin-right: 6px; color: var(--blue); }
        .input-group {
            display: flex; align-items: stretch;
            border: 1.5px solid var(--border);
            border-radius: 12px; overflow: hidden;
            transition: all .3s; background: white;
        }
        .input-group:focus-within {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(18,70,160,.1);
        }
        .input-icon {
            display: flex; align-items: center; justify-content: center;
            padding: 0 16px; background: #f8fafc; color: var(--muted);
            border-right: 1.5px solid var(--border);
        }
        .input-field {
            flex: 1; padding: 14px 16px; border: none; outline: none;
            font-size: .95rem; font-family: var(--f-body); background: transparent;
        }
        .input-field.font-mono { font-family: var(--f-mono); letter-spacing: 1px; }
        .toggle-password {
            padding: 0 16px; background: #f8fafc;
            border: none; border-left: 1.5px solid var(--border);
            color: var(--muted); transition: all .3s;
        }
        .toggle-password:hover { color: var(--blue); background: var(--blue-pale); }

        /* Row helper — fixed column widths untuk WhatsApp & Instansi */
        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 8px;
        }
        /* Kolom WhatsApp dan Instansi sekarang proporsional tanpa overflow */
        .col-wa {
            flex: 1.2;
            min-width: 0;  /* mencegah overflow */
        }
        .col-instansi {
            flex: 2;
            min-width: 0;  /* mencegah overflow */
        }
        /* Agar kedua input tetap rapi, input-field di dalam col otomatis full width */
        .col-wa .input-group,
        .col-instansi .input-group {
            width: 100%;
        }

        /* Password Strength */
        .pwd-strength { height: 4px; border-radius: 4px; background: #e2e8f0; margin-top: 6px; overflow: hidden; }
        .pwd-strength-bar { height: 100%; border-radius: 4px; width: 0; transition: all .4s; }
        .pwd-label { font-size: .75rem; color: #94a3b8; margin-top: 4px; transition: all .3s; }

        /* Submit */
        .btn-submit {
            width: 100%; padding: 16px;
            background: var(--blue); color: white;
            border: none; border-radius: 12px;
            font-size: 1rem; font-weight: 600; font-family: var(--f-body);
            display: flex; align-items: center; justify-content: center; gap: 10px;
            transition: all .3s; margin: 24px 0 16px;
        }
        .btn-submit:hover {
            background: var(--blue-mid); transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(18,70,160,.3);
        }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit i { font-size: 1.1rem; }

        .register-link { text-align: center; color: var(--muted); font-size: .95rem; margin-top: 12px; }
        .register-link a { color: var(--blue); text-decoration: none; font-weight: 600; margin-left: 5px; }
        .register-link a:hover { text-decoration: underline; }

        /* Alert */
        .alert { padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; }
        .alert-danger  { background: rgba(254,226,226,.9); color: #991b1b; border: 1px solid rgba(153,27,27,.2); }
        .alert-success { background: rgba(220,252,231,.9); color: #166534; border: 1px solid rgba(22,101,52,.2); }

        /* Slidedown */
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .form-group-animate { animation: fadeInUp2 .5s ease-out both; }
        .form-group-animate:nth-child(1) { animation-delay: .1s; }
        .form-group-animate:nth-child(2) { animation-delay: .15s; }
        .form-group-animate:nth-child(3) { animation-delay: .2s; }
        .form-group-animate:nth-child(4) { animation-delay: .25s; }
        .form-group-animate:nth-child(5) { animation-delay: .3s; }
        .form-group-animate:nth-child(6) { animation-delay: .35s; }
        .form-group-animate:nth-child(7) { animation-delay: .4s; }
        @keyframes fadeInUp2 {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-title    { animation: fadeInLeft .6s ease-out; }
        .animate-subtitle { animation: fadeInLeft .6s ease-out .2s both; }
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* Match hint */
        .match-hint { font-size: .8rem; margin-top: 4px; }

        /* ── INFO PANEL ──────────────────────────────────────────── */
        .info-panel {
            flex: .8; min-width: 400px;
            background: linear-gradient(135deg, rgba(18,70,160,.95), rgba(11,45,120,.95));
            backdrop-filter: blur(5px);
            padding: 48px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            position: relative; overflow: hidden;
        }
        .info-bg-icon {
            position: absolute; font-size: 12rem;
            color: rgba(255,255,255,.05); transform: rotate(15deg);
            right: -20px; bottom: -20px;
        }
        .floating-icon { position: absolute; color: rgba(255,255,255,.1); animation: floatBadge 4s ease-in-out infinite; z-index: 1; }
        .floating-icon:nth-child(1) { top: 10%; left: 10%; font-size: 2rem; }
        .floating-icon:nth-child(2) { top: 20%; right: 15%; font-size: 2.5rem; animation-delay: 1s; }
        .floating-icon:nth-child(3) { bottom: 20%; left: 15%; font-size: 2rem; animation-delay: 2s; }
        .floating-icon:nth-child(4) { bottom: 25%; right: 25%; font-size: 3rem; animation-delay: 3s; }

        .profile-card {
            background: rgba(255,255,255,.1); backdrop-filter: blur(10px);
            border-radius: 24px; padding: 32px; width: 100%; max-width: 320px;
            text-align: center; border: 1px solid rgba(255,255,255,.2);
            animation: floatBadge 3s ease-in-out infinite;
            position: relative; z-index: 2; margin-bottom: 20px;
        }
        .profile-image {
            width: 100px; height: 100px; border-radius: 50%;
            border: 3px solid white; margin: 0 auto 16px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,.3); display: block;
        }
        .profile-name {
            font-size: 1.3rem; font-weight: 600; color: white; margin-bottom: 4px;
            font-family: var(--f-display); text-shadow: 2px 2px 4px rgba(0,0,0,.3);
        }
        .profile-email {
            font-size: .85rem; color: rgba(255,255,255,.9);
            background: rgba(0,0,0,.2); padding: 4px 12px;
            border-radius: 50px; display: inline-block; margin-bottom: 16px;
        }
        .stats-card { background: rgba(0,0,0,.2); border-radius: 16px; padding: 16px; margin-bottom: 16px; }
        .stat-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 8px 0; color: white; border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .stat-item:last-child { border-bottom: none; }
        .stat-label { display: flex; align-items: center; gap: 8px; font-size: .9rem; }
        .stat-value {
            background: var(--gold); color: var(--ink);
            padding: 2px 12px; border-radius: 50px;
            font-weight: 600; font-size: .85rem;
        }
        .rating-card {
            background: rgba(0,0,0,.2); border-radius: 50px;
            padding: 10px 16px; display: flex; align-items: center;
            justify-content: center; gap: 10px; border: 1px solid rgba(255,255,255,.1);
        }
        .stars { color: var(--gold); font-size: 1rem; letter-spacing: 2px; }
        .rating-text { font-size: .85rem; color: white; }

        /* Step List */
        .step-list { width: 100%; max-width: 280px; }
        .step-item {
            display: flex; align-items: flex-start; gap: 12px;
            margin-bottom: 12px; padding: 8px 12px; border-radius: 12px;
            transition: all .3s;
            background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1);
        }
        .step-item:hover { background: rgba(255,255,255,.15); transform: translateX(5px); }
        .step-number {
            width: 28px; height: 28px; background: var(--gold); color: var(--ink);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .9rem; flex-shrink: 0; border: 2px solid white;
        }
        .step-content { flex: 1; }
        .step-title { font-weight: 600; font-size: .9rem; color: white; margin-bottom: 2px; }
        .step-desc  { font-size: .75rem; color: rgba(255,255,255,.7); }

        /* Checkmark SVG */
        .checkmark { width: 40px; height: 40px; border-radius: 50%; display: block; stroke-width: 2; stroke: #fff; stroke-miterlimit: 10; margin: 10px auto 0; }
        .checkmark__circle { stroke-dasharray: 166; stroke-dashoffset: 166; stroke-width: 2; stroke-miterlimit: 10; stroke: var(--success); fill: none; animation: stroke .6s cubic-bezier(.65,0,.45,1) forwards; }
        .checkmark__check { transform-origin: 50% 50%; stroke-dasharray: 48; stroke-dashoffset: 48; animation: stroke .3s cubic-bezier(.65,0,.45,1) .8s forwards; }
        @keyframes stroke { 100% { stroke-dashoffset: 0; } }

        /* ── RESPONSIVE (tetap sama, hanya tambahan penyesuaian) ── */
        @media (max-width: 1000px) {
            nav { width: calc(100% - 32px); top: 12px; padding: 0 6px 0 16px; }
            .nav-links a:not(.nav-cta) { display: none; }
            .nav-divider { display: none; }
            main { padding: 80px 20px 40px; }
            .register-card { flex-direction: column; }
            .form-panel, .info-panel { min-width: 100%; }
            .form-panel { padding: 32px 24px; }
            .logo-float { top: 24px; left: 24px; }
            .form-header { margin-top: 40px; }
            /* pada mobile, row jadi kolom, kedua field penuh */
            .row { flex-direction: column; gap: 12px; }
            .col-wa, .col-instansi { flex: auto; width: 100%; }
        }
    </style>
</head>
<body>

{{-- BACKGROUND --}}
<div class="page-bg"></div>
<div class="grid-overlay"></div>
<div class="orb-bg"></div>

{{-- CURSOR --}}
<div class="cursor" id="cursor"></div>
<div class="cursor-ring" id="cursorRing"></div>

{{-- ══ NAV — pill identik welcome ══ --}}
<nav id="mainNav">
    <a href="/" class="nav-logo">
        <img src="{{ asset('assets/images/EDUFINANCE1.png') }}" alt="EduFinance Logo" class="nav-logo-img">
        <span class="nav-logo-text">EduFinance</span>
    </a>
    <div class="nav-links">
        <a href="/">Home</a>
        <a href="/#fitur">Fitur</a>
        <a href="/#kolaborasi">Instansi</a>
        <a href="{{ route('pricing') }}">Harga</a>
        <div class="nav-divider"></div>
        @auth
            <a href="{{ route('dashboard') }}" class="nav-cta">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="nav-cta">Masuk</a>
        @endauth
    </div>
</nav>

{{-- MAIN --}}
<main>
    <div class="register-card">

        {{-- FORM PANEL --}}
        <div class="form-panel">
            <div class="logo-float">
                <img src="{{ asset('assets/images/EDUFINANCE1.png') }}" alt="Logo">
            </div>

            <div class="form-header">
                <h1 class="animate-title">Buat Akun</h1>
                <p class="animate-subtitle">Masukkan kode lisensi dari email/WhatsApp Anda</p>
            </div>

            {{-- Step Bar --}}
            <div class="step-bar" id="stepBar">
                <div class="step" id="step1"></div>
                <div class="step" id="step2"></div>
                <div class="step" id="step3"></div>
            </div>

            {{-- Alerts --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li class="small">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                {{-- License Key --}}
                <div class="form-group form-group-animate">
                    <label class="form-label">
                        <i class="bi bi-key"></i> Kode Lisensi <span style="color:var(--danger)">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-icon"><i class="bi bi-key-fill"></i></span>
                        <input type="text"
                               class="input-field font-mono @error('license_key') is-invalid @enderror"
                               name="license_key" id="license_key"
                               value="{{ old('license_key', $licenseKey ?? '') }}"
                               placeholder="XXXX-XXXX-XXXX-XXXX"
                               autocomplete="off" required>
                    </div>
                    <div class="license-status" id="licenseStatus"></div>
                    <div class="license-info-card" id="licenseInfo"></div>
                </div>

                {{-- Name --}}
                <div class="form-group form-group-animate">
                    <label class="form-label">
                        <i class="bi bi-person"></i> Nama Lengkap <span style="color:var(--danger)">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-icon"><i class="bi bi-person-fill"></i></span>
                        <input type="text"
                               class="input-field @error('name') is-invalid @enderror"
                               name="name" id="name"
                               value="{{ old('name', $prefill['name'] ?? '') }}"
                               placeholder="Nama lengkap Anda" required>
                    </div>
                </div>

                {{-- Email --}}
                <div class="form-group form-group-animate">
                    <label class="form-label">
                        <i class="bi bi-envelope"></i> Email <span style="color:var(--danger)">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-icon"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email"
                               class="input-field @error('email') is-invalid @enderror"
                               name="email" id="email"
                               value="{{ old('email', $prefill['email'] ?? '') }}"
                               placeholder="email@sekolah.com" required>
                    </div>
                </div>

                {{-- Phone & School — pakai row dengan class baru agar instansi lebih lebar proporsional --}}
                <div class="row">
                    <div class="col-wa">
                        <div class="form-group form-group-animate">
                            <label class="form-label">
                                <i class="bi bi-whatsapp"></i> WhatsApp <span style="color:var(--danger)">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-icon"><i class="bi bi-whatsapp"></i></span>
                                <input type="text"
                                       class="input-field @error('phone') is-invalid @enderror"
                                       name="phone" id="phone"
                                       value="{{ old('phone', $prefill['phone'] ?? '') }}"
                                       placeholder="08123456789" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-instansi">
                        <div class="form-group form-group-animate">
                            <label class="form-label">
                                <i class="bi bi-building"></i> Nama Instansi <span style="color:var(--danger)">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-icon"><i class="bi bi-building"></i></span>
                                <input type="text"
                                       class="input-field @error('school_name') is-invalid @enderror"
                                       name="school_name" id="school_name"
                                       value="{{ old('school_name', $prefill['school_name'] ?? '') }}"
                                       placeholder="SMAN 1 Kota / Universitas" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Password --}}
                <div class="form-group form-group-animate">
                    <label class="form-label">
                        <i class="bi bi-lock"></i> Password <span style="color:var(--danger)">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-icon"><i class="bi bi-lock-fill"></i></span>
                        <input type="password"
                               class="input-field @error('password') is-invalid @enderror"
                               name="password" id="password"
                               placeholder="Minimal 8 karakter" required>
                        <button type="button" class="toggle-password" onclick="togglePwd('password')">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    <div class="pwd-strength"><div class="pwd-strength-bar" id="pwdBar"></div></div>
                    <div class="pwd-label" id="pwdLabel">Ketikkan password...</div>
                </div>

                {{-- Confirm Password --}}
                <div class="form-group form-group-animate">
                    <label class="form-label">
                        <i class="bi bi-lock-fill"></i> Konfirmasi Password <span style="color:var(--danger)">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-icon"><i class="bi bi-lock-fill"></i></span>
                        <input type="password"
                               class="input-field"
                               name="password_confirmation" id="password_confirmation"
                               placeholder="Ulangi password" required>
                        <button type="button" class="toggle-password" onclick="togglePwd('password_confirmation')">
                            <i class="bi bi-eye" id="toggleConfirmIcon"></i>
                        </button>
                    </div>
                    <div class="match-hint" id="matchHint" style="display:none;"></div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="bi bi-person-check"></i>
                    <span>Buat Akun</span>
                </button>

                <div class="register-link">
                    <span>Sudah punya akun?</span>
                    <a href="{{ route('login') }}">Login di sini</a>
                </div>
                <div class="register-link">
                    <span>Belum punya lisensi?</span>
                    <a href="{{ route('pricing') }}">Beli Sekarang</a>
                </div>
            </form>
        </div>

        {{-- INFO PANEL --}}
        <div class="info-panel">
            <div class="floating-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="floating-icon"><i class="bi bi-graph-up"></i></div>
            <div class="floating-icon"><i class="bi bi-people"></i></div>
            <div class="floating-icon"><i class="bi bi-building"></i></div>
            <div class="info-bg-icon"><i class="bi bi-building"></i></div>

            <div class="profile-card">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($prefill['name'] ?? 'EduFinance') }}&size=100&length=2&background=1246a0&color=fff&bold=true"
                     alt="Profile" class="profile-image" id="profileImage">
                <h3 class="profile-name" id="profileName">{{ $prefill['name'] ?? 'EduFinance' }}</h3>
                <span class="profile-email" id="profileEmail">{{ $prefill['email'] ?? 'akun@edufinance.id' }}</span>

                <div class="stats-card">
                    <div class="stat-item">
                        <span class="stat-label"><i class="bi bi-building"></i> Total Instansi</span>
                        <span class="stat-value" id="totalSekolah">1,247++</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label"><i class="bi bi-people"></i> Pengguna Aktif</span>
                        <span class="stat-value" id="penggunaAktif">5,832++</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label"><i class="bi bi-graph-up"></i> Transaksi/hari</span>
                        <span class="stat-value" id="transaksiHari">10,456++</span>
                    </div>
                </div>

                <div class="rating-card">
                    <div class="stars">★★★★★</div>
                    <div class="rating-text">4.9 (1,247 ulasan)</div>
                </div>

                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52" style="display:none;" id="successCheckmark">
                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
            </div>

            <div class="step-list">
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <div class="step-title">Beli lisensi</div>
                        <div class="step-desc">Pilih paket di halaman Harga</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <div class="step-title">Cek Email/WA</div>
                        <div class="step-desc">Kode lisensi dikirim otomatis</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <div class="step-title">Daftar Akun</div>
                        <div class="step-desc">Masukkan kode lisensi di form ini</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <div class="step-title">Mulai pakai</div>
                        <div class="step-desc">Akses semua fitur langsung</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
/* ── CURSOR ─────────────────────────────────────────────── */
const cursor = document.getElementById('cursor');
const ring   = document.getElementById('cursorRing');
let mx = 0, my = 0, rx = 0, ry = 0;
document.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; });
(function anim() {
    cursor.style.left = mx+'px'; cursor.style.top = my+'px';
    rx += (mx-rx)*.11; ry += (my-ry)*.11;
    ring.style.left = rx+'px'; ring.style.top = ry+'px';
    requestAnimationFrame(anim);
})();
document.querySelectorAll('a, button, .step-item').forEach(el => {
    el.addEventListener('mouseenter', () => { cursor.classList.add('hovered'); ring.classList.add('hovered'); });
    el.addEventListener('mouseleave', () => { cursor.classList.remove('hovered'); ring.classList.remove('hovered'); });
});

/* ── NAV SCROLL ─────────────────────────────────────────── */
window.addEventListener('scroll', () => {
    document.getElementById('mainNav').classList.toggle('scrolled', window.scrollY > 60);
});

/* ── LICENSE CHECK ───────────────────────────────────────── */
const licenseInput  = document.getElementById('license_key');
const licenseStatus = document.getElementById('licenseStatus');
const licenseInfo   = document.getElementById('licenseInfo');
let licenseTimer = null;
let licenseValid = false;
const prefillFields = ['name','email','phone','school_name'];

function setLicenseStatus(type, msg) {
    licenseStatus.className = 'license-status ' + type;
    licenseStatus.textContent = msg;
    licenseStatus.style.display = 'block';
}

function applyPrefill(data) {
    prefillFields.forEach(id => {
        const el = document.getElementById(id);
        if (!el || !data[id]) return;
        el.value = data[id];
        el.setAttribute('readonly', true);
        el.style.background = '#f0f6ff';
        el.style.color = 'var(--blue)';
        el.style.fontWeight = '500';
        el.closest('.input-group').style.borderColor = 'var(--blue-lt)';
    });
    updateProfile();
}

function clearPrefill() {
    prefillFields.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.value = '';
        el.removeAttribute('readonly');
        el.style.background = '';
        el.style.color = '';
        el.style.fontWeight = '';
        el.closest('.input-group').style.borderColor = '';
    });
}

async function checkLicense(key) {
    if (key.length < 10) {
        licenseStatus.style.display = 'none';
        licenseInfo.style.display = 'none';
        clearPrefill();
        licenseValid = false;
        updateStepBar();
        return;
    }
    setLicenseStatus('checking', '⏳ Memeriksa kode lisensi...');
    try {
        const res  = await fetch(`/api/license/lookup?key=${encodeURIComponent(key)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();
        if (json.valid) {
            setLicenseStatus('valid', '✅ Kode lisensi valid');
            licenseInfo.style.display = 'block';
            licenseInfo.innerHTML = `
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <span class="pkg-badge">${json.package_type}</span>
                        <div style="font-size:.75rem;margin-top:4px;">Berlaku sampai <strong>${json.expires_at}</strong></div>
                    </div>
                    <i class="bi bi-patch-check-fill" style="color:var(--blue);font-size:1.5rem;"></i>
                </div>`;
            licenseValid = true;
            applyPrefill(json.prefill);
            const mark = document.getElementById('successCheckmark');
            if (mark) { mark.style.display = 'block'; setTimeout(() => { mark.style.display = 'none'; }, 2000); }
        } else {
            setLicenseStatus('invalid', `❌ ${json.message ?? 'Kode lisensi tidak valid'}`);
            licenseInfo.style.display = 'none';
            licenseValid = false;
            clearPrefill();
        }
    } catch {
        setLicenseStatus('invalid', '❌ Gagal memeriksa lisensi. Coba lagi.');
        licenseValid = false;
        clearPrefill();
    }
    updateStepBar();
}

licenseInput.addEventListener('input', () => {
    clearTimeout(licenseTimer);
    const val = licenseInput.value.trim().toUpperCase();
    licenseInput.value = val;
    licenseTimer = setTimeout(() => checkLicense(val), 600);
});

const prefilledKey = licenseInput.value.trim();
if (prefilledKey.length > 5) setTimeout(() => checkLicense(prefilledKey), 300);

/* ── PASSWORD STRENGTH ───────────────────────────────────── */
const pwdInput  = document.getElementById('password');
const pwdConf   = document.getElementById('password_confirmation');
const pwdBar    = document.getElementById('pwdBar');
const pwdLabel  = document.getElementById('pwdLabel');
const matchHint = document.getElementById('matchHint');

const strengthLevels = [
    { min:0,  max:3,  color:'#ef4444', w:'20%',  label:'Terlalu lemah' },
    { min:4,  max:6,  color:'#f59e0b', w:'50%',  label:'Sedang' },
    { min:7,  max:9,  color:'#3b82f6', w:'75%',  label:'Kuat' },
    { min:10, max:99, color:'#10b981', w:'100%', label:'Sangat kuat' },
];

function scorePassword(p) {
    let s = 0;
    if (p.length >= 8)  s += 2;
    if (p.length >= 12) s += 2;
    if (/[A-Z]/.test(p)) s += 2;
    if (/[0-9]/.test(p)) s += 2;
    if (/[^A-Za-z0-9]/.test(p)) s += 2;
    return s;
}

pwdInput.addEventListener('input', () => {
    const score = scorePassword(pwdInput.value);
    const level = strengthLevels.find(l => score >= l.min && score <= l.max) || strengthLevels[0];
    pwdBar.style.width = level.w;
    pwdBar.style.background = level.color;
    pwdLabel.textContent = pwdInput.value ? level.label : 'Ketikkan password...';
    pwdLabel.style.color = level.color;
    checkMatch();
    updateStepBar();
});

pwdConf.addEventListener('input', checkMatch);

function checkMatch() {
    if (!pwdConf.value) { matchHint.style.display = 'none'; return; }
    matchHint.style.display = 'block';
    if (pwdInput.value === pwdConf.value) {
        matchHint.textContent = '✅ Password cocok';
        matchHint.style.color = '#10b981';
    } else {
        matchHint.textContent = '❌ Password tidak cocok';
        matchHint.style.color = '#ef4444';
    }
    updateStepBar();
}

/* ── STEP BAR ────────────────────────────────────────────── */
function updateStepBar() {
    const hasLicense = licenseValid;
    const hasData    = document.getElementById('name').value &&
                       document.getElementById('email').value &&
                       document.getElementById('school_name').value;
    const hasPwd     = pwdInput.value.length >= 8 && pwdInput.value === pwdConf.value;

    document.getElementById('step1').className = 'step ' + (hasLicense ? 'done' : 'active');
    document.getElementById('step2').className = 'step ' + (hasData ? 'done' : hasLicense ? 'active' : '');
    document.getElementById('step3').className = 'step ' + (hasPwd  ? 'done' : hasData    ? 'active' : '');
}

['name','email','phone','school_name'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', updateStepBar);
});
updateStepBar();

/* ── TOGGLE PASSWORD ─────────────────────────────────────── */
function togglePwd(id) {
    const input = document.getElementById(id);
    const iconId = id === 'password' ? 'togglePasswordIcon' : 'toggleConfirmIcon';
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye','bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash','bi-eye');
    }
}

/* ── FORM SUBMIT ─────────────────────────────────────────── */
document.getElementById('registerForm').addEventListener('submit', function(e) {
    if (!licenseValid) {
        e.preventDefault();
        setLicenseStatus('invalid', '❌ Masukkan kode lisensi yang valid terlebih dahulu.');
        licenseInput.focus();
        return;
    }
    if (pwdInput.value !== pwdConf.value) {
        e.preventDefault();
        matchHint.textContent = '❌ Password tidak cocok';
        matchHint.style.color = '#ef4444';
        matchHint.style.display = 'block';
        pwdConf.focus();
        return;
    }
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i><span>Membuat akun...</span>';
});

/* ── DYNAMIC PROFILE ─────────────────────────────────────── */
const nameInput    = document.getElementById('name');
const emailInput   = document.getElementById('email');
const profileImage = document.getElementById('profileImage');
const profileName  = document.getElementById('profileName');
const profileEmail = document.getElementById('profileEmail');

function updateProfile() {
    const name  = nameInput.value.trim();
    const email = emailInput.value.trim();
    if (name) {
        profileImage.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=100&length=2&background=1246a0&color=fff&bold=true`;
        profileName.textContent = name;
    }
    if (email) profileEmail.textContent = email;
}

nameInput.addEventListener('input', updateProfile);
emailInput.addEventListener('input', updateProfile);
</script>
</body>
</html>