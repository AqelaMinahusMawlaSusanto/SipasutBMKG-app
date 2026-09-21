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

        <nav class="navbar__menu">
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
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
