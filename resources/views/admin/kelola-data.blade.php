@extends('layouts.admin')

@section('title', 'Kelola Data')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-sky-600 uppercase tracking-wider">Modul Input & Parser Data</span>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">Kelola Data Pasang Surut</h1>
            <p class="text-xs text-slate-500 mt-0.5">Unggah data tabel matriks BMKG (Excel/CSV/PDF) untuk diproses otomatis oleh Python engine.</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Python Engine Ready
            </span>
        </div>
    </div>

    <!-- Upload Multi-File Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
        <h2 class="text-base font-bold text-slate-900 mb-1">Unggah Berkas Baru (Multi-File)</h2>
        <p class="text-xs text-slate-500 mb-5">Format yang didukung: <strong>.xlsx, .xls, .csv, .pdf</strong> (Format tabel matriks 31 hari x 24 jam BMKG Maritim).</p>

        <form action="{{ route('admin.data.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Lokasi Stasiun -->
                <div>
                    <label for="location_id" class="block text-xs font-bold text-slate-700 mb-1.5">Titik Lokasi Pantai</label>
                    <select name="location_id" id="location_id" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold bg-slate-50 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Bulan Periode -->
                <div>
                    <label for="period_month" class="block text-xs font-bold text-slate-700 mb-1.5">Bulan Data</label>
                    <select name="period_month" id="period_month" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold bg-slate-50 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ old('period_month', 7) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Tahun Periode -->
                <div>
                    <label for="period_year" class="block text-xs font-bold text-slate-700 mb-1.5">Tahun Data</label>
                    <input type="number" name="period_year" id="period_year" value="{{ old('period_year', 2026) }}" required min="2020" max="2035"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold bg-slate-50 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <!-- File Upload Dropzone -->
            <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-sky-400 hover:bg-sky-50/20 transition cursor-pointer">
                <input type="file" name="files[]" id="files" multiple required accept=".xlsx,.xls,.csv,.pdf"
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-sky-600 file:text-white hover:file:bg-sky-700 cursor-pointer">
                <p class="text-[11px] text-slate-400 mt-2">Anda dapat memilih satu atau banyak file sekaligus (CTRL + Klik atau Drag & Drop).</p>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs shadow-md shadow-sky-600/20 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Mulai Proses & Ekstrak Data Python</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Riwayat Upload Data Table -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Riwayat Berkas yang Diunggah</h2>
                <p class="text-xs text-slate-500">Daftar file pasang surut yang telah diproses ke dalam database</p>
            </div>

            <!-- Filter Lokasi -->
            <form method="GET" action="{{ route('admin.data') }}">
                <select name="location_id" onchange="this.form.submit()"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold bg-slate-50">
                    <option value="">Semua Lokasi</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3">Nama Berkas</th>
                        <th class="py-3 px-3">Lokasi Stasiun</th>
                        <th class="py-3 px-3">Periode</th>
                        <th class="py-3 px-3">Total Data</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3">Waktu Unggah</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($uploads as $up)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 font-semibold text-slate-900">
                                <div class="flex items-center gap-2">
                                    <span class="uppercase font-bold text-[9px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">
                                        {{ $up->file_type }}
                                    </span>
                                    <span class="truncate max-w-xs">{{ $up->file_name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-slate-700 font-medium">
                                {{ $up->location->name ?? '-' }}
                            </td>
                            <td class="py-3 px-3 font-medium text-slate-600">
                                {{ \Carbon\Carbon::create(2026, $up->period_month, 1)->translatedFormat('F') }} {{ $up->period_year }}
                            </td>
                            <td class="py-3 px-3 font-mono font-bold text-slate-700">
                                {{ number_format($up->total_records) }} titik
                            </td>
                            <td class="py-3 px-3">
                                @if($up->status === 'completed')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Selesai</span>
                                @elseif($up->status === 'processing')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800">Memproses</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Gagal</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-slate-500 text-[11px]">
                                {{ $up->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-3 text-right">
                                <form action="{{ route('admin.data.delete', $up->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Belum ada riwayat berkas yang diunggah. Silakan unggah berkas data baru di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $uploads->links() }}
        </div>
    </div>

</div>
@endsection
