<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BMKG Monitoring Pasang Surut')</title>

    {{-- Ganti dengan @vite(['resources/css/app.css','resources/js/app.js']) kalau sudah pakai build Vite --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/download.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    @stack('styles')
</head>
<body>

    <header class="navbar">
        <a href="{{ route('dashboard.index') }}" class="navbar__brand">
            <span class="navbar__logo" aria-hidden="true">
                <img src="{{ asset('images/logo-bmkg.png') }}" alt="Logo BMKG" width="40" height="40">
            </span>
            <span class="navbar__title">
                BMKG
                <small>Monitoring Pasang Surut</small>
            </span>
        </a>

        <button type="button" class="navbar__toggle" id="navToggle"
                aria-label="Buka menu" aria-expanded="false" aria-controls="navMenu">
            <svg class="icon-open" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <svg class="icon-close" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>

        <nav class="navbar__menu" id="navMenu">
            <a href="{{ route('dashboard.index') }}"
               class="navbar__link {{ request()->routeIs('dashboard.index') || request()->is('/') ? 'is-active' : '' }}">
               Dashboard
            </a>
            <a href="{{ route('download.index') }}"
               class="navbar__link {{ request()->routeIs('download.index') ? 'is-active' : '' }}">
               Download Pasang Surut
            </a>
            <a href="{{ route('lokasi.index') }}"
               class="navbar__link {{ request()->routeIs('lokasi.index') ? 'is-active' : '' }}">
               Lokasi
            </a>
            <a href="{{ route('kalender.index') }}"
               class="navbar__link {{ request()->routeIs('kalender.index') ? 'is-active' : '' }}">
               Kalender
            </a>
            @auth
    @if(Auth::user()->hasRole('admin') || Auth::user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="navbar__link" style="color: #0284c7; font-weight: 600;">
            Panel Admin
        </a>
    @endif
@else
    <a href="{{ route('login') }}" class="navbar__link" style="color: #0284c7; font-weight: 600;">
        Login Admin
    </a>
@endauth
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <script>
        // Menu hamburger (tablet & HP)
        (function () {
            var toggle = document.getElementById('navToggle');
            var menu = document.getElementById('navMenu');
            if (!toggle || !menu) return;

            function setOpen(open) {
                menu.classList.toggle('is-open', open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                toggle.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
            }

            toggle.addEventListener('click', function (e) {
                e.stopPropagation();
                setOpen(!menu.classList.contains('is-open'));
            });
            document.addEventListener('click', function (e) {
                if (!menu.contains(e.target) && !toggle.contains(e.target)) setOpen(false);
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') setOpen(false);
            });
            window.addEventListener('resize', function () {
                if (window.innerWidth > 1024) setOpen(false);
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>