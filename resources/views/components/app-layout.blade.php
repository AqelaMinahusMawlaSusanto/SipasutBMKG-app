<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BMKG Monitoring Pasang Surut') }} - Admin Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}?v=2">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="dashboard-body">

<!-- ================= NAVBAR ================= -->
<header class="navbar admin-navbar">
    <div class="brand-mark">
        <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none; min-width: 0;">
            <img src="{{ asset('assets/img/logo-bmkg.png') }}" alt="Logo BMKG">
            <div style="min-width: 0;">
                <div class="brand-name">BMKG</div>
                <div class="brand-sub">Monitoring Pasang Surut</div>
            </div>
        </a>
    </div>

    <ul class="nav-links">
        <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
        <li><a href="{{ route('admin.data') }}" class="{{ request()->routeIs('admin.data*') ? 'active' : '' }}">Data Pasang Surut</a></li>
        <li><a href="{{ route('admin.lokasi') }}" class="{{ request()->routeIs('admin.lokasi*') ? 'active' : '' }}">Lokasi</a></li>
        <li><a href="{{ route('admin.aktivitas') }}" class="{{ request()->routeIs('admin.aktivitas*') ? 'active' : '' }}">Log Aktivitas</a></li>
        <li><a href="{{ route('admin.notif') }}" class="{{ request()->routeIs('admin.notif*') ? 'active' : '' }}">Notifikasi</a></li>
        <li><a href="{{ route('dashboard.index') }}" target="_blank" style="color: #0284c7;">Web Publik ↗</a></li>
    </ul>

    <div class="navbar-right">
        <div class="profile">
            <a href="{{ route('admin.profil') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;">
                <div class="avatar">👤</div>
                <div class="info">
                    <p class="profile-name">{{ Auth::user()->name ?? 'Admin BMKG' }}</p>
                    <p class="profile-role">{{ (Auth::check() && Auth::user()->hasRole('admin')) ? 'Admin BMKG' : 'Petugas' }}</p>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;" class="desktop-logout">
                @csrf
                <button type="submit" class="logout-link">Keluar</button>
            </form>
        </div>

        <button type="button" class="admin-nav-toggle" id="adminNavToggle" aria-label="Buka menu" aria-expanded="false" aria-controls="adminMobileMenu">
            <svg class="icon-open" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg class="icon-close" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M6 6l12 12M18 6L6 18"/>
            </svg>
        </button>
    </div>

    <nav class="admin-mobile-menu" id="adminMobileMenu">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">Dashboard</a>
        <a href="{{ route('admin.data') }}" class="{{ request()->routeIs('admin.data*') ? 'is-active' : '' }}">Data Pasang Surut</a>
        <a href="{{ route('admin.lokasi') }}" class="{{ request()->routeIs('admin.lokasi*') ? 'is-active' : '' }}">Lokasi</a>
        <a href="{{ route('admin.aktivitas') }}" class="{{ request()->routeIs('admin.aktivitas*') ? 'is-active' : '' }}">Log Aktivitas</a>
        <a href="{{ route('admin.notif') }}" class="{{ request()->routeIs('admin.notif*') ? 'is-active' : '' }}">Notifikasi</a>
        <a href="{{ route('admin.profil') }}" class="{{ request()->routeIs('admin.profil*') ? 'is-active' : '' }}">Profil Admin</a>
        <a href="{{ route('dashboard.index') }}" target="_blank" style="color: #0284c7;">Web Publik ↗</a>
        <form method="POST" action="{{ route('logout') }}" style="margin: 8px 0 0;">
            @csrf
            <button type="submit" class="mobile-logout-btn">Keluar</button>
        </form>
    </nav>
</header>

<!-- ================= KONTEN HALAMAN ================= -->
{{ $slot }}

<script>
    (function () {
        var toggle = document.getElementById('adminNavToggle');
        var menu = document.getElementById('adminMobileMenu');
        if (!toggle || !menu) return;

        function setOpen(open) {
            menu.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
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
            if (window.innerWidth > 960) setOpen(false);
        });
    })();
</script>
@stack('scripts')
</body>
</html>
