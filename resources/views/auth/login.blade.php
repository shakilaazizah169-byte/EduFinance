<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFinance — Login</title>

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

        /* ── BACKGROUND (identik hero welcome) ───────────────────── */
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
        a, button, .role-btn { cursor: none; }

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
        .login-card {
            max-width: 1000px; width: 100%;
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
            flex: 1; min-width: 400px;
            padding: 48px; position: relative; background: transparent;
        }
        .logo-float {
            position: absolute; top: 48px; left: 48px;
            width: 48px; height: 48px; background: white; border-radius: 50%;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,.2);
            display: flex; align-items: center; justify-content: center;
            animation: floatBadge 3s ease-in-out infinite;
            border: 2px solid var(--blue);
        }
        .logo-float img { width: 32px; height: 32px; object-fit: contain; }
        @keyframes floatBadge {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-5px); }
        }
        .form-header { margin-top: 60px; margin-bottom: 32px; }
        .form-header h1 {
            font-family: var(--f-display); font-size: 2.5rem;
            font-weight: 600; color: var(--ink);
            letter-spacing: -.02em; margin-bottom: 8px;
        }
        .form-header p { color: var(--muted); font-size: 1rem; }

        /* Role Selector */
        .role-selector {
            display: flex; gap: 12px;
            background: rgba(0,0,0,.05);
            padding: 6px; border-radius: 60px; margin-bottom: 32px;
        }
        .role-btn {
            flex: 1; padding: 12px 20px;
            border: none; background: transparent;
            border-radius: 60px; font-size: .95rem; font-weight: 500;
            color: var(--muted);
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all .3s; font-family: var(--f-body);
        }
        .role-btn i { font-size: 1.1rem; }
        .role-btn.active {
            background: white; color: var(--blue);
            box-shadow: 0 4px 12px rgba(18,70,160,.15);
        }
        .role-btn:hover:not(.active) { background: rgba(255,255,255,.5); color: var(--ink); }

        /* Form Elements */
        .form-group { margin-bottom: 24px; }
        .form-label {
            display: block; font-size: .85rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .5px;
            color: var(--muted); margin-bottom: 8px;
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
        .toggle-password {
            padding: 0 16px; background: #f8fafc;
            border: none; border-left: 1.5px solid var(--border);
            color: var(--muted); transition: all .3s;
        }
        .toggle-password:hover { color: var(--blue); background: var(--blue-pale); }

        /* Form Options */
        .form-options {
            display: flex; align-items: center;
            justify-content: space-between; margin-bottom: 28px;
        }
        .checkbox { display: flex; align-items: center; gap: 8px; }
        .checkbox input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--blue); }
        .checkbox span { font-size: .9rem; color: var(--ink); }
        .forgot-link {
            color: var(--blue); text-decoration: none;
            font-size: .9rem; font-weight: 500;
        }
        .forgot-link:hover { text-decoration: underline; }

        /* Submit */
        .btn-submit {
            width: 100%; padding: 16px;
            background: var(--blue); color: white;
            border: none; border-radius: 12px;
            font-size: 1rem; font-weight: 600; font-family: var(--f-body);
            display: flex; align-items: center; justify-content: center; gap: 10px;
            transition: all .3s; margin-bottom: 24px;
        }
        .btn-submit:hover {
            background: var(--blue-mid); transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(18,70,160,.3);
        }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit i { font-size: 1.1rem; }

        .register-link { text-align: center; color: var(--muted); font-size: .95rem; }
        .register-link a { color: var(--blue); text-decoration: none; font-weight: 600; margin-left: 5px; }
        .register-link a:hover { text-decoration: underline; }

        /* Alert */
        .alert { padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; }
        .alert-danger  { background: rgba(254,226,226,.9); color: #991b1b; border: 1px solid rgba(153,27,27,.2); }
        .alert-success { background: rgba(220,252,231,.9); color: #166534; border: 1px solid rgba(22,101,52,.2); }

        /* ── INFO PANEL ──────────────────────────────────────────── */
        .info-panel {
            flex: 1; min-width: 400px;
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
        .profile-card {
            background: rgba(255,255,255,.1); backdrop-filter: blur(10px);
            border-radius: 24px; padding: 32px; width: 100%; max-width: 320px;
            text-align: center; border: 1px solid rgba(255,255,255,.2);
            animation: floatBadge 3s ease-in-out infinite;
            position: relative; z-index: 2;
        }
        .profile-image {
            width: 100px; height: 100px; border-radius: 50%;
            border: 3px solid white; margin: 0 auto 16px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,.3);
            display: block;
        }
        .profile-name {
            font-size: 1.5rem; font-weight: 600; color: white; margin-bottom: 4px;
            font-family: var(--f-display); text-shadow: 2px 2px 4px rgba(0,0,0,.3);
        }
        .profile-role {
            display: inline-block; background: rgba(255,255,255,.15);
            padding: 4px 16px; border-radius: 50px; font-size: .8rem;
            color: white; border: 1px solid rgba(255,255,255,.3); margin-bottom: 24px;
        }
        .stats { background: rgba(0,0,0,.2); border-radius: 16px; padding: 16px; }
        .stat-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 8px 0; color: white; border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .stat-item:last-child { border-bottom: none; }
        .stat-label { display: flex; align-items: center; gap: 8px; font-size: .9rem; text-shadow: 1px 1px 2px rgba(0,0,0,.3); }
        .stat-value {
            background: var(--gold); color: var(--ink);
            padding: 2px 12px; border-radius: 50px;
            font-weight: 600; font-size: .85rem; box-shadow: 0 2px 5px rgba(0,0,0,.2);
        }
        .welcome-message {
            margin-top: 20px; padding: 12px 20px;
            background: rgba(0,0,0,.3); border-radius: 50px; color: white;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            font-size: .9rem; border: 1px solid rgba(255,255,255,.2); backdrop-filter: blur(5px);
        }
        .welcome-message i { color: var(--gold); }
        .floating-icon { position: absolute; color: rgba(255,255,255,.1); animation: floatBadge 4s ease-in-out infinite; }
        .floating-icon:nth-child(1) { top: 10%; left: 10%; font-size: 2rem; }
        .floating-icon:nth-child(2) { top: 20%; right: 15%; font-size: 2.5rem; animation-delay: 1s; }
        .floating-icon:nth-child(3) { bottom: 20%; left: 15%; font-size: 2rem; animation-delay: 2s; }

        /* ── RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 900px) {
            nav { width: calc(100% - 32px); top: 12px; padding: 0 6px 0 16px; }
            .nav-links a:not(.nav-cta) { display: none; }
            .nav-divider { display: none; }
            main { padding: 80px 20px 40px; }
            .login-card { flex-direction: column; }
            .form-panel, .info-panel { min-width: 100%; }
            .form-panel { padding: 32px 24px; }
            .logo-float { top: 24px; left: 24px; }
            .form-header { margin-top: 40px; }
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
            <a href="{{ route('register') }}" class="nav-cta">Daftar</a>
        @endauth
    </div>
</nav>

{{-- MAIN --}}
<main>
    <div class="login-card">

        {{-- FORM PANEL --}}
        <div class="form-panel">
            <div class="logo-float">
                <img src="{{ asset('assets/images/EDUFINANCE.jpg') }}" alt="Logo">
            </div>

            <div class="form-header">
                <h1>Selamat Datang</h1>
                <p>Masuk ke akun EduFinance Anda</p>
            </div>

            {{-- Role Selector --}}
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
            @if(session('status'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                <input type="hidden" name="role" id="roleInput" value="admin">

                <div class="form-group">
                    <label class="form-label"><i class="bi bi-envelope"></i> Email</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" class="input-field" name="email"
                               value="{{ old('email') }}" placeholder="email@sekolah.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="bi bi-lock"></i> Password</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="input-field" name="password"
                               id="password" placeholder="••••••••" required>
                        <button type="button" class="toggle-password" onclick="togglePassword()">
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

        {{-- INFO PANEL --}}
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
                        <span class="stat-value" id="totalSekolah">1,234+</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label"><i class="bi bi-people"></i> Pengguna Aktif</span>
                        <span class="stat-value" id="penggunaAktif">5,678+</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label"><i class="bi bi-graph-up"></i> Transaksi/hari</span>
                        <span class="stat-value" id="transaksiHari">10K+</span>
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
document.querySelectorAll('a, button, .role-btn').forEach(el => {
    el.addEventListener('mouseenter', () => { cursor.classList.add('hovered'); ring.classList.add('hovered'); });
    el.addEventListener('mouseleave', () => { cursor.classList.remove('hovered'); ring.classList.remove('hovered'); });
});

/* ── NAV SCROLL ─────────────────────────────────────────── */
window.addEventListener('scroll', () => {
    document.getElementById('mainNav').classList.toggle('scrolled', window.scrollY > 60);
});

/* ── TOGGLE PASSWORD ─────────────────────────────────────── */
function togglePassword() {
    const pwd  = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.classList.replace('bi-eye','bi-eye-slash');
    } else {
        pwd.type = 'password';
        icon.classList.replace('bi-eye-slash','bi-eye');
    }
}

/* ── ROLE SELECTOR ───────────────────────────────────────── */
function setRole(role) {
    const roleInput      = document.getElementById('roleInput');
    const btnAdmin       = document.getElementById('btnAdmin');
    const btnSuperAdmin  = document.getElementById('btnSuperAdmin');
    const profileImage   = document.getElementById('profileImage');
    const profileName    = document.getElementById('profileName');
    const profileRole    = document.getElementById('profileRole');

    roleInput.value = role;

    if (role === 'admin') {
        btnAdmin.classList.add('active');
        btnSuperAdmin.classList.remove('active');
        profileImage.src = 'https://ui-avatars.com/api/?name=Admin+Instansi&size=100&length=2&background=1246a0&color=fff&bold=true';
        profileName.textContent = 'Admin Instansi';
        profileRole.textContent = 'Administrator';
    } else {
        btnAdmin.classList.remove('active');
        btnSuperAdmin.classList.add('active');
        profileImage.src = 'https://ui-avatars.com/api/?name=Super+Admin&size=100&length=2&background=FFA500&color=fff&bold=true';
        profileName.textContent = 'Super Admin';
        profileRole.textContent = 'Super Administrator';
    }
}

/* ── WELCOME MESSAGE BY TIME ─────────────────────────────── */
function updateWelcomeMessage() {
    const hour = new Date().getHours();
    const el = document.getElementById('welcomeMessage');
    let icon, text;
    if      (hour < 12) { icon = 'bi-sun';       text = 'Selamat pagi, Admin'; }
    else if (hour < 15) { icon = 'bi-cloud-sun';  text = 'Selamat siang, Admin'; }
    else if (hour < 18) { icon = 'bi-cloud-sun';  text = 'Selamat sore, Admin'; }
    else                { icon = 'bi-moon';        text = 'Selamat malam, Admin'; }
    el.innerHTML = `<i class="bi ${icon}"></i><span>${text}</span>`;
}

/* ── FORM SUBMIT LOADING ─────────────────────────────────── */
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i><span>Memproses...</span>';
});

/* ── INIT ────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function() {
    updateWelcomeMessage();
    setInterval(updateWelcomeMessage, 60000);
});
</script>
</body>
</html>