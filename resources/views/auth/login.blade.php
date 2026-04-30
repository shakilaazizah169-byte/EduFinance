<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFinance — Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600&family=Outfit:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --blue:      #1246a0;
            --blue-mid:  #1a5bc4;
            --blue-lt:   #3b82f6;
            --blue-pale: #dde9ff;
            --ink:       #0d1b35;
            --muted:     #6b7a99;
            --border:    rgba(18,70,160,.12);
            --gold:      #FFD700;

            --f-display: 'Cormorant Garamond', Georgia, serif;
            --f-body:    'Outfit', system-ui, sans-serif;

            --ease-out:    cubic-bezier(.16,1,.3,1);
        }

        html, body {
            min-height: 100vh;
            overflow-x: hidden;
            font-family: var(--f-body);
            color: var(--ink);
            line-height: 1.6;
        }

        /* ── BACKGROUND ──────────────────────────────────────── */
        .page-bg {
            position: fixed; inset: 0; z-index: -2;
            background:
                radial-gradient(ellipse 80% 60% at 70% 20%, rgba(18,70,160,.6) 0%, transparent 60%),
                radial-gradient(ellipse 50% 50% at 20% 80%, rgba(59,130,246,.25) 0%, transparent 50%),
                linear-gradient(160deg, #0d1b35 0%, #0a1528 100%);
        }
        .grid-overlay {
            position: fixed; inset: 0; z-index: -1; pointer-events: none;
            background-image:
                linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 80px 80px;
        }
        .orb-bg {
            position: fixed; width: 700px; height: 700px;
            right: -150px; top: -200px; border-radius: 50%; z-index: -1;
            background: radial-gradient(circle, rgba(59,130,246,.18) 0%, transparent 70%);
            pointer-events: none;
            animation: floatOrb 20s ease-in-out infinite;
        }
        @keyframes floatOrb  { 0%,100%{transform:translate(0,0)} 50%{transform:translate(30px,20px)} }
        @keyframes floatBadge{ 0%,100%{transform:translateY(0)}  50%{transform:translateY(-5px)}     }
        @keyframes fadeInUp  { from{opacity:0;transform:translateY(28px)} to{opacity:1;transform:translateY(0)} }

        /* ── CURSOR (mouse device saja) ──────────────────────── */
        .cursor, .cursor-ring { display: none; pointer-events: none; }
        @media (hover: hover) and (pointer: fine) {
            body { cursor: none; }
            a, button { cursor: none; }
            .cursor {
                display: block; position: fixed;
                width: 8px; height: 8px; border-radius: 50%;
                background: var(--blue-lt); z-index: 99999;
                transform: translate(-50%,-50%);
                transition: width .3s var(--ease-out), height .3s var(--ease-out);
                mix-blend-mode: multiply;
            }
            .cursor-ring {
                display: block; position: fixed;
                width: 32px; height: 32px; border-radius: 50%;
                border: 1.5px solid var(--blue-lt); z-index: 99998;
                transform: translate(-50%,-50%); opacity: .5;
                transition: width .4s var(--ease-out), height .4s var(--ease-out), opacity .3s;
            }
            .cursor.hovered      { width: 16px; height: 16px; background: white; }
            .cursor-ring.hovered { width: 54px; height: 54px; opacity: .25; border-color: white; }
        }

        /* ── NAV ─────────────────────────────────────────────── */
        nav {
            position: fixed; top: 16px; left: 50%; transform: translateX(-50%);
            z-index: 8000;
            width: calc(100% - 80px); max-width: 1100px; height: 58px;
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.16);
            backdrop-filter: blur(20px) saturate(1.6);
            -webkit-backdrop-filter: blur(20px) saturate(1.6);
            border-radius: 100px; padding: 0 8px 0 20px;
            box-shadow: 0 4px 32px rgba(0,0,0,.15);
            transition: background .4s, border-color .4s, box-shadow .4s, top .4s;
        }
        nav.scrolled {
            background: rgba(255,255,255,.95);
            border-color: rgba(18,70,160,.12);
            box-shadow: 0 4px 40px rgba(18,70,160,.10);
            top: 12px;
        }
        .nav-logo { display: flex; align-items: center; gap: 8px; text-decoration: none; flex-shrink: 0; }
        .nav-logo-img { height: 26px; filter: brightness(0) invert(1); transition: filter .4s; }
        nav.scrolled .nav-logo-img { filter: brightness(0); }
        .nav-logo-text {
            font-family: var(--f-display); font-size: 17px; font-weight: 700;
            color: white; letter-spacing: -.02em; transition: color .4s;
        }
        nav.scrolled .nav-logo-text { color: var(--ink); }

        .nav-links { display: flex; align-items: center; gap: 2px; }
        .nav-links a {
            font-size: 13px; color: rgba(255,255,255,.72); text-decoration: none;
            padding: 7px 13px; border-radius: 100px;
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
            box-shadow: 0 2px 14px rgba(0,0,0,.15);
            transition: all .3s !important;
        }
        .nav-cta::after { content: '→'; font-size: 12px; transition: transform .3s; display: inline-block; }
        .nav-cta:hover { background: var(--blue-pale) !important; transform: translateY(-1px) !important; }
        .nav-cta:hover::after { transform: translateX(3px); }
        nav.scrolled .nav-cta { background: var(--blue) !important; color: white !important; }

        /* ── MAIN ────────────────────────────────────────────── */
        main {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 90px 40px 50px;
        }

        /* ── LOGIN CARD ──────────────────────────────────────── */
        /*  Pakai flex dengan lebar tetap di tiap panel agar
            desktop selalu tampil 2 kolom side-by-side           */
        .login-card {
            width: 100%; max-width: 980px;
            display: flex; flex-direction: row;    /* SELALU row di desktop */
            border-radius: 28px; overflow: hidden;
            box-shadow: 0 30px 60px -15px rgba(0,0,0,.55);
            background: white;
            border: 1px solid rgba(255,255,255,.15);
            animation: fadeInUp .7s var(--ease-out) both;
        }

        /* ── FORM PANEL (kiri) ───────────────────────────────── */
        .form-panel {
            width: 50%; flex-shrink: 0;           /* lebar fixed 50% */
            padding: 44px 44px 40px;
            background: white;
            display: flex; flex-direction: column;
        }

        .logo-float {
            width: 48px; height: 48px; flex-shrink: 0;
            background: white; border-radius: 50%;
            box-shadow: 0 8px 24px -4px rgba(18,70,160,.2);
            border: 2px solid var(--blue);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 22px;
            animation: floatBadge 3s ease-in-out infinite;
        }
        .logo-float img { width: 30px; height: 30px; object-fit: contain; border-radius: 50%; }

        .form-header { margin-bottom: 22px; }
        .form-header h1 {
            font-family: var(--f-display); font-size: 2.3rem;
            font-weight: 600; color: var(--ink);
            letter-spacing: -.02em; line-height: 1.15; margin-bottom: 5px;
        }
        .form-header p { color: var(--muted); font-size: .95rem; }

        .role-selector {
            display: flex; gap: 10px;
            background: rgba(0,0,0,.055);
            padding: 5px; border-radius: 60px; margin-bottom: 22px;
        }
        .role-btn {
            flex: 1; padding: 10px 16px; border: none;
            background: transparent; border-radius: 60px;
            font-size: .875rem; font-weight: 500;
            color: var(--muted); font-family: var(--f-body);
            display: flex; align-items: center; justify-content: center; gap: 7px;
            transition: all .3s; white-space: nowrap;
        }
        .role-btn i { font-size: 1rem; flex-shrink: 0; }
        .role-btn.active { background: white; color: var(--blue); box-shadow: 0 4px 12px rgba(18,70,160,.15); }
        .role-btn:hover:not(.active) { background: rgba(255,255,255,.6); color: var(--ink); }

        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block; font-size: .78rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .5px;
            color: var(--muted); margin-bottom: 7px;
        }
        .form-label i { margin-right: 5px; color: var(--blue); }
        .input-group {
            display: flex; align-items: stretch;
            border: 1.5px solid var(--border); border-radius: 12px;
            overflow: hidden; background: white;
            transition: border-color .25s, box-shadow .25s;
        }
        .input-group:focus-within {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(18,70,160,.1);
        }
        .input-icon {
            display: flex; align-items: center; justify-content: center;
            padding: 0 14px; background: #f8fafc; color: var(--muted);
            border-right: 1.5px solid var(--border); flex-shrink: 0;
        }
        .input-field {
            flex: 1; min-width: 0;
            padding: 13px 14px; border: none; outline: none;
            font-size: .9rem; font-family: var(--f-body); background: transparent;
        }
        .toggle-password {
            padding: 0 13px; background: #f8fafc;
            border: none; border-left: 1.5px solid var(--border);
            color: var(--muted); transition: all .25s; flex-shrink: 0;
        }
        .toggle-password:hover { color: var(--blue); background: var(--blue-pale); }

        .form-options {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px; flex-wrap: wrap; gap: 8px;
        }
        .checkbox { display: flex; align-items: center; gap: 8px; }
        .checkbox input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--blue); flex-shrink: 0; }
        .checkbox span { font-size: .875rem; color: var(--ink); }
        .forgot-link { color: var(--blue); text-decoration: none; font-size: .875rem; font-weight: 500; }
        .forgot-link:hover { text-decoration: underline; }

        .btn-submit {
            width: 100%; padding: 14px;
            background: var(--blue); color: white;
            border: none; border-radius: 12px;
            font-size: .95rem; font-weight: 600; font-family: var(--f-body);
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all .3s; margin-bottom: 18px;
        }
        .btn-submit:hover {
            background: var(--blue-mid); transform: translateY(-2px);
            box-shadow: 0 10px 24px -5px rgba(18,70,160,.35);
        }
        .btn-submit:active { transform: translateY(0); }

        .register-link { text-align: center; color: var(--muted); font-size: .9rem; margin-top: auto; }
        .register-link a { color: var(--blue); text-decoration: none; font-weight: 600; margin-left: 4px; }
        .register-link a:hover { text-decoration: underline; }

        .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: .875rem; }
        .alert-danger  { background: #fee2e2; color: #991b1b; border: 1px solid rgba(153,27,27,.2); }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid rgba(22,101,52,.2); }

        /* ── INFO PANEL (kanan) ──────────────────────────────── */
        .info-panel {
            width: 50%; flex-shrink: 0;           /* lebar fixed 50% */
            background: linear-gradient(145deg, #1246a0 0%, #0b2d78 100%);
            padding: 44px 36px;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            position: relative; overflow: hidden;
        }
        .info-bg-icon {
            position: absolute; font-size: 14rem;
            color: rgba(255,255,255,.05); transform: rotate(15deg);
            right: -30px; bottom: -30px; pointer-events: none;
        }
        .floating-icon {
            position: absolute; color: rgba(255,255,255,.1);
            animation: floatBadge 4s ease-in-out infinite; pointer-events: none;
        }
        .floating-icon:nth-child(1){ top:10%; left:10%; font-size:2rem; }
        .floating-icon:nth-child(2){ top:18%; right:12%; font-size:2.5rem; animation-delay:1s; }
        .floating-icon:nth-child(3){ bottom:18%; left:12%; font-size:2rem; animation-delay:2s; }

        .profile-card {
            background: rgba(255,255,255,.10);
            backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
            border-radius: 24px; padding: 32px 28px;
            width: 100%; max-width: 300px; text-align: center;
            border: 1px solid rgba(255,255,255,.18);
            animation: floatBadge 3s ease-in-out infinite;
            position: relative; z-index: 2;
        }
        .profile-image {
            width: 88px; height: 88px; border-radius: 50%;
            border: 3px solid white; margin: 0 auto 14px; display: block;
            box-shadow: 0 10px 28px -5px rgba(0,0,0,.35);
        }
        .profile-name {
            font-family: var(--f-display); font-size: 1.45rem;
            font-weight: 600; color: white; margin-bottom: 4px;
            text-shadow: 1px 2px 4px rgba(0,0,0,.3);
        }
        .profile-role {
            display: inline-block; background: rgba(255,255,255,.15);
            padding: 4px 16px; border-radius: 50px; font-size: .78rem;
            color: white; border: 1px solid rgba(255,255,255,.3); margin-bottom: 20px;
        }
        .stats { background: rgba(0,0,0,.2); border-radius: 14px; padding: 14px 12px; }
        .stat-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 7px 0; color: white;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .stat-item:last-child { border-bottom: none; }
        .stat-label { display: flex; align-items: center; gap: 7px; font-size: .85rem; }
        .stat-value {
            background: var(--gold); color: var(--ink);
            padding: 2px 10px; border-radius: 50px;
            font-weight: 600; font-size: .8rem;
            white-space: nowrap; margin-left: 8px;
        }
        .welcome-message {
            margin-top: 16px; padding: 10px 18px;
            background: rgba(0,0,0,.3); border-radius: 50px; color: white;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            font-size: .875rem; border: 1px solid rgba(255,255,255,.2);
        }
        .welcome-message i { color: var(--gold); }


        /* ════════════════════════════════════════════════════
           RESPONSIVE
        ════════════════════════════════════════════════════ */

        /* Laptop kecil / tablet landscape ≤ 1024px — masih 2 kolom */
        @media (max-width: 1024px) {
            main { padding: 84px 28px 44px; }
            .form-panel { padding: 36px 32px 32px; }
            .info-panel  { padding: 36px 28px; }
            .form-header h1 { font-size: 2rem; }
        }

        /* Tablet portrait ≤ 860px — switch ke 1 kolom */
        @media (max-width: 860px) {
            nav { width: calc(100% - 32px); }
            .nav-links a:not(.nav-cta) { display: none; }
            .nav-divider { display: none; }

            main { padding: 80px 20px 40px; }

            .login-card {
                flex-direction: column;   /* stack vertikal */
                max-width: 480px;
                border-radius: 22px;
            }
            .form-panel { width: 100%; padding: 36px 32px 28px; }
            .info-panel  { width: 100%; padding: 32px 28px; border-top: 1px solid rgba(255,255,255,.1); }
            .profile-card { max-width: 100%; }
        }

        /* Mobile besar ≤ 600px */
        @media (max-width: 600px) {
            main { padding: 76px 14px 32px; }
            .login-card { border-radius: 18px; }
            .form-panel  { padding: 28px 24px 22px; }
            .info-panel  { padding: 26px 20px; }
            .form-header h1 { font-size: 1.85rem; }
        }

        /* Mobile standar ≤ 480px */
        @media (max-width: 480px) {
            nav { width: calc(100% - 24px); height: 52px; padding: 0 6px 0 14px; }
            .nav-logo-text { font-size: 15px; }

            main { padding: 70px 12px 28px; }
            .login-card { border-radius: 16px; }
            .form-panel  { padding: 24px 18px 20px; }

            .logo-float { width: 42px; height: 42px; }
            .logo-float img { width: 26px; height: 26px; }

            .form-header h1 { font-size: 1.65rem; }
            .form-header p  { font-size: .875rem; }

            .role-btn { font-size: .82rem; padding: 9px 10px; gap: 5px; }

            .input-field    { padding: 12px; font-size: .875rem; }
            .input-icon     { padding: 0 12px; }
            .toggle-password{ padding: 0 12px; }

            .btn-submit { padding: 13px; font-size: .9rem; }

            .info-panel   { padding: 24px 16px; }
            .profile-card { padding: 22px 16px; }
            .profile-image{ width: 76px; height: 76px; }
            .profile-name { font-size: 1.2rem; }
            .floating-icon{ display: none; }
        }

        /* Mobile kecil ≤ 360px */
        @media (max-width: 360px) {
            main { padding: 66px 10px 24px; }
            .form-panel { padding: 20px 14px; }
            .form-header h1 { font-size: 1.45rem; }
            .role-btn { font-size: .78rem; padding: 8px; gap: 4px; }
        }

        /* Landscape HP (tinggi layar < 520px) */
        @media (max-height: 520px) and (orientation: landscape) {
            main { padding: 66px 20px 20px; align-items: flex-start; }
            .login-card { flex-direction: row; max-width: 800px; }
            .form-panel { width: 55%; padding: 20px 24px; }
            .info-panel { width: 45%; padding: 20px 18px; }
            .logo-float { margin-bottom: 10px; width: 36px; height: 36px; }
            .logo-float img { width: 22px; height: 22px; }
            .form-header { margin-bottom: 10px; }
            .form-header h1 { font-size: 1.35rem; }
            .form-group { margin-bottom: 10px; }
            .role-selector { margin-bottom: 12px; }
            .form-options { margin-bottom: 12px; }
            .btn-submit { padding: 11px; margin-bottom: 10px; }
            .profile-card { padding: 16px 14px; }
            .profile-image{ width: 58px; height: 58px; margin-bottom: 8px; }
            .profile-name { font-size: .95rem; }
            .floating-icon{ display: none; }
        }
    </style>
</head>
<body>

<div class="page-bg"></div>
<div class="grid-overlay"></div>
<div class="orb-bg"></div>
<div class="cursor" id="cursor"></div>
<div class="cursor-ring" id="cursorRing"></div>

<!-- NAV -->
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
            <a href="{{ route('register') }}" class="nav-cta">Daftar</a>
        @endauth
    </div>
</nav>

<!-- MAIN -->
<main>
    <div class="login-card">

        <!-- FORM PANEL -->
        <div class="form-panel">
            <div class="logo-float">
                <img src="{{ asset('assets/images/EDUFINANCE.jpg') }}" alt="Logo">
            </div>

            <div class="form-header">
                <h1>Selamat Datang</h1>
                <p>Masuk ke akun EduFinance Anda</p>
            </div>

            <div class="role-selector">
                <button class="role-btn active" id="btnAdmin" onclick="setRole('admin')">
                    <i class="bi bi-person-badge"></i>
                    <span>Admin Instansi</span>
                </button>
                <button class="role-btn" id="btnSuperAdmin" onclick="setRole('super-admin')">
                    <i class="bi bi-shield-lock"></i>
                    <span>Super Admin</span>
                </button>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin:0;padding-left:18px;">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif
            @if(session('status'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle" style="margin-right:6px;"></i>{{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                <input type="hidden" name="role" id="roleInput" value="admin">

                <div class="form-group">
                    <label class="form-label"><i class="bi bi-envelope"></i> Email</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" class="input-field" name="email"
                               value="{{ old('email') }}" placeholder="email@sekolah.com"
                               required autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="bi bi-lock"></i> Password</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="input-field" name="password"
                               id="password" placeholder="••••••••"
                               required autocomplete="current-password">
                        <button type="button" class="toggle-password" onclick="togglePassword()" aria-label="Tampilkan password">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Ingat saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span>Masuk</span>
                    <i class="bi bi-arrow-right"></i>
                </button>

                <div class="register-link">
                    <span>Belum punya akun?</span>
                    <a href="{{ route('register') }}">Daftar Sekarang</a>
                </div>
            </form>
        </div>

        <!-- INFO PANEL -->
        <div class="info-panel">
            <div class="floating-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="floating-icon"><i class="bi bi-graph-up"></i></div>
            <div class="floating-icon"><i class="bi bi-people"></i></div>
            <div class="info-bg-icon"><i class="bi bi-building"></i></div>

            <div class="profile-card">
                <img src="https://ui-avatars.com/api/?name=Admin+Instansi&size=100&length=2&background=1246a0&color=fff&bold=true"
                     alt="Profile" class="profile-image" id="profileImage">
                <h3 class="profile-name" id="profileName">Admin Instansi</h3>
                <span class="profile-role" id="profileRole">Administrator</span>

                <div class="stats">
                    <div class="stat-item">
                        <span class="stat-label"><i class="bi bi-building"></i> Total Instansi</span>
                        <span class="stat-value">1,234+</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label"><i class="bi bi-people"></i> Pengguna Aktif</span>
                        <span class="stat-value">5,678+</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label"><i class="bi bi-graph-up"></i> Transaksi/hari</span>
                        <span class="stat-value">10K+</span>
                    </div>
                </div>

                <div class="welcome-message" id="welcomeMessage">
                    <i class="bi bi-sun"></i>
                    <span>Selamat datang kembali</span>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
/* CURSOR — mouse only */
if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    const cursor = document.getElementById('cursor');
    const ring   = document.getElementById('cursorRing');
    let mx=0,my=0,rx=0,ry=0;
    document.addEventListener('mousemove', e => { mx=e.clientX; my=e.clientY; });
    (function anim(){
        cursor.style.left=mx+'px'; cursor.style.top=my+'px';
        rx+=(mx-rx)*.11; ry+=(my-ry)*.11;
        ring.style.left=rx+'px'; ring.style.top=ry+'px';
        requestAnimationFrame(anim);
    })();
    document.querySelectorAll('a,button').forEach(el => {
        el.addEventListener('mouseenter',()=>{ cursor.classList.add('hovered'); ring.classList.add('hovered'); });
        el.addEventListener('mouseleave',()=>{ cursor.classList.remove('hovered'); ring.classList.remove('hovered'); });
    });
}

/* NAV SCROLL */
window.addEventListener('scroll', () => {
    document.getElementById('mainNav').classList.toggle('scrolled', window.scrollY > 60);
}, { passive: true });

/* TOGGLE PASSWORD */
function togglePassword() {
    const pwd  = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    const show = pwd.type === 'password';
    pwd.type = show ? 'text' : 'password';
    icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
}

/* ROLE SELECTOR */
function setRole(role) {
    const isAdmin = role === 'admin';
    document.getElementById('roleInput').value = role;
    document.getElementById('btnAdmin').classList.toggle('active', isAdmin);
    document.getElementById('btnSuperAdmin').classList.toggle('active', !isAdmin);
    document.getElementById('profileImage').src = isAdmin
        ? 'https://ui-avatars.com/api/?name=Admin+Instansi&size=100&length=2&background=1246a0&color=fff&bold=true'
        : 'https://ui-avatars.com/api/?name=Super+Admin&size=100&length=2&background=FFA500&color=fff&bold=true';
    document.getElementById('profileName').textContent = isAdmin ? 'Admin Instansi' : 'Super Admin';
    document.getElementById('profileRole').textContent = isAdmin ? 'Administrator' : 'Super Administrator';
}

/* WELCOME MESSAGE */
function updateWelcomeMessage() {
    const h = new Date().getHours();
    const [icon, text] =
        h < 12 ? ['bi-sun',       'Selamat pagi, Admin']  :
        h < 15 ? ['bi-cloud-sun', 'Selamat siang, Admin'] :
        h < 18 ? ['bi-cloud-sun', 'Selamat sore, Admin']  :
                 ['bi-moon',      'Selamat malam, Admin'];
    document.getElementById('welcomeMessage').innerHTML =
        `<i class="bi ${icon}"></i><span>${text}</span>`;
}

/* SUBMIT */
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i><span>Memproses...</span>';
});

/* INIT */
document.addEventListener('DOMContentLoaded', () => {
    updateWelcomeMessage();
    setInterval(updateWelcomeMessage, 60000);
});
</script>
</body>
</html>