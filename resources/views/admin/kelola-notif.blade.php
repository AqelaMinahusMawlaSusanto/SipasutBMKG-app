@extends('layouts.admin')

@section('title', 'Kelola Notifikasi')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-sky-600 uppercase tracking-wider">Pusat Peringatan Dini</span>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">Kelola Notifikasi & Banner Peringatan</h1>
            <p class="text-xs text-slate-500 mt-0.5">Terbitkan peringatan banjir rob atau informasi pasang maksimum yang tampil di halaman dashboard publik.</p>
        </div>
    </div>

    <!-- Form Tambah Notifikasi Baru -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
        <h2 class="text-base font-bold text-slate-900 mb-1">Terbitkan Notifikasi Baru</h2>
        <p class="text-xs text-slate-500 mb-5">Banner akan otomatis tampil di atas dashboard pengguna jika status diset aktif.</p>

        <form action="{{ route('admin.notif.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label for="title" class="block font-bold text-slate-700 mb-1.5">Judul Peringatan / Notifikasi</label>
                    <input type="text" name="title" id="title" required placeholder="Contoh: Peringatan Dini Banjir Rob Selat Madura"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>

                <div>
                    <label for="type" class="block font-bold text-slate-700 mb-1.5">Kategori / Level Bahaya</label>
                    <select name="type" id="type" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-semibold bg-slate-50 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="warning">Peringatan / Warning (Kuning)</option>
                        <option value="danger">Bahaya / Kritis (Merah)</option>
                        <option value="info">Informasi Umum (Biru)</option>
                        <option value="success">Kondisi Normal / Aman (Hijau)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="location_id" class="block font-bold text-slate-700 mb-1.5">Khusus Lokasi Tertentu (Opsional)</label>
                    <select name="location_id" id="location_id"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-semibold bg-slate-50 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="">Semua Lokasi Pantai Jawa Timur</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="threshold_value" class="block font-bold text-slate-700 mb-1.5">Batas Ambang Ketinggian Air (cm)</label>
                    <input type="number" name="threshold_value" id="threshold_value" placeholder="Contoh: 120 (Level Rob)"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label for="message" class="block font-bold text-slate-700 mb-1.5">Pesan Lengkap Notifikasi</label>
                <textarea name="message" id="message" rows="3" required placeholder="Tuliskan imbauan untuk masyarakat pesisir, jadwal puncak pasang, atau wilayah terdampak..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-between pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-sky-600 rounded">
                    <span class="font-semibold text-slate-700">Langsung Aktifkan Banner di Dashboard</span>
                </label>

                <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold shadow-md shadow-sky-600/20 transition">
                    Terbitkan Notifikasi
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Notifikasi -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
        <h2 class="text-base font-bold text-slate-900 mb-4">Daftar Notifikasi yang Telah Dibuat</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3">Judul & Pesan</th>
                        <th class="py-3 px-3">Kategori</th>
                        <th class="py-3 px-3">Lokasi</th>
                        <th class="py-3 px-3">Waktu Terbit</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($notifications as $notif)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3">
                                <form action="{{ route('admin.notif.toggle', $notif->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold cursor-pointer transition
                                        {{ $notif->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                                        title="Klik untuk ubah status">
                                        {{ $notif->is_active ? 'Aktif' : 'Non-Aktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3 px-3 max-w-md">
                                <p class="font-bold text-slate-900">{{ $notif->title }}</p>
                                <p class="text-slate-500 text-[11px] mt-0.5 line-clamp-2">{{ $notif->message }}</p>
                            </td>
                            <td class="py-3 px-3 uppercase font-bold text-[10px]">
                                <span class="px-2 py-0.5 rounded
                                    {{ $notif->type === 'warning' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $notif->type === 'danger' ? 'bg-rose-100 text-rose-800' : '' }}
                                    {{ $notif->type === 'info' ? 'bg-sky-100 text-sky-800' : '' }}
                                    {{ $notif->type === 'success' ? 'bg-emerald-100 text-emerald-800' : '' }}">
                                    {{ $notif->type }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-slate-600 font-medium">
                                {{ $notif->location->name ?? 'Semua Wilayah' }}
                            </td>
                            <td class="py-3 px-3 text-slate-500 text-[11px]">
                                {{ $notif->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-3 text-right">
                                <form action="{{ route('admin.notif.delete', $notif->id) }}" method="POST" onsubmit="return confirm('Hapus notifikasi ini?')">
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
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada notifikasi yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>

</div>
@endsection
