<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'BMKG Monitoring Pasang Surut') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}?v=1">
</head>
<body class="landing-body">

<div class="landing-navbar">
    <div class="brand-mark">
        <img src="{{ asset('assets/img/logo-bmkg.png') }}" alt="Logo BMKG">
        <div>
            <div class="brand-name">BMKG</div>
            <div class="brand-sub">Monitoring Pasang Surut</div>
        </div>
    </div>

    <div class="landing-nav-actions">
        @auth
            <a href="{{ route('dashboard') }}" class="btn-filled">Ke Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="btn-outline">Login</a>
            <a href="{{ route('register') }}" class="btn-filled">Daftar</a>
        @endauth
    </div>
</div>

<div class="landing-hero">
    <div class="landing-hero-text">
        <h1>Pantau Pasang Surut.<br>Lebih Mudah, Lebih Akurat.</h1>
        <p>Sistem monitoring pasang surut air laut BMKG Tanjung Perak Surabaya — akses data stasiun, prediksi, dan peringatan dini secara terintegrasi untuk mendukung kebutuhan monitoring dan analisis.</p>

        @guest
            <div class="landing-cta">
                <a href="{{ route('login') }}" class="btn-filled">Masuk ke Sistem</a>
                <a href="{{ route('register') }}" class="btn-outline">Daftar Akun Baru</a>
            </div>
        @else
            <div class="landing-cta">
                <a href="{{ route('dashboard') }}" class="btn-filled">Buka Dashboard</a>
            </div>
        @endguest
    </div>
</div>

</body>
</html>