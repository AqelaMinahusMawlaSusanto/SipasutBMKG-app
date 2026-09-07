@extends('layouts.admin')

@section('title', 'Kelola Lokasi')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-sky-600 uppercase tracking-wider">Manajemen Stasiun Maritim</span>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">Kelola Titik Lokasi Pantai</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar stasiun pengamatan pasang surut air laut di wilayah pesisir Jawa Timur.</p>
        </div>

        <button type="button" onclick="openAddModal()" 
                class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs shadow-md shadow-sky-600/20 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Lokasi Baru</span>
        </button>
    </div>

    <!-- Tabel Daftar Lokasi -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3">Kode</th>
                        <th class="py-3 px-3">Nama Titik Stasiun</th>
                        <th class="py-3 px-3">Koordinat (Lat, Lng)</th>
                        <th class="py-3 px-3">Total Data</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($locations as $loc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 font-mono font-bold text-sky-700">
                                {{ $loc->code }}
                            </td>
                            <td class="py-3 px-3 font-bold text-slate-900">
                                {{ $loc->name }}
                                <p class="text-[11px] font-normal text-slate-500 mt-0.5">{{ $loc->institution }}</p>
                            </td>
                            <td class="py-3 px-3 font-mono text-slate-600">
                                {{ $loc->latitude }}, {{ $loc->longitude }}
                            </td>
                            <td class="py-3 px-3 font-mono font-bold text-slate-700">
                                {{ number_format($loc->tidal_data_count ?? 0) }} data
                            </td>
                            <td class="py-3 px-3">
                                @if($loc->is_active)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="openEditModal({{ json_encode($loc) }})"
                                            class="p-1.5 rounded-lg text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    
                                    <form action="{{ route('admin.lokasi.delete', $loc->id) }}" method="POST" onsubmit="return confirm('Hapus titik stasiun ini beserta seluruh data pasang surutnya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada lokasi titik pengamatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Form Tambah/Edit Lokasi -->
<div id="locationModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
        <button type="button" onclick="closeLocationModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <h3 class="text-lg font-extrabold text-slate-900" id="modalTitle">Tambah Titik Lokasi</h3>
        <p class="text-xs text-slate-500 mt-0.5">Informasi stasiun pengamatan pasang surut BMKG</p>

        <form id="locationForm" method="POST" action="{{ route('admin.lokasi.store') }}" class="mt-5 space-y-4 text-xs">
            @csrf
            <div id="methodContainer"></div>

            <div>
                <label for="name" class="block font-bold text-slate-700 mb-1">Nama Titik Lokasi / Pantai</label>
                <input type="text" name="name" id="modalName" required placeholder="Contoh: Surabaya Timur"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="code" class="block font-bold text-slate-700 mb-1">Kode Stasiun</label>
                    <input type="text" name="code" id="modalCode" required placeholder="SBYTIM"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none uppercase">
                </div>
                <div>
                    <label for="institution" class="block font-bold text-slate-700 mb-1">Institusi / Instansi</label>
                    <input type="text" name="institution" id="modalInstitution" value="BMKG Maritim Perak"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="latitude" class="block font-bold text-slate-700 mb-1">Latitude</label>
                    <input type="number" step="any" name="latitude" id="modalLatitude" required placeholder="-7.2458"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label for="longitude" class="block font-bold text-slate-700 mb-1">Longitude</label>
                    <input type="number" step="any" name="longitude" id="modalLongitude" required placeholder="112.7988"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label for="description" class="block font-bold text-slate-700 mb-1">Deskripsi / Keterangan</label>
                <textarea name="description" id="modalDescription" rows="2" placeholder="Catatan mengenai stasiun pantai ini..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_active" id="modalActive" value="1" checked class="w-4 h-4 text-sky-600 rounded">
                <label for="modalActive" class="font-semibold text-slate-700 cursor-pointer">Stasiun Aktif di Sistem</label>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeLocationModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold shadow-md shadow-sky-600/20">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openAddModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Titik Lokasi';
        document.getElementById('locationForm').action = "{{ route('admin.lokasi.store') }}";
        document.getElementById('methodContainer').innerHTML = '';
        document.getElementById('modalName').value = '';
        document.getElementById('modalCode').value = '';
        document.getElementById('modalInstitution').value = 'BMKG Stasiun Meteorologi Maritim Perak Surabaya';
        document.getElementById('modalLatitude').value = '';
        document.getElementById('modalLongitude').value = '';
        document.getElementById('modalDescription').value = '';
        document.getElementById('modalActive').checked = true;
        document.getElementById('locationModal').classList.remove('hidden');
    }

    function openEditModal(loc) {
        document.getElementById('modalTitle').innerText = 'Edit Titik Lokasi: ' + loc.name;
        document.getElementById('locationForm').action = "/admin/lokasi/" + loc.id;
        document.getElementById('methodContainer').innerHTML = '@method("PUT")';
        document.getElementById('modalName').value = loc.name;
        document.getElementById('modalCode').value = loc.code;
        document.getElementById('modalInstitution').value = loc.institution || '';
        document.getElementById('modalLatitude').value = loc.latitude;
        document.getElementById('modalLongitude').value = loc.longitude;
        document.getElementById('modalDescription').value = loc.description || '';
        document.getElementById('modalActive').checked = loc.is_active;
        document.getElementById('locationModal').classList.remove('hidden');
    }

    function closeLocationModal() {
        document.getElementById('locationModal').classList.add('hidden');
    }
</script>
@endpush
