<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Server Error | EduFinance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400&family=Outfit:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --white:     #ffffff;
            --offwhite:  #f7f8fc;
            --light:     #eef1f8;
            --blue:      #1246a0;
            --blue-mid:  #1a5bc4;
            --blue-lt:   #3b82f6;
            --ink:       #0d1b35;
            --muted:     #6b7a99;
            --border:    rgba(18,70,160,0.10);
            --f-display: 'Cormorant Garamond', Georgia, serif;
            --f-body:    'Outfit', system-ui, sans-serif;
            --f-mono:    'DM Mono', monospace;
        }
        html, body { height: 100%; font-family: var(--f-body); background: var(--offwhite); color: var(--ink); overflow: hidden; }

        .bg-orb {
            position: fixed; width: 700px; height: 700px; border-radius: 50%;
            background: radial-gradient(circle, rgba(18,70,160,0.06) 0%, transparent 70%);
            bottom: -200px; right: -100px; pointer-events: none;
            animation: floatOrb 10s ease-in-out infinite;
        }
        .bg-orb-2 {
            position: fixed; width: 500px; height: 500px; border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,0.04) 0%, transparent 70%);
            top: -150px; left: -100px; pointer-events: none;
            animation: floatOrb 13s ease-in-out infinite reverse;
        }
        @keyframes floatOrb { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-22px); } }
        .grid-bg {
            position: fixed; inset: 0;
            background-image: linear-gradient(rgba(18,70,160,0.025) 1px, transparent 1px), linear-gradient(90deg, rgba(18,70,160,0.025) 1px, transparent 1px);
            background-size: 60px 60px; pointer-events: none;
        }

        .logo {
            position: fixed; top: 28px; left: 40px;
            font-family: var(--f-display); font-size: 22px; font-weight: 700;
            color: var(--blue); text-decoration: none; letter-spacing: -0.02em;
        }

        .wrap {
            position: relative; z-index: 10; min-height: 100vh;
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; padding: 40px 24px; text-align: center;
        }

        .code-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(18,70,160,0.06); border: 1px solid var(--border);
            border-radius: 100px; padding: 6px 16px;
            font-family: var(--f-mono); font-size: 11px; letter-spacing: 0.2em;
            color: var(--blue-mid); text-transform: uppercase; margin-bottom: 32px;
            animation: fadeUp 0.6s ease both;
        }
        .code-badge-dot {
            width: 6px; height: 6px; border-radius: 50%; background: #ef4444;
            animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.3; transform: scale(0.7); } }

        .error-num {
            font-family: var(--f-display); font-size: clamp(100px, 18vw, 180px);
            font-weight: 700; line-height: 1; color: transparent;
            background: linear-gradient(135deg, var(--blue) 0%, var(--blue-lt) 100%);
            -webkit-background-clip: text; background-clip: text;
            letter-spacing: -0.04em; margin-bottom: 8px;
            animation: fadeUp 0.6s 0.1s ease both;
        }
        .error-title {
            font-family: var(--f-display); font-size: clamp(22px, 4vw, 36px);
            font-weight: 600; color: var(--ink); letter-spacing: -0.02em;
            margin-bottom: 16px; animation: fadeUp 0.6s 0.2s ease both;
        }
        .error-title em { font-style: italic; color: var(--blue-mid); }
        .divider {
            width: 48px; height: 1px;
            background: linear-gradient(90deg, transparent, var(--blue-mid), transparent);
            margin: 0 auto 24px; animation: fadeUp 0.6s 0.25s ease both;
        }
        .error-desc {
            font-size: 15px; color: var(--muted); max-width: 420px;
            line-height: 1.7; margin-bottom: 32px;
            animation: fadeUp 0.6s 0.3s ease both;
        }

        /* Status card */
        .status-card {
            display: flex; align-items: center; gap: 12px;
            background: white; border: 1px solid var(--border);
            border-radius: 12px; padding: 14px 20px;
            margin-bottom: 40px; max-width: 360px;
            animation: fadeUp 0.6s 0.35s ease both;
            box-shadow: 0 2px 12px rgba(18,70,160,0.06);
        }
        .status-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }
        .status-text { text-align: left; }
        .status-label { font-size: 11px; font-family: var(--f-mono); letter-spacing: 0.1em; color: var(--muted); text-transform: uppercase; }
        .status-val { font-size: 13px; font-weight: 500; color: var(--ink); margin-top: 2px; }

        .actions {
            display: flex; gap: 12px; flex-wrap: wrap; justify-content: center;
            animation: fadeUp 0.6s 0.4s ease both;
        }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--blue); color: white; text-decoration: none;
            padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 500;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 16px rgba(18,70,160,0.25); border: none; cursor: pointer;
        }
        .btn-primary:hover { background: var(--blue-mid); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(18,70,160,0.35); }
        .btn-ghost {
            display: inline-flex; align-items: center; gap: 8px;
            background: white; color: var(--ink); text-decoration: none;
            padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 500;
            border: 1px solid var(--border); transition: background 0.2s, transform 0.2s;
        }
        .btn-ghost:hover { background: var(--light); transform: translateY(-2px); }
        .footer-note {
            position: fixed; bottom: 28px;
            font-family: var(--f-mono); font-size: 10px; letter-spacing: 0.2em;
            color: var(--muted); opacity: 0.4; text-transform: uppercase;
        }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="bg-orb"></div>
    <div class="bg-orb-2"></div>
    <div class="grid-bg"></div>
    <a href="{{ url('/') }}" class="logo">EduFinance</a>

    <div class="wrap">
        <div class="code-badge">
            <span class="code-badge-dot"></span>
            Error 500
        </div>
        <div class="error-num">500</div>
        <h1 class="error-title">Server <em>Bermasalah</em></h1>
        <div class="divider"></div>
        <p class="error-desc">
            Terjadi kesalahan pada server kami. Tim teknis sudah diberitahu
            dan sedang menangani masalah ini. Silakan coba beberapa saat lagi.
        </p>

        <div class="status-card">
            <div class="status-icon">⚠️</div>
            <div class="status-text">
                <div class="status-label">Status</div>
                <div class="status-val">Internal Server Error — Sedang ditangani</div>
            </div>
        </div>

        <div class="actions">
            <button onclick="location.reload()" class="btn-ghost">↺ Coba Lagi</button>
            <a href="{{ route('dashboard') }}" class="btn-primary">Ke Dashboard →</a>
        </div>
    </div>

    <span class="footer-note">© {{ date('Y') }} EduFinance</span>
</body>
</html>