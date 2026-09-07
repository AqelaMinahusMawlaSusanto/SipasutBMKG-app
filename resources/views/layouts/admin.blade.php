<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - SIPASUT BMKG</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased flex min-h-screen">

    <!-- Sidebar Admin -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col shrink-0 hidden md:flex min-h-screen sticky top-0 border-r border-slate-800">
        <!-- Brand Header -->
        <div class="h-20 flex items-center gap-3 px-6 border-b border-slate-800">
            <div class="w-9 h-9 rounded-xl bg-sky-500 flex items-center justify-center text-white font-bold shadow-md shadow-sky-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <span class="text-base font-extrabold text-white tracking-tight">SIPASUT ADMIN</span>
                <p class="text-[11px] text-slate-400">BMKG Maritim Perak</p>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="flex-1 px-4 py-6 space-y-1 text-sm font-medium">
            <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Menu Utama</div>

            <!-- 1. Kelola Data -->
            <a href="{{ route('admin.data') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.data*') ? 'bg-sky-600 text-white font-semibold shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6"/></svg>
                <span>Kelola Data</span>
            </a>

            <!-- 2. Kelola Lokasi -->
            <a href="{{ route('admin.lokasi') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.lokasi*') ? 'bg-sky-600 text-white font-semibold shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Kelola Lokasi</span>
            </a>

            <!-- 3. Kelola Notif -->
            <a href="{{ route('admin.notif') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.notif*') ? 'bg-sky-600 text-white font-semibold shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span>Kelola Notif</span>
            </a>

            <!-- 4. Kelola Profil -->
            <a href="{{ route('admin.profil') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.profil*') ? 'bg-sky-600 text-white font-semibold shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Kelola Profil</span>
            </a>

            <!-- 5. Login Aktivitas -->
            <a href="{{ route('admin.aktivitas') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.aktivitas*') ? 'bg-sky-600 text-white font-semibold shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Login Aktivitas</span>
            </a>

            <div class="pt-6 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Navigasi Luar</div>
            <a href="{{ route('user.dashboard') }}" target="_blank"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Lihat Dashboard User</span>
            </a>
        </div>

        <!-- Admin Profile footer in sidebar -->
        <div class="p-4 border-t border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center text-sm font-bold text-sky-400 border border-slate-600">
                    {{ substr(Auth::user()->name ?? 'Admin', 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Mobile Header -->
        <div class="md:hidden bg-slate-900 text-white p-4 flex items-center justify-between border-b border-slate-800">
            <span class="font-bold">SIPASUT ADMIN</span>
            <div class="flex gap-2 text-xs">
                <a href="{{ route('admin.data') }}" class="p-1 text-slate-300">Data</a>
                <a href="{{ route('admin.lokasi') }}" class="p-1 text-slate-300">Lokasi</a>
                <a href="{{ route('admin.notif') }}" class="p-1 text-slate-300">Notif</a>
                <a href="{{ route('admin.profil') }}" class="p-1 text-slate-300">Profil</a>
                <a href="{{ route('admin.aktivitas') }}" class="p-1 text-slate-300">Log</a>
            </div>
        </div>

        <!-- Alert Notification Messages -->
        <div class="p-6 pb-0">
            @if(session('success'))
                <div class="p-4 mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('warning'))
                <div class="p-4 mb-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="p-4 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
