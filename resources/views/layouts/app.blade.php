<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BMKG Monitoring Pasang Surut') }}</title>

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
        <img src="{{ asset('assets/img/logo-bmkg.png') }}" alt="Logo BMKG">
        <div>
            <div class="brand-name">BMKG</div>
            <div class="brand-sub">Monitoring Pasang Surut</div>
        </div>
    </div>

    <ul class="nav-links">
        <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
        <li><a href="#">Data Pasang Surut</a></li>
        <li><a href="#">Lokasi</a></li>
        <li><a href="#">Log Aktivitas</a></li>
        <li><a href="#">Notifikasi</a></li>
    </ul>

    <div class="profile">
        <div class="avatar">👤</div>
        <div class="info">
            <p class="profile-name">{{ Auth::user()->name }}</p>
            <p class="profile-role">Petugas</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-link">Keluar</button>
        </form>
    </div>
</div>

<!-- ================= KONTEN HALAMAN ================= -->
{{ $slot }}

</body>
</html>