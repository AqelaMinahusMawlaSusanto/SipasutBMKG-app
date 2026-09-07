<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPASUT') - Sistem Informasi Pasang Surut Air Laut Jawa Timur</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Leaflet Map CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Leaflet JS & ApexCharts JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .ocean-gradient {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 50%, #075985 100%);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col antialiased">
    
    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Logo & Brand -->
                <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl ocean-gradient flex items-center justify-center text-white shadow-md shadow-sky-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-sky-600 to-blue-800 bg-clip-text text-transparent">SIPASUT</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-sky-100 text-sky-700">Jatim</span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium leading-none mt-0.5">Sistem Pasang Surut BMKG Maritim Perak</p>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-1 text-sm font-semibold">
                    <a href="{{ route('user.dashboard') }}" 
                       class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('user.dashboard') ? 'bg-sky-50 text-sky-700 font-bold' : 'text-slate-600 hover:text-sky-600 hover:bg-slate-50' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('user.kondisi') }}" 
                       class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('user.kondisi') ? 'bg-sky-50 text-sky-700 font-bold' : 'text-slate-600 hover:text-sky-600 hover:bg-slate-50' }}">
                        Kondisi Pasang Surut
                    </a>
                    <a href="{{ route('user.monitoring') }}" 
                       class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('user.monitoring') ? 'bg-sky-50 text-sky-700 font-bold' : 'text-slate-600 hover:text-sky-600 hover:bg-slate-50' }}">
                        Lokasi Monitoring
                    </a>
                    <a href="{{ route('user.calendar') }}" 
                       class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('user.calendar') ? 'bg-sky-50 text-sky-700 font-bold' : 'text-slate-600 hover:text-sky-600 hover:bg-slate-50' }}">
                        Kalender
                    </a>
                </nav>

                <!-- Right Action / Auth Button -->
                <div class="flex items-center gap-3">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.data') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-sky-600 text-white hover:bg-sky-700 shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Panel Admin</span>
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 border border-rose-200 transition">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 hover:text-slate-900 transition">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span>Masuk Admin</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Mobile Bottom Nav bar -->
        <div class="md:hidden border-t border-slate-200 bg-white grid grid-cols-4 text-center py-2 text-[11px] font-medium">
            <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'text-sky-600 font-bold' : 'text-slate-500' }}">Dashboard</a>
            <a href="{{ route('user.kondisi') }}" class="{{ request()->routeIs('user.kondisi') ? 'text-sky-600 font-bold' : 'text-slate-500' }}">Kondisi</a>
            <a href="{{ route('user.monitoring') }}" class="{{ request()->routeIs('user.monitoring') ? 'text-sky-600 font-bold' : 'text-slate-500' }}">Lokasi</a>
            <a href="{{ route('user.calendar') }}" class="{{ request()->routeIs('user.calendar') ? 'text-sky-600 font-bold' : 'text-slate-500' }}">Kalender</a>
        </div>
    </header>

    <!-- Alert Messages (Success/Error/Warning) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
    </div>

    <!-- Main Page Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-12 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4">
            <p class="font-semibold text-slate-700">SIPASUT — Sistem Informasi Pasang Surut Air Laut Jawa Timur</p>
            <p class="mt-1">Data resmi bersumber dari Stasiun Meteorologi Maritim Perak Surabaya, Badan Meteorologi, Klimatologi, dan Geofisika (BMKG).</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
