<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="flexilecode" />
    
    <title> Dashboard || EduFinance</title>
    
    <!-- Favicon-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}" />

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/daterangepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}" />
    
    @stack('styles')

    <!-- GSAP for loader -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        /* ===== FAST PREMIUM LOADER — target total ~1.2s ===== */
        #page-loader {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: #060e24;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 0;
        }

        /* deep blue radial — baked in CSS, no animation needed */
        #page-loader::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 90% 70% at 60% 0%,   rgba(18,70,160,0.75) 0%, transparent 55%),
                radial-gradient(ellipse 60% 80% at 0%  100%, rgba(10,30,90,0.6)   0%, transparent 50%);
            pointer-events: none;
        }

        /* ── logo ── */
        #ldr-logo {
            width: 56px;
            filter: brightness(0) invert(1);
            opacity: 0;
            transform: scale(0.8);
            position: relative;
            z-index: 2;
            margin-bottom: 20px;
        }

        /* ── wordmark ── */
        #ldr-word {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: clamp(22px, 4vw, 40px);
            font-weight: 300;
            letter-spacing: 0.06em;
            color: #fff;
            opacity: 0;
            position: relative;
            z-index: 2;
            margin-bottom: 6px;
        }

        /* ── mono sub ── */
        #ldr-mono {
            font-family: 'DM Mono', monospace;
            font-size: 9px;
            letter-spacing: 0.35em;
            text-transform: uppercase;
            color: rgba(99,165,255,0.5);
            opacity: 0;
            position: relative;
            z-index: 2;
            margin-bottom: 36px;
        }

        /* ── progress bar — the HERO element ── */
        #ldr-bar-wrap {
            width: min(220px, 52vw);
            position: relative;
            z-index: 2;
        }

        #ldr-track {
            width: 100%;
            height: 1px;
            background: rgba(255,255,255,0.08);
            position: relative;
            overflow: visible;
        }

        #ldr-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, rgba(99,165,255,0.4), #fff);
            position: relative;
        }

        /* glowing orb at the tip — this is the "wow" */
        #ldr-fill::after {
            content: '';
            position: absolute;
            right: -3px; top: -4px;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #fff;
            box-shadow:
                0 0 8px  rgba(180,215,255,1),
                0 0 20px rgba(99,165,255,0.8),
                0 0 40px rgba(59,130,246,0.4);
        }

        /* ── aurora sweep — single CSS animation, costs nothing ── */
        #ldr-aurora {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 1;
        }
        #ldr-aurora::after {
            content: '';
            position: absolute;
            top: 0; bottom: 0; left: -60%;
            width: 40%;
            background: linear-gradient(90deg,
                transparent,
                rgba(99,165,255,0.04) 40%,
                rgba(180,215,255,0.09) 50%,
                rgba(99,165,255,0.04) 60%,
                transparent
            );
            animation: ldr-sweep 1.4s cubic-bezier(0.4,0,0.2,1) forwards;
            animation-delay: 0.05s;
        }
        @keyframes ldr-sweep {
            to { left: 120%; }
        }

        /* ── curtain exit ── */
        #ldr-curtain {
            position: absolute;
            inset: 0;
            background: #fff;
            transform: scaleY(0);
            transform-origin: bottom;
            z-index: 20;
            pointer-events: none;
        }

        /* hide page content until done */
        body.loading > *:not(#page-loader) { visibility: hidden; }
    </style>
    
    <!--[if lt IE 9]>
        <script src="https:oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https:oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="loading">

    <!-- ===== FAST PREMIUM LOADER ===== -->
    <div id="page-loader">
        <div id="ldr-aurora"></div>
        <img id="ldr-logo" src="{{ asset('images/logo.png') }}" alt="Logo">
        <div id="ldr-word">EduFinance</div>
        <div id="ldr-mono">Dashboard Keuangan</div>
        <div id="ldr-bar-wrap">
            <div id="ldr-track">
                <div id="ldr-fill"></div>
            </div>
        </div>
        <div id="ldr-curtain"></div>
    </div>

    <!-- Navigation Manu -->
    @include('layouts.partials.sidebar')

    <!-- Header -->
    @include('layouts.partials.header')

    <!-- Main Content -->
    <main class="nxl-container">
        @yield('content')
        @include('layouts.partials.footer')
    </main>

    <!-- ============================================ -->
    <!--           JAVASCRIPT - URUTAN PENTING!       -->
    <!-- ============================================ -->
    
    <!-- 1. JQUERY - WAJIB PERTAMA! -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- 2. BOOTSTRAP - Butuh jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- 3. VENDORS JS - Plugin dependencies -->
    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
    
    <!-- 4. MOMENT JS - Butuh jQuery, untuk daterangepicker -->
    <script src="{{ asset('assets/vendors/js/moment.min.js') }}"></script>
    
    <!-- 5. DATERANGEPICKER - Butuh jQuery + Moment -->
    <script src="{{ asset('assets/vendors/js/daterangepicker.min.js') }}"></script>
    
    <!-- 6. APEXCHARTS - Independent -->
    @if(!isset($disableCharts) || !$disableCharts)
    <script src="{{ asset('assets/vendors/js/apexcharts.min.js') }}"></script>
    @endif

    <!-- 7. CIRCLE PROGRESS - Butuh jQuery -->
    @if(!isset($disableCharts) || !$disableCharts)
    <script src="{{ asset('assets/vendors/js/circle-progress.min.js') }}"></script>
    @endif

    <!-- 8. THEME INIT - Butuh semua plugin di atas -->
    @if(!isset($disableCharts) || !$disableCharts)
    <script src="{{ asset('assets/js/common-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme-customizer-init.min.js') }}"></script>
    @endif
    
    <!-- 9. ADDITIONAL LIBRARIES -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- 10. CUSTOM SCRIPTS - Load paling akhir -->
    @stack('scripts')
    
    <script>
    // ===== FAST LOADER — total budget ~1.2s =====
    (function () {
        const logo    = document.getElementById('ldr-logo');
        const word    = document.getElementById('ldr-word');
        const mono    = document.getElementById('ldr-mono');
        const fill    = document.getElementById('ldr-fill');
        const curtain = document.getElementById('ldr-curtain');

        // Phase 1 — everything flashes in together (0 → 0.35s)
        gsap.timeline()
            .to(logo, { opacity: 1, scale: 1, duration: 0.35, ease: 'back.out(1.6)' }, 0)
            .to(word,  { opacity: 1, duration: 0.3, ease: 'power2.out' }, 0.08)
            .to(mono,  { opacity: 1, duration: 0.25, ease: 'power2.out' }, 0.16);

        // Phase 2 — bar sprints to 100% in 0.55s (starts at 0.2s)
        gsap.to(fill, {
            width: '100%',
            duration: 0.55,
            ease: 'power2.inOut',
            delay: 0.2,
            onComplete: exitLoader
        });

        // Phase 3 — curtain wipe up
        function exitLoader() {
            gsap.to(curtain, {
                scaleY: 1,
                duration: 0.38,
                ease: 'power3.inOut',
                onComplete: () => {
                    document.getElementById('page-loader').remove();
                    document.body.classList.remove('loading');
                }
            });
        }
    })();

    // ===== DEBUG =====
    document.addEventListener('DOMContentLoaded', function() {
        console.log('✅ jQuery version:', $.fn.jquery);
        console.log('✅ daterangepicker:', typeof $.fn.daterangepicker !== 'undefined' ? 'Loaded' : '❌ Not Loaded');
        console.log('✅ circleProgress:', typeof $.fn.circleProgress !== 'undefined' ? 'Loaded' : '❌ Not Loaded');
        console.log('✅ ApexCharts:', typeof ApexCharts !== 'undefined' ? 'Loaded' : '❌ Not Loaded');
    });
    </script>
</body>
</html>