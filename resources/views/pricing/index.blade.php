<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFinance — Pilih Paket Langganan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600&family=Outfit:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollToPlugin.min.js"></script>

    <style>
        /* ─── RESET & ROOT ─────────────────────────────────────── */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --white:    #ffffff;
            --offwhite: #f7f8fc;
            --light:    #eef1f8;
            --blue:     #1246a0;
            --blue-mid: #1a5bc4;
            --blue-lt:  #3b82f6;
            --blue-pale:#dde9ff;
            --ink:      #0d1b35;
            --muted:    #6b7a99;
            --border:   rgba(18,70,160,0.10);
            --border-lt:rgba(18,70,160,0.06);

            --f-display: 'Cormorant Garamond', Georgia, serif;
            --f-body:    'Outfit', system-ui, sans-serif;
            --f-mono:    'DM Mono', monospace;

            --ease-out:    cubic-bezier(0.16, 1, 0.3, 1);
            --ease-in-out: cubic-bezier(0.45, 0, 0.55, 1);
            --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        body {
            font-family: var(--f-body);
            background: var(--white);
            color: var(--ink);
            line-height: 1.6;
            overflow-x: hidden;
            cursor: none;
        }

        /* ─── CUSTOM CURSOR ──────────────────────────────── */
        .cursor {
            position: fixed;
            width: 8px; height: 8px;
            background: var(--blue);
            border-radius: 50%;
            pointer-events: none;
            z-index: 99999;
            transform: translate(-50%, -50%);
            transition: width 0.3s var(--ease-out), height 0.3s var(--ease-out);
            mix-blend-mode: multiply;
        }
        .cursor-ring {
            position: fixed;
            width: 32px; height: 32px;
            border: 1.5px solid var(--blue);
            border-radius: 50%;
            pointer-events: none;
            z-index: 99998;
            transform: translate(-50%, -50%);
            transition: width 0.4s var(--ease-out), height 0.4s var(--ease-out), opacity 0.3s, border-color 0.3s;
            opacity: 0.5;
        }
        .cursor.hovered { width: 16px; height: 16px; }
        .cursor-ring.hovered { width: 54px; height: 54px; opacity: 0.25; }
        a, button { cursor: none; }

        /* ─── NAV ────────────────────────────────────────────────── */
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
        @media (max-width: 768px) {
            nav { width: calc(100% - 32px); top: 12px; padding: 0 6px 0 16px; }
            .nav-links a:not(.nav-cta):not(.nav-wa) { display: none; }
            .nav-divider { display: none; }
        }

        /* ─── PRICING HERO (lebih soft) ────────────────────────── */
        .pricing-hero {
            background: linear-gradient(135deg, #1e3a8a, #1d4ed8 60%, #0284c7);
            padding: 120px 52px 100px;
            text-align: center;
            margin-top: 0;
            position: relative;
            overflow: hidden;
        }

        .pricing-hero h1 {
            font-family: var(--f-display);
            font-size: clamp(56px, 8vw, 96px);
            font-weight: 600;
            color: white;
            line-height: 0.92;
            letter-spacing: -0.03em;
            margin-bottom: 24px;
        }
        .pricing-hero h1 em {
            font-style: italic;
            font-weight: 300;
            background: linear-gradient(135deg, #93c5fd 0%, #60a5fa 40%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .pricing-hero p {
            color: rgba(255,255,255,.85);
            font-size: 1.1rem;
            max-width: 550px;
            margin: 0 auto;
            font-weight: 300;
        }

        .pricing-hero .badge-top {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(5px);
            color: #fff;
            font-size: .8rem;
            font-weight: 500;
            padding: 6px 16px;
            border-radius: 50px;
            letter-spacing: 0.5px;
            margin-bottom: 24px;
            border: 1px solid rgba(255,255,255,0.2);
            font-family: var(--f-mono);
        }

        /* ─── CARDS WRAPPER ───────────────────────────────────── */
        .cards-wrapper {
            padding: 60px 52px 80px;
            background: var(--white);
        }

        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 60px;
        }

        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: var(--f-mono);
            font-size: 10.5px;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: var(--blue);
            margin-bottom: 20px;
        }
        .section-tag::before {
            content: '';
            width: 28px; height: 1px;
            background: var(--blue);
            opacity: 0.4;
        }

        .section-h2 {
            font-family: var(--f-display);
            font-size: clamp(42px, 5.5vw, 64px);
            font-weight: 600;
            line-height: 1;
            letter-spacing: -0.03em;
            color: var(--ink);
        }
        .section-h2 em {
            font-style: italic;
            font-weight: 300;
            color: var(--blue-mid);
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .price-card {
            background: white;
            border: 1px solid var(--border-lt);
            border-radius: 14px;
            padding: 44px 36px;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            transition: transform 0.4s var(--ease-out), box-shadow 0.4s, border-color 0.3s;
        }
        .price-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 32px 64px rgba(18,70,160,0.1);
            border-color: rgba(18,70,160,0.18);
        }
        .price-card.featured {
            background: linear-gradient(145deg, var(--blue) 0%, #0f3a88 100%);
            border-color: transparent;
        }
        .price-card.featured:hover {
            box-shadow: 0 32px 64px rgba(18,70,160,0.3);
        }
        .price-badge {
            position: absolute;
            top: 20px; right: 20px;
            font-family: var(--f-mono);
            font-size: 9.5px;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            background: rgba(255,255,255,0.18);
            color: white;
            padding: 4px 10px;
            border-radius: 100px;
        }
        .price-tier {
            font-family: var(--f-mono);
            font-size: 10.5px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 28px;
        }
        .featured .price-tier { color: rgba(255,255,255,0.4); }
        .price-amount {
            font-family: var(--f-display);
            font-size: clamp(38px, 4.5vw, 56px);
            font-weight: 600;
            color: var(--ink);
            letter-spacing: -0.03em;
            line-height: 1;
            margin-bottom: 6px;
        }
        .featured .price-amount { color: white; }
        .price-period {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 36px;
        }
        .featured .price-period { color: rgba(255,255,255,0.4); }
        .price-divider {
            height: 1px;
            background: var(--border-lt);
            margin-bottom: 28px;
        }
        .featured .price-divider { background: rgba(255,255,255,0.1); }
        .price-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 36px;
            flex: 1;
        }
        .price-features li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 14px;
            color: var(--muted);
            line-height: 1.5;
        }
        .featured .price-features li { color: rgba(255,255,255,0.55); }
        .price-features li::before {
            content: '';
            width: 16px; height: 16px;
            flex-shrink: 0;
            margin-top: 1px;
            background: url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='8' cy='8' r='7.5' stroke='%231246a0' stroke-opacity='0.2'/%3E%3Cpath d='M5 8l2 2 4-4' stroke='%231246a0' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") center/contain no-repeat;
        }
        .featured .price-features li::before {
            background: url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='8' cy='8' r='7.5' stroke='white' stroke-opacity='0.3'/%3E%3Cpath d='M5 8l2 2 4-4' stroke='white' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") center/contain no-repeat;
        }

        .btn-price {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.01em;
            border-radius: 8px;
            border: 1.5px solid var(--border);
            color: var(--blue);
            background: transparent;
            transition: all 0.3s var(--ease-out);
        }
        .btn-price:hover {
            background: var(--blue);
            border-color: var(--blue);
            color: white;
            transform: translateY(-1px);
        }
        .btn-price-featured {
            background: white;
            border-color: transparent;
            color: var(--blue);
        }
        .btn-price-featured:hover {
            background: var(--blue-pale);
            color: var(--blue);
            transform: translateY(-1px);
        }

        /* ─── ALERT INFO (UNTUK USER LOGIN) ───────────────────────── */
        .alert-info {
            background-color: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 12px;
            padding: 16px 24px;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .alert-info i {
            font-size: 24px;
            color: var(--blue);
        }
        .alert-info-content {
            flex: 1;
            font-size: 14px;
            color: var(--ink);
        }
        .alert-info-content strong {
            color: var(--blue);
        }
        .alert-info .btn-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--muted);
        }

        /* ─── TRUST BAR ──────────────────────────────────────── */
        .trust-bar {
            background: var(--offwhite);
            border-top: 1px solid var(--border-lt);
            border-bottom: 1px solid var(--border-lt);
            padding: 60px 52px;
        }

        .trust-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .trust-item {
            text-align: center;
            transition: transform 0.3s var(--ease-out);
        }
        .trust-item:hover {
            transform: translateY(-4px);
        }
        .trust-item i {
            font-size: 2.2rem;
            color: var(--blue);
            display: block;
            margin-bottom: 12px;
        }
        .trust-item .lbl {
            font-weight: 600;
            font-size: .95rem;
            color: var(--ink);
            margin-bottom: 4px;
        }
        .trust-item .sub {
            color: var(--muted);
            font-size: .8rem;
        }

        /* ─── FAQ SECTION ─────────────────────────────────────── */
        .faq-section {
            padding: 80px 52px;
            background: var(--white);
        }

        .faq-container {
            max-width: 800px;
            margin: 40px auto 0;
        }

        .faq-item {
            border-bottom: 1px solid var(--border-lt);
            padding: 24px 0;
            cursor: pointer;
        }
        .faq-q {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }
        .faq-q-text {
            font-family: var(--f-display);
            font-size: 18px;
            font-weight: 600;
            color: var(--ink);
            transition: color 0.3s;
        }
        .faq-item:hover .faq-q-text { color: var(--blue); }
        .faq-toggle {
            width: 26px; height: 26px;
            border: 1.5px solid var(--border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            transition: all 0.3s var(--ease-out);
            font-size: 16px;
        }
        .faq-item.open .faq-toggle {
            background: var(--blue);
            border-color: var(--blue);
            color: white;
            transform: rotate(45deg);
        }
        .faq-a {
            font-size: 14.5px;
            color: var(--muted);
            line-height: 1.85;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s var(--ease-out);
        }
        .faq-item.open .faq-a {
            max-height: 200px;
            margin-top: 16px;
        }

        /* ─── CTA ─────────────────────────────────────────────── */
        .cta-section {
            background: linear-gradient(145deg, var(--blue) 0%, #0b2d78 100%);
            padding: 80px 52px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .cta-section h2 {
            font-family: var(--f-display);
            font-size: clamp(40px, 5vw, 64px);
            font-weight: 600;
            color: white;
            line-height: 1;
            letter-spacing: -0.03em;
            margin-bottom: 24px;
        }
        .cta-section h2 em {
            font-style: italic;
            font-weight: 300;
            color: #93c5fd;
        }
        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: white;
            color: var(--blue);
            padding: 15px 32px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s var(--ease-out);
            box-shadow: 0 4px 24px rgba(0,0,0,0.2);
        }
        .cta-button:hover {
            background: var(--blue-pale);
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
        }

        /* ─── FOOTER (sama persis dengan welcome) ───────────────── */
        footer {
            background: var(--ink);
            padding: 64px 52px 36px;
        }
        .footer-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 48px;
            padding-bottom: 48px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 36px;
        }
        .footer-brand { max-width: 280px; }
        .footer-logo {
            font-family: var(--f-display);
            font-size: 26px;
            font-weight: 600;
            color: white;
            letter-spacing: -0.02em;
            margin-bottom: 12px;
        }
        .footer-tagline {
            font-size: 13.5px;
            color: rgba(255,255,255,0.28);
            line-height: 1.7;
        }
        .footer-links { display: flex; gap: 64px; }
        .footer-col { display: flex; flex-direction: column; gap: 14px; }
        .footer-col-title {
            font-family: var(--f-mono);
            font-size: 10px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.25);
            margin-bottom: 4px;
        }
        .footer-col a {
            font-size: 13.5px;
            color: rgba(255,255,255,0.45);
            text-decoration: none;
            transition: color 0.3s;
        }
        .footer-col a:hover { color: rgba(255,255,255,0.85); }
        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }
        .footer-copy, .footer-love {
            font-family: var(--f-mono);
            font-size: 10.5px;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.18);
        }

        /* ─── REVEAL ANIMATIONS ───────────────────────────────── */
        .reveal {
            opacity: 0;
            transform: translateY(36px);
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.85s var(--ease-out), transform 0.85s var(--ease-out);
        }

        /* ─── RESPONSIVE ──────────────────────────────────────── */
        @media (max-width: 1024px) {
            nav, .pricing-hero, .cards-wrapper, .trust-bar, .faq-section, .cta-section, footer {
                padding-left: 32px;
                padding-right: 32px;
            }
            .pricing-grid { grid-template-columns: repeat(2, 1fr); }
            .trust-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            nav { padding: 0 20px; }
            .nav-links a:not(.nav-cta) { display: none; }
            .pricing-grid { grid-template-columns: 1fr; max-width: 400px; margin: 0 auto; }
            .trust-grid { grid-template-columns: 1fr; }
            .footer-top { flex-direction: column; }
            .footer-links { gap: 32px; flex-wrap: wrap; }
            .pricing-hero h1 { font-size: clamp(40px, 10vw, 56px); }
            .cards-wrapper, .trust-bar, .faq-section, .cta-section, footer {
                padding-left: 20px;
                padding-right: 20px;
            }
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

    <!-- CURSOR -->
    <div class="cursor" id="cursor"></div>
    <div class="cursor-ring" id="cursorRing"></div>

    <!-- WA FLOATING BUTTON -->
    <a href="https://wa.me/62895356753500" target="_blank" class="wa-float" rel="noopener noreferrer" title="Hubungi via WhatsApp">
        <span class="wa-float-label">💬 Hubungi Kami via WA</span>
        <div class="wa-float-btn" style="position:relative;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            <div class="wa-pulse"></div>
        </div>
    </a>

    <!-- NAV -->
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

    <!-- PRICING HERO -->
    <div class="pricing-hero">
        <div class="badge-top reveal">HARGA TRANSPARAN</div>
        <h1 class="reveal" style="transition-delay: 0.1s;">Pilih Paket<br><em>Langganan</em></h1>
        <p class="reveal" style="transition-delay: 0.2s;">Kelola keuangan sekolah lebih mudah, tertib, dan transparan. Semua paket sudah termasuk akses penuh ke seluruh fitur.</p>
    </div>

    <!-- KARTU PAKET -->
    <div class="cards-wrapper">
        <div class="section-header reveal">
            <div class="section-tag">PAKET</div>
            <h2 class="section-h2">Sesuaikan dengan<br><em>Kebutuhan Instansi</em></h2>
        </div>

        {{-- ALERT UNTUK USER YANG SUDAH LOGIN --}}
        @if($isLoggedIn)
        <div class="alert-info reveal" style="max-width: 1200px; margin: 0 auto 32px auto;">
            <i>🔔</i>
            <div class="alert-info-content">
                <strong>Halo, {{ $userData['name'] }}!</strong>
                @if($currentLicense && $currentLicense['is_active'])
                    Anda sedang berlangganan paket <strong>{{ ucfirst($currentLicense['package_type']) }}</strong>.
                    Lisensi aktif hingga <strong>{{ \Carbon\Carbon::parse($currentLicense['expired_at'])->format('d/m/Y') }}</strong>
                    (sisa {{ $currentLicense['days_left'] }} hari).
                    <br>Pilih paket di bawah untuk <strong>memperpanjang</strong> atau <strong>upgrade</strong> lisensi. Pembelian akan langsung diproses tanpa isi form.
                @else
                    Anda belum memiliki lisensi aktif. Pilih paket di bawah untuk membeli lisensi (langsung diproses).
                @endif
            </div>
        </div>
        @endif

        <div class="pricing-grid">
            <!-- Bulanan -->
            <div class="price-card reveal">
                <div class="price-tier">Bulanan</div>
                <div class="price-amount">Rp 100K</div>
                <div class="price-period">per bulan · berlaku 30 hari</div>
                <div class="price-divider"></div>
                <ul class="price-features">
                    <li>Akses penuh semua fitur</li>
                    <li>Support via WhatsApp</li>
                    <li>Update gratis</li>
                    <li>Backup data</li>
                    <li>Lisensi 1 perangkat aktif</li>
                </ul>
                <a href="{{ route('checkout.page', 'monthly') }}" class="btn-price">
                    @if($isLoggedIn && $currentLicense && $currentLicense['is_active'])
                        Perpanjang / Upgrade →
                    @else
                        Pilih Paket Bulanan →
                    @endif
                </a>
            </div>

            <!-- Tahunan (Populer) -->
            <div class="price-card featured reveal" style="transition-delay: 0.1s;">
                <div class="price-badge">PALING POPULER</div>
                <div class="price-tier">Tahunan</div>
                <div class="price-amount">Rp 1 Juta</div>
                <div class="price-period">per tahun · hemat Rp 200.000</div>
                <div class="price-divider"></div>
                <ul class="price-features">
                    <li>Akses penuh semua fitur</li>
                    <li>Support prioritas</li>
                    <li>Update gratis</li>
                    <li>Backup data</li>
                    <li>Lisensi 1 perangkat aktif</li>
                    <li>Hemat 2 bulan</li>
                </ul>
                <a href="{{ route('checkout.page', 'yearly') }}" class="btn-price btn-price-featured">
                    @if($isLoggedIn && $currentLicense && $currentLicense['is_active'])
                        Perpanjang / Upgrade →
                    @else
                        Pilih Paket Tahunan →
                    @endif
                </a>
            </div>

            <!-- Lifetime -->
            <div class="price-card reveal" style="transition-delay: 0.2s;">
                <div class="price-tier">Lifetime</div>
                <div class="price-amount">Rp 5 Juta</div>
                <div class="price-period">sekali bayar · seumur hidup</div>
                <div class="price-divider"></div>
                <ul class="price-features">
                    <li>Akses penuh selamanya</li>
                    <li>Support prioritas</li>
                    <li>Update gratis selamanya</li>
                    <li>Backup data otomatis</li>
                    <li>Lisensi 1 perangkat aktif</li>
                    <li>Instalasi & setup gratis</li>
                </ul>
                <a href="{{ route('checkout.page', 'lifetime') }}" class="btn-price">
                    @if($isLoggedIn && $currentLicense && $currentLicense['is_active'])
                        Perpanjang / Upgrade →
                    @else
                        Pilih Paket Lifetime →
                    @endif
                </a>
            </div>
        </div>

        @if($isLoggedIn && $currentLicense && $currentLicense['is_active'])
        <div class="alert-info reveal" style="max-width: 1200px; margin: 32px auto 0 auto; background: rgba(255, 193, 7, 0.1); border-color: rgba(255, 193, 7, 0.2);">
            <i>ℹ️</i>
            <div class="alert-info-content">
                <strong>Informasi Perpanjangan:</strong>
                Jika Anda membeli paket baru, masa aktif akan <strong>ditambahkan</strong> ke tanggal expired saat ini.
                Contoh: Lisensi expired 31 Jan + beli paket 30 hari → expired baru 2 Maret.
            </div>
        </div>
        @endif
    </div>

    <!-- TRUST BAR -->
    <div class="trust-bar">
        <div class="trust-grid">
            <div class="trust-item reveal">
                <i>🛡️</i>
                <div class="lbl">Pembayaran Aman</div>
                <div class="sub">Diproses oleh Midtrans</div>
            </div>
            <div class="trust-item reveal" style="transition-delay: 0.05s;">
                <i>📧</i>
                <div class="lbl">Lisensi via Email & WA</div>
                <div class="sub">Otomatis setelah bayar</div>
            </div>
            <div class="trust-item reveal" style="transition-delay: 0.1s;">
                <i>🏢</i>
                <div class="lbl">Data Terpisah</div>
                <div class="sub">Privasi terjamin</div>
            </div>
            <div class="trust-item reveal" style="transition-delay: 0.15s;">
                <i>🎧</i>
                <div class="lbl">Support Aktif</div>
                <div class="sub">Respon via WhatsApp</div>
            </div>
        </div>
    </div>

    <!-- FAQ -->
    <div class="faq-section">
        <div class="section-header reveal">
            <div class="section-tag">FAQ</div>
            <h2 class="section-h2">Pertanyaan<br><em>Umum</em></h2>
        </div>

        <div class="faq-container">
            <div class="faq-item reveal">
                <div class="faq-q">
                    <div class="faq-q-text">Apa itu kode lisensi?</div>
                    <div class="faq-toggle">+</div>
                </div>
                <div class="faq-a">Kode lisensi adalah kunci unik yang dikirim ke email & WhatsApp Anda setelah pembayaran sukses. Kode ini digunakan saat membuat akun di halaman registrasi.</div>
            </div>
            <div class="faq-item reveal" style="transition-delay: 0.05s;">
                <div class="faq-q">
                    <div class="faq-q-text">Apakah data saya aman?</div>
                    <div class="faq-toggle">+</div>
                </div>
                <div class="faq-a">Ya. Setiap sekolah memiliki data yang sepenuhnya terisolasi. Admin sekolah lain tidak bisa melihat atau mengakses data sekolah Anda.</div>
            </div>
            <div class="faq-item reveal" style="transition-delay: 0.1s;">
                <div class="faq-q">
                    <div class="faq-q-text">Bisa pakai di banyak perangkat?</div>
                    <div class="faq-toggle">+</div>
                </div>
                <div class="faq-a">Satu lisensi untuk satu akun dengan satu sesi aktif. Jika login di perangkat baru, sesi perangkat lama otomatis berakhir.</div>
            </div>
            <div class="faq-item reveal" style="transition-delay: 0.15s;">
                <div class="faq-q">
                    <div class="faq-q-text">Bagaimana cara perpanjang lisensi?</div>
                    <div class="faq-toggle">+</div>
                </div>
                <div class="faq-a">Beli paket baru di halaman Harga kapan saja. Masa aktif akan ditambahkan ke tanggal expired saat ini. Kode lisensi baru akan dikirim ke email & WA Anda.</div>
            </div>
            <div class="faq-item reveal" style="transition-delay: 0.2s;">
                <div class="faq-q">
                    <div class="faq-q-text">Metode pembayaran apa yang diterima?</div>
                    <div class="faq-toggle">+</div>
                </div>
                <div class="faq-a">Semua metode via Midtrans: QRIS, transfer bank (BCA, BNI, BRI, Mandiri, dll), GoPay, OVO, Dana, ShopeePay, dan kartu kredit/debit.</div>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="cta-section">
        <h2 class="reveal">Siap Kelola Keuangan<br><em>Lebih Baik?</em></h2>
        <a href="{{ route('checkout.page', 'yearly') }}" class="cta-button reveal" style="transition-delay: 0.1s;">
            Mulai Sekarang →
        </a>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="footer-top">
            <div class="footer-brand">
                <div class="footer-logo">EduFinance</div>
                <p class="footer-tagline">Aplikasi manajemen keuangan sekolah modern &amp; terpercaya untuk Indonesia.</p>
            </div>
            <div class="footer-links">
                <div class="footer-col">
                    <div class="footer-col-title">Platform</div>
                    <a href="/#fitur">Fitur</a>
                    <a href="{{ route('pricing') }}">Harga</a>
                    <a href="{{ route('login') }}">Masuk</a>
                    <a href="{{ route('register') }}">Daftar</a>
                </div>
                <div class="footer-col">
                    <div class="footer-col-title">Bantuan</div>
                    <a href="#faq">FAQ</a>
                    <a href="https://wa.me/62895356753500" target="_blank">WhatsApp</a>
                    <a href="mailto:support@edufinance.com">Email</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-copy">© {{ date('Y') }} EduFinance. All rights reserved.</div>
            <div class="footer-love">Made with ❤ for Indonesian Schools</div>
        </div>
    </footer>

    <script>
        // Cursor
        const cursor = document.getElementById('cursor');
        const ring = document.getElementById('cursorRing');
        let mx = 0, my = 0, rx = 0, ry = 0;
        
        document.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; });
        
        function animCursor() {
            cursor.style.left = mx + 'px'; cursor.style.top = my + 'px';
            rx += (mx - rx) * 0.11; ry += (my - ry) * 0.11;
            ring.style.left = rx + 'px'; ring.style.top = ry + 'px';
            requestAnimationFrame(animCursor);
        }
        animCursor();

        // Hover effects
        document.querySelectorAll('a, button, .faq-item, .price-card').forEach(el => {
            el.addEventListener('mouseenter', () => { cursor.classList.add('hovered'); ring.classList.add('hovered'); });
            el.addEventListener('mouseleave', () => { cursor.classList.remove('hovered'); ring.classList.remove('hovered'); });
        });

        // Nav scroll
        const nav = document.getElementById('mainNav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 60) nav.classList.add('scrolled');
            else nav.classList.remove('scrolled');
        });

        // Reveal on scroll
        const revealObs = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) { 
                    e.target.classList.add('visible'); 
                    revealObs.unobserve(e.target); 
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -50px 0px' });
        
        document.querySelectorAll('.reveal').forEach(el => revealObs.observe(el));

        // FAQ Accordion
        document.querySelectorAll('.faq-item').forEach(item => {
            item.querySelector('.faq-q').addEventListener('click', () => {
                const isOpen = item.classList.contains('open');
                document.querySelectorAll('.faq-item.open').forEach(o => o.classList.remove('open'));
                if (!isOpen) item.classList.add('open');
            });
        });

        // Smooth anchor scroll
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const t = document.querySelector(a.getAttribute('href'));
                if (!t) return;
                e.preventDefault();
                gsap.to(window, { duration: 1.1, scrollTo: { y: t, offsetY: 72 }, ease: 'power3.inOut' });
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollToPlugin.min.js"></script>
</body>
</html>