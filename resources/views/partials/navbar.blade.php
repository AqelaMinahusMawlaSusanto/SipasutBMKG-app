<header class="bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-b from-sky-500 to-emerald-500 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2 12c2 2 4 2 6 0s4-2 6 0 4 2 6 0M2 17c2 2 4 2 6 0s4-2 6 0 4 2 6 0M2 7c2 2 4 2 6 0s4-2 6 0 4 2 6 0" />
                </svg>
            </span>
            <span class="leading-tight">
                <span class="block font-bold text-slate-900 text-lg">BMKG</span>
                <span class="block text-sm text-slate-500 -mt-0.5">Monitoring Pasang Surut</span>
            </span>
        </a>

        <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
            <a href="{{ route('dashboard') }}"
               class="pb-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-sky-600 text-sky-700' : 'border-transparent hover:text-sky-700' }}">
                Dashboard
            </a>
            <a href="{{ route('download') }}"
               class="pb-1 border-b-2 {{ request()->routeIs('download') ? 'border-sky-600 text-sky-700' : 'border-transparent hover:text-sky-700' }}">
                Download Pasang Surut
            </a>
            <a href="{{ route('lokasi') }}"
               class="pb-1 border-b-2 {{ request()->routeIs('lokasi') ? 'border-sky-600 text-sky-700' : 'border-transparent hover:text-sky-700' }}">
                Lokasi
            </a>
            <a href="{{ route('kalender') }}"