<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFinance — Lupa Password</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600&family=Outfit:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

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

            --f-display: 'Cormorant Garamond', Georgia, serif;
            --f-body:    'Outfit', system-ui, sans-serif;
            --f-mono:    'DM Mono', monospace;

            --ease-out:    cubic-bezier(.16,1,.3,1);
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

        /* ── BACKGROUND (identik dengan hero welcome) ─────────── */
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
        a, button { cursor: none; }

        /* ── NAV (pill — identik welcome) ───────────────────────── */
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
        .nav-logo {
            display: flex; align-items: center; gap: 8px;
            text-decoration: none; flex-shrink: 0;
        }
        .nav-logo-img {
            height: 26px; width: auto;
            filter: brightness(0) invert(1); transition: filter .4s;
        }
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
            padding: 92px 52px 52px; position: relative;
        }

        /* ── CARD ────────────────────────────────────────────────── */
        .auth-card {
            max-width: 920px; width: 100%;
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
            flex: 1; min-width: 360px;
            padding: 56px 48px; position: relative;
        }

        .logo-float {
            position: absolute; top: 48px; left: 48px;
            width: 48px; height: 48px;
            background: white; border-radius: 50%;
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

        .form-header { margin-top: 64px; margin-bottom: 36px; }
        .form-header h1 {
            font-family: var(--f-display); font-size: 2.6rem;
            font-weight: 600; color: var(--ink);
            letter-spacing: -.02em; margin-bottom: 8px; line-height: 1;
        }
        .form-header p { color: var(--muted); font-size: .97rem; line-height: 1.65; }

        /* form elements */
        .form-group { margin-bottom: 24px; }
        .form-label {
            display: block; font-size: .8rem; font-weight: 600;
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
        .input-field.is-invalid { border-color: #ef4444; }
        .invalid-feedback { display: block; font-size: .8rem; color: #ef4444; margin-top: 6px; }

        /* alert */
        .alert {
            padding: 12px 16px; border-radius: 12px; margin-bottom: 24px;
            font-size: .9rem; display: flex; align-items: center; gap: 10px;
        }
        .alert-success { background: rgba(220,252,231,.9); color: #166534; border: 1px solid rgba(22,101,52,.2); }
        .alert-danger  { background: rgba(254,226,226,.9); color: #991b1b; border: 1px solid rgba(153,27,27,.2); }

        /* btn submit */
        .btn-submit {
            width: 100%; padding: 15px;
            background: var(--blue); color: white;
            border: none; border-radius: 12px;
            font-size: .97rem; font-weight: 600; font-family: var(--f-body);
            display: flex; align-items: center; justify-content: center; gap: 10px;
            transition: all .3s; margin-bottom: 20px;
        }
        .btn-submit:hover {
            background: var(--blue-mid); transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(18,70,160,.3);
        }
        .btn-submit i { font-size: 1rem; }

        .back-link {
            text-align: center; font-size: .9rem; color: var(--muted);
        }
        .back-link a {
            color: var(--blue); text-decoration: none; font-weight: 600; margin-left: 4px;
        }
        .back-link a:hover { text-decoration: underline; }

        /* ── INFO PANEL ──────────────────────────────────────────── */
        .info-panel {
            flex: 0 0 360px;
            background: linear-gradient(135deg, rgba(18,70,160,.95), rgba(11,45,120,.95));
            padding: 48px 40px;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            position: relative; overflow: hidden;
        }
        .info-panel-orb {
            position: absolute; width: 300px; height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,.25) 0%, transparent 70%);
            right: -80px; bottom: -80px; pointer-events: none;
        }
        .info-icon-wrap {
            width: 96px; height: 96px; border-radius: 50%;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem; color: white; margin-bottom: 28px;
            animation: floatBadge 3.5s ease-in-out infinite;
            position: relative; z-index: 2;
        }
        .info-title {
            font-family: var(--f-display); font-size: 1.8rem;
            font-weight: 600; color: white; text-align: center;
            letter-spacing: -.02em; line-height: 1.2; margin-bottom: 14px;
            position: relative; z-index: 2;
        }
        .info-desc {
            font-size: .88rem; color: rgba(255,255,255,.5);
            text-align: center; line-height: 1.75;
            position: relative; z-index: 2;
        }
        .info-steps {
            margin-top: 36px; width: 100%;
            display: flex; flex-direction: column; gap: 12px;
            position: relative; z-index: 2;
        }
        .info-step {
            display: flex; align-items: center; gap: 14px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 12px; padding: 14px 16px;
        }
        .info-step-num {
            font-family: var(--f-mono); font-size: 10px;
            letter-spacing: .15em; color: rgba(255,255,255,.35);
            flex-shrink: 0; width: 20px; text-align: center;
        }
        .info-step-text { font-size: .85rem; color: rgba(255,255,255,.65); }
        .info-step i { color: rgba(255,255,255,.4); font-size: .9rem; flex-shrink: 0; }

        /* ── RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 900px) {
            nav { width: calc(100% - 32px); top: 12px; padding: 0 6px 0 16px; }
            .nav-links a:not(.nav-cta) { display: none; }
            .nav-divider { display: none; }
            main { padding: 80px 20px 40px; }
            .auth-card { flex-direction: column; }
            .info-panel { flex: none; width: 100%; }
            .form-panel { min-width: unset; padding: 40px 24px; }
            .logo-float { top: 24px; left: 24px; }
            .form-header { margin-top: 44px; }
        }
    </style>
</head>
<body>

<div class="page-bg"></div>
<div class="grid-overlay"></div>
<div class="orb-bg"></div>

{{-- CURSOR --}}
<div class="cursor" id="cursor"></div>
<div class="cursor-ring" id="cursorRing"></div>

{{-- NAV --}}
<nav id="mainNav">
    <a href="/" class="nav-logo">
        <img src="{{ asset('assets/images/EDUFINANCE1.png') }}" alt="EduFinance Logo" class="nav-logo-img">
        <span class="nav-logo-text">EduFinance</span>
    </a>
    <div class="nav-links">
        <a href="/">Home</a>
        <a href="/#fitur">Fitur</a>
        <a href="/#pricing">Harga</a>
        <div class="nav-divider"></div>
        <a href="{{ route('login') }}" class="nav-cta">Masuk</a>
    </div>
</nav>

{{-- MAIN --}}
<main>
    <div class="auth-card">

        {{-- FORM PANEL --}}
        <div class="form-panel">
            <div class="logo-float">
                <img src="{{ asset('assets/images/EDUFINANCE.jpg') }}" alt="Logo">
            </div>

            <div class="form-header">
                <h1>Lupa Password?</h1>
                <p>Masukkan email Anda dan kami akan mengirimkan<br>link untuk mereset password.</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" id="resetForm">
                @csrf

                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-envelope"></i> Alamat Email
                    </label>
                    <div class="input-group">
                        <span class="input-icon"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email"
                               class="input-field {{ $errors->has('email') ? 'is-invalid' : '' }}"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="email@instansi.com"
                               required autocomplete="email" autofocus>
                    </div>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="bi bi-send"></i>
                    <span>Kirim Link Reset Password</span>
                </button>

                <div class="back-link">
                    Ingat password Anda?
                    <a href="{{ route('login') }}">Kembali ke Login</a>
                </div>
            </form>
        </div>

        {{-- INFO PANEL --}}
        <div class="info-panel">
            <div class="info-panel-orb"></div>
            <div class="info-icon-wrap">
                <i class="bi bi-shield-lock"></i>
            </div>
            <div class="info-title">Reset<br><em style="font-style:italic;font-weight:300;">Password</em></div>
            <p class="info-desc">Ikuti langkah mudah berikut untuk memulihkan akses akun EduFinance Anda.</p>
            <div class="info-steps">
                <div class="info-step">
                    <span class="info-step-num">01</span>
                    <i class="bi bi-envelope"></i>
                    <span class="info-step-text">Masukkan email yang terdaftar</span>
                </div>
                <div class="info-step">
                    <span class="info-step-num">02</span>
                    <i class="bi bi-inbox"></i>
                    <span class="info-step-text">Periksa kotak masuk email Anda</span>
                </div>
                <div class="info-step">
                    <span class="info-step-num">03</span>
                    <i class="bi bi-key"></i>
                    <span class="info-step-text">Klik link & buat password baru</span>
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
document.querySelectorAll('a, button').forEach(el => {
    el.addEventListener('mouseenter', () => { cursor.classList.add('hovered'); ring.classList.add('hovered'); });
    el.addEventListener('mouseleave', () => { cursor.classList.remove('hovered'); ring.classList.remove('hovered'); });
});

/* ── NAV SCROLL ─────────────────────────────────────────── */
window.addEventListener('scroll', () => {
    document.getElementById('mainNav').classList.toggle('scrolled', window.scrollY > 60);
});

/* ── FORM SUBMIT LOADING ─────────────────────────────────── */
document.getElementById('resetForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i><span>Mengirim...</span>';
});
</script>
</body>
</html>