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
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}?v=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="dashboard-body">

<!-- ================= NAVBAR ================= -->
<div class="navbar">
    <div class="brand-mark">
        <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
            <img src="{{ asset('assets/img/logo-bmkg.png') }}" alt="Logo BMKG">
            <div>
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

    <div class="profile">
        <div class="avatar">👤</div>
        <div class="info">
            <p class="profile-name">{{ Auth::user()->name ?? 'Admin BMKG' }}</p>
            <p class="profile-role">{{ (Auth::check() && Auth::user()->hasRole('admin')) ? 'Admin BMKG' : 'Petugas' }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
            @csrf
            <button type="submit" class="logout-link">Keluar</button>
        </form>
    </div>
</div>

<!-- ================= KONTEN HALAMAN ================= -->
{{ $slot }}

</body>
</html>
