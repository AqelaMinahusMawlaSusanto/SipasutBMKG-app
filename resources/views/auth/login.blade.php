<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SIPASUT BMKG Jawa Timur</title>
    
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
    
    <div class="max-w-md w-full">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <a href="{{ route('user.dashboard') }}" class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-sky-500/20 text-sky-400 border border-sky-400/30 backdrop-blur-md shadow-xl mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </a>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">SIPASUT BMKG</h1>
            <p class="text-sm text-sky-200/80 mt-1">Sistem Informasi Pasang Surut Air Laut Jawa Timur</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl p-8 shadow-2xl border border-white/20">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-900">Masuk ke Akun Anda</h2>
                <p class="text-xs text-slate-500 mt-1">Gunakan akun Anda untuk mengakses panel sistem</p>
            </div>

            @if($errors->any())
                <div class="p-3.5 mb-5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           placeholder="nama@sipasut.id"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-700">Kata Sandi</label>
                    </div>
                    <input type="password" name="password" id="password" required
                           placeholder="••••••••"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500">
                        <span class="text-xs text-slate-600">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-3 px-4 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm shadow-lg shadow-sky-600/30 transition duration-150">
                    Masuk Sekarang
                </button>
            </form>

            <!-- Quick Demo Credentials -->
            <div class="mt-6 pt-5 border-t border-slate-100">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center mb-3">Akun Demo Cepat</p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="fillCreds('admin@sipasut.id', 'admin123')"
                            class="p-2.5 rounded-xl bg-slate-50 hover:bg-sky-50 border border-slate-200 hover:border-sky-300 text-left transition">
                        <p class="text-[11px] font-bold text-slate-800">Admin BMKG</p>
                        <p class="text-[10px] text-slate-500">admin@sipasut.id</p>
                    </button>
                    <button type="button" onclick="fillCreds('user@sipasut.id', 'user123')"
                            class="p-2.5 rounded-xl bg-slate-50 hover:bg-sky-50 border border-slate-200 hover:border-sky-300 text-left transition">
                        <p class="text-[11px] font-bold text-slate-800">Pengguna/User</p>
                        <p class="text-[10px] text-slate-500">user@sipasut.id</p>
                    </button>
                </div>
            </div>

            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">
                    Belum memiliki akun? 
                    <a href="{{ route('register') }}" class="font-bold text-sky-600 hover:underline">Daftar sekarang</a>
                </p>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('user.dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                    &larr; Kembali ke Dashboard Publik
                </a>
            </div>
        </div>
    </div>

    <script>
        function fillCreds(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
