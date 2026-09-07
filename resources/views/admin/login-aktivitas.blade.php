@extends('layouts.admin')

@section('title', 'Login Aktivitas')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-sky-600 uppercase tracking-wider">Audit & Security Log</span>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">Catatan Aktivitas Pengguna</h1>
            <p class="text-xs text-slate-500 mt-0.5">Rekam jejak setiap login, logout, dan tindakan administratif di sistem SIPASUT.</p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.aktivitas') }}" class="flex flex-wrap items-center gap-3 text-xs">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block font-bold text-slate-600 uppercase text-[10px] mb-1">Cari Pengguna / IP / Deskripsi</label>
                <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Ketik kata kunci..."
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div class="min-w-[160px]">
                <label for="action" class="block font-bold text-slate-600 uppercase text-[10px] mb-1">Filter Jenis Aksi</label>
                <select name="action" id="action" onchange="this.form.submit()"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 font-semibold bg-slate-50 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ $actionFilter == $act ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $act)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="self-end flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold transition">
                    Cari
                </button>
                @if($search || $actionFilter)
                    <a href="{{ route('admin.aktivitas') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Audit Log -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3">Waktu Kejadian</th>
                        <th class="py-3 px-3">Pengguna</th>
                        <th class="py-3 px-3">Jenis Aktivitas</th>
                        <th class="py-3 px-3">IP Address</th>
                        <th class="py-3 px-3">Keterangan / Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 whitespace-nowrap text-slate-600 font-mono text-[11px]">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3 px-3 font-semibold text-slate-900">
                                <div>{{ $log->user_name ?? 'Guest / Tamu' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $log->user_email ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-3 whitespace-nowrap">
                                @php
                                    $act = $log->action;
                                    $badge = match($act) {
                                        'login' => 'bg-emerald-100 text-emerald-800',
                                        'logout' => 'bg-slate-100 text-slate-700',
                                        'failed_login' => 'bg-rose-100 text-rose-800',
                                        'upload_data' => 'bg-sky-100 text-sky-800',
                                        default => 'bg-indigo-100 text-indigo-800',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $badge }}">
                                    {{ strtoupper(str_replace('_', ' ', $act)) }}
                                </span>
                            </td>
                            <td class="py-3 px-3 font-mono text-slate-600 text-[11px]">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                            <td class="py-3 px-3 text-slate-600 max-w-sm">
                                {{ $log->description ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Belum ada riwayat aktivitas yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
