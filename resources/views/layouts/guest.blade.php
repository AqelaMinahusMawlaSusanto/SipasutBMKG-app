<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'BMKG Monitoring Pasang Surut') }} - Login Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}?v=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">

<div class="stage">

    <div class="brand-mark">
        <a href="{{ route('dashboard.index') }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
            <img src="{{ asset('assets/img/logo-bmkg.png') }}" alt="Logo BMKG">
            <div>
                <div class="brand-name">BMKG</div>
                <div class="brand-sub">Monitoring Pasang Surut Tanjung Perak</div>
            </div>
        </a>
    </div>

    <div class="stage-grid">

        <div class="headline">
            <h1>Pantau Pasang Surut.<br>Lebih Mudah, Lebih Akurat.</h1>
            <p>Akses informasi pasang surut secara terintegrasi untuk mendukung kebutuhan monitoring, analisis maritim, dan peringatan dini BMKG.</p>
        </div>

        <div class="card-slot">
            {{ $slot }}
        </div>

    </div>

</div>

</body>
</html>
