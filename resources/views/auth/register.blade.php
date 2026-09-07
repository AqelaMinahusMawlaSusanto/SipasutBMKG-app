<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - SIPASUT BMKG Jawa Timur</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .ocean-bg {
            background: radial-gradient(circle at 20% 20%, #0369a1 0%, #0f172a 100%);
        }
    </style>
</head>
<body class="ocean-bg min-h-screen flex items-center justify-center p-4 antialiased">
    
    <div class="max-w-md w-full my-8">
        <!-- Logo & Header -->
        <div class="text-center mb-6">
            <a href="{{ route('user.dashboard') }}" class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-sky-500/20 text-sky-400 border border-sky-400/30 backdrop-blur-md shadow-xl mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </a>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">SIPASUT BMKG</h1>
            <p class="text-sm text-sky-200/80 mt-1">Sistem Informasi Pasang Surut Air Laut Jawa Timur</p>
        </div>

        <!-- Register Card -->
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl p-8 shadow-2xl border border-white/20">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-900">Buat Akun Baru</h2>
                <p class="text-xs text-slate-500 mt-1">Daftar untuk mengakses data dan notifikasi pasang surut</p>
            </div>

            @if($errors->any())
                <div class="p-3.5 mb-5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                <!-- Name Input -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                           placeholder="Nama Anda"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                </div>

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           placeholder="nama@email.com"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi</label>
                    <input type="password" name="password" id="password" required
                           placeholder="Minimal 6 karakter"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           placeholder="Ulangi kata sandi"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-3 px-4 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm shadow-lg shadow-sky-600/30 transition duration-150 mt-2">
                    Daftar Akun
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">
                    Sudah memiliki akun? 
                    <a href="{{ route('login') }}" class="font-bold text-sky-600 hover:underline">Masuk di sini</a>
                </p>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('user.dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                    &larr; Kembali ke Dashboard Publik
                </a>
            </div>
        </div>
    </div>
</body>
</html>
