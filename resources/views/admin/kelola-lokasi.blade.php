<x-app-layout>
    <div class="bg-[#F8FAFC] min-h-screen font-sans" style="background-color: #F8FAFC;">
        <div class="max-w-7xl mx-auto px-6 py-8 space-y-6" style="padding-top: 32px;">
            
            <!-- Header Section & Tombol Tambah Lokasi -->
            <div class="flex items-center justify-between" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                <div>
                    <h1 class="text-3xl font-bold text-[#0F172A] tracking-tight" style="color: #0F172A; font-weight: 700; font-size: 26px; margin: 0 0 4px;">Kelola Lokasi Monitoring</h1>
                    <p class="text-sm font-medium text-slate-500 mt-1" style="color: #64748B; font-size: 13px; margin: 0;">Kelola data lokasi stasiun monitoring pasang surut.</p>
                </div>
                
                <!-- Tombol Tambah Lokasi Biru Solid Figma -->
                <button onclick="openModal('modalTambahLokasi')"
                    style="background-color: #1D61E7; color: #FFFFFF; font-weight: 700; padding: 12px 24px; border-radius: 12px; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; box-shadow: 0 2px 8px rgba(29, 97, 231, 0.25);">
                    <span style="font-size: 18px; font-weight: 800; line-height: 1;">+</span>
                    <span style="font-size: 14px; font-weight: 700;">Tambah Lokasi</span>
                </button>
            </div>

            <!-- Flash Alert Notification -->
            @if(session('success'))
                <div style="background-color: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 12px 18px; border-radius: 12px; font-size: 13px; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <span>✔ {{ session('success') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div style="background-color: #FFFBEB; border: 1px solid #FDE68A; color: #92400E; padding: 12px 18px; border-radius: 12px; font-size: 13px; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <span>⚠️ {{ session('warning') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div style="background-color: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 12px 18px; border-radius: 12px; font-size: 13px; margin-bottom: 16px;">
                    @foreach($errors->all() as $error)
                        <div style="margin-bottom: 2px;"> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Container Utama Card Putih -->
            <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);" class="space-y-6">

                <!-- Input Cari Lokasi Stasiun (Sejajar dengan Figma) -->
                <form action="{{ route('admin.lokasi') }}" method="GET" style="margin-bottom: 20px;">
                    <div style="position: relative; width: 100%;">
                        <span style="position: absolute; top: 50%; transform: translateY(-50%); left: 16px; color: #94A3B8; font-size: 15px;">
                            🔍
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Lokasi Stasiun" 
                            style="width: 100%; border: 1px solid #CBD5E1; border-radius: 12px; padding: 13px 16px 13px 44px; font-size: 14px; font-weight: 500; color: #334155; background-color: #FFFFFF; outline: none; box-sizing: border-box;">
                    </div>
                </form>

                <!-- Tabel Lokasi Monitoring -->
                <div style="border: 1px solid #E2E8F0; border-radius: 12px; overflow: hidden;">
                    <table style="width: 100%; text-align: left; border-collapse: collapse; font-size: 14px;">
                        <!-- Header Tabel Biru Muda Figma BMKG -->
                        <thead style="background-color: #D7E5F9; color: #0F172A; font-weight: 700; font-size: 13px;">
                            <tr>
                                <th style="padding: 16px 20px; width: 80px;">No</th>
                                <th style="padding: 16px 20px;">Lokasi</th>
                                <th style="padding: 16px 20px;">Wilayah</th>
                                <th style="padding: 16px 20px;">Koordinat</th>
                                <th style="padding: 16px 20px;">Status</th>
                                <th style="padding: 16px 20px; text-align: center; width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody style="font-weight: 500; color: #334155;">
                            @forelse($locations as $index => $station)
                                @php
                                    $rowNum = (method_exists($locations, 'currentPage') ? ($locations->currentPage() - 1) * $locations->perPage() : 0) + $index + 1;
                                @endphp
                                <tr style="border-top: 1px solid #F1F5F9;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 16px 20px; color: #64748B;">{{ $rowNum }}.</td>
                                    <td style="padding: 16px 20px; font-weight: 700; color: #0F172A;">
                                        {{ $station->name }}
                                        <span style="font-size: 11px; font-weight: 600; color: #1D61E7; background-color: #E0ECFF; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">
                                            {{ $station->code }}
                                        </span>
                                    </td>
                                    <td style="padding: 16px 20px; color: #475569;">
                                        {{ $station->institution ? (str_contains($station->institution, 'Perak') ? 'Surabaya' : (str_contains($station->name, 'Banyuwangi') ? 'Banyuwangi' : 'Jawa Timur')) : 'Jawa Timur' }}
                                    </td>
                                    <td style="padding: 16px 20px; font-family: monospace; font-size: 13px; color: #475569;">
                                        {{ number_format($station->latitude, 4) }}, {{ number_format($station->longitude, 4) }}
                                    </td>
                                    <td style="padding: 16px 20px;">
                                        @if($station->is_active)
                                            <span style="background-color: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; padding: 4px 14px; border-radius: 9999px; font-size: 12px; font-weight: 700;">
                                                Aktif
                                            </span>
                                        @else
                                            <span style="background-color: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; padding: 4px 14px; border-radius: 9999px; font-size: 12px; font-weight: 700;">
                                                Non-Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 16px 20px; text-align: center;">
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                            {{-- Detail Button --}}
                                            <button type="button" class="btn-detail-loc"
                                                style="border: 1px solid #93C5FD; background-color: #EFF6FF; color: #1D4ED8; padding: 6px 10px; border-radius: 8px; cursor: pointer;"
                                                title="Detail"
                                                data-name="{{ $station->name }}"
                                                data-code="{{ $station->code }}"
                                                data-lat="{{ $station->latitude }}"
                                                data-long="{{ $station->longitude }}"
                                                data-inst="{{ $station->institution ?? 'BMKG Stasiun Meteorologi Maritim Perak Surabaya' }}"
                                                data-desc="{{ $station->description ?? 'Tidak ada keterangan tambahan.' }}"
                                                data-status="{{ $station->is_active ? 'Aktif' : 'Non-Aktif' }}">
                                                
                                            </button>

                                            {{-- Edit Button --}}
                                            <button type="button" class="btn-edit-loc"
                                                style="border: 1px solid #FDE68A; background-color: #FEF3C7; color: #D97706; padding: 6px 10px; border-radius: 8px; cursor: pointer;"
                                                title="Edit"
                                                data-id="{{ $station->id }}"
                                                data-name="{{ $station->name }}"
                                                data-code="{{ $station->code }}"
                                                data-lat="{{ $station->latitude }}"
                                                data-long="{{ $station->longitude }}"
                                                data-inst="{{ $station->institution }}"
                                                data-desc="{{ $station->description }}"
                                                data-active="{{ $station->is_active ? '1' : '0' }}">
                                                
                                            </button>

                                            {{-- Delete Form --}}
                                            <form action="{{ route('admin.lokasi.delete', $station->id) }}" method="POST"
                                                  class="form-delete-loc"
                                                  data-name="{{ $station->name }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="border: 1px solid #FECACA; background-color: #FEE2E2; color: #DC2626; padding: 6px 10px; border-radius: 8px; cursor: pointer;" title="Hapus">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 32px 20px; text-align: center; color: #94A3B8;">
                                        Tidak ada data stasiun monitoring yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($locations) && method_exists($locations, 'hasPages') && $locations->hasPages())
                    <div style="padding-top: 8px;">
                        {{ $locations->links() }}
                    </div>
                @endif

            </div>

        </div>

        {{-- ================================================================
             MODAL: TAMBAH LOKASI
             ================================================================ --}}
        <div id="modalTambahLokasi"
            style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.55);z-index:9000;align-items:center;justify-content:center;padding:20px;">
            <div style="background:#fff;border-radius:20px;width:100%;max-width:480px;box-shadow:0 25px 60px rgba(0,0,0,0.2);overflow:hidden;">
                <div style="padding:20px 24px;border-bottom:1px solid #F1F5F9;display:flex;align-items:center;justify-content:space-between;background:#F8FAFC;">
                    <h3 style="margin:0;font-size:16px;font-weight:700;color:#0F172A;">Tambah Lokasi Stasiun</h3>
                    <button onclick="closeModal('modalTambahLokasi')" style="background:#F1F5F9;border:none;border-radius:8px;width:32px;height:32px;font-size:16px;cursor:pointer;color:#64748B;display:flex;align-items:center;justify-content:center;">✕</button>
                </div>
                <form action="{{ route('admin.lokasi.store') }}" method="POST" style="padding:24px;">
                    @csrf
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Nama Stasiun / Lokasi <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Surabaya Barat"
                            style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                    </div>
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Kode Stasiun <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="code" required placeholder="Contoh: SBYBRT"
                            style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;text-transform:uppercase;box-sizing:border-box;">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Latitude <span style="color:#ef4444;">*</span></label>
                            <input type="number" step="0.0000001" name="latitude" required placeholder="-7.1950"
                                style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Longitude <span style="color:#ef4444;">*</span></label>
                            <input type="number" step="0.0000001" name="longitude" required placeholder="112.7390"
                                style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                        </div>
                    </div>
                    <div style="margin-bottom:20px;">
                        <label style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#374151;cursor:pointer;">
                            <input type="checkbox" name="is_active" value="1" checked style="width:16px;height:16px;">
                            <span>Status Aktif</span>
                        </label>
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:16px;border-top:1px solid #F1F5F9;">
                        <button type="button" onclick="closeModal('modalTambahLokasi')"
                            style="padding:10px 20px;font-size:13px;font-weight:600;color:#64748B;background:#F1F5F9;border:none;border-radius:10px;cursor:pointer;">Batal</button>
                        <button type="submit"
                            style="padding:10px 22px;font-size:13px;font-weight:700;color:#fff;background:#1D61E7;border:none;border-radius:10px;cursor:pointer;box-shadow:0 2px 8px rgba(29,97,231,0.25);">Simpan Lokasi</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================================================================
             MODAL: EDIT LOKASI
             ================================================================ --}}
        <div id="modalEditLokasi"
            style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.55);z-index:9000;align-items:center;justify-content:center;padding:20px;">
            <div style="background:#fff;border-radius:20px;width:100%;max-width:480px;box-shadow:0 25px 60px rgba(0,0,0,0.2);overflow:hidden;">
                <div style="padding:20px 24px;border-bottom:1px solid #F1F5F9;display:flex;align-items:center;justify-content:space-between;background:#FFFBEB;">
                    <h3 style="margin:0;font-size:16px;font-weight:700;color:#0F172A;">Edit Lokasi Stasiun</h3>
                    <button onclick="closeModal('modalEditLokasi')" style="background:#F1F5F9;border:none;border-radius:8px;width:32px;height:32px;font-size:16px;cursor:pointer;color:#64748B;display:flex;align-items:center;justify-content:center;">✕</button>
                </div>
                <form id="formEditLokasi" method="POST" style="padding:24px;">
                    @csrf
                    @method('PUT')
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Nama Stasiun / Lokasi <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" id="editName" required
                            style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                    </div>
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Kode Stasiun <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="code" id="editCode" required
                            style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;text-transform:uppercase;box-sizing:border-box;">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Latitude <span style="color:#ef4444;">*</span></label>
                            <input type="number" step="0.0000001" name="latitude" id="editLat" required
                                style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Longitude <span style="color:#ef4444;">*</span></label>
                            <input type="number" step="0.0000001" name="longitude" id="editLong" required
                                style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                        </div>
                    </div>
                    <div style="margin-bottom:20px;">
                        <label style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#374151;cursor:pointer;">
                            <input type="checkbox" name="is_active" id="editActive" value="1" style="width:16px;height:16px;">
                            <span>Status Aktif</span>
                        </label>
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:16px;border-top:1px solid #F1F5F9;">
                        <button type="button" onclick="closeModal('modalEditLokasi')"
                            style="padding:10px 20px;font-size:13px;font-weight:600;color:#64748B;background:#F1F5F9;border:none;border-radius:10px;cursor:pointer;">Batal</button>
                        <button type="submit"
                            style="padding:10px 22px;font-size:13px;font-weight:700;color:#fff;background:#D97706;border:none;border-radius:10px;cursor:pointer;box-shadow:0 2px 8px rgba(217,119,6,0.25);">Update Lokasi</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================================================================
             MODAL: DETAIL LOKASI
             ================================================================ --}}
        <div id="modalDetailLokasi"
            style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.55);z-index:9000;align-items:center;justify-content:center;padding:20px;">
            <div style="background:#fff;border-radius:20px;width:100%;max-width:480px;box-shadow:0 25px 60px rgba(0,0,0,0.2);overflow:hidden;">
                <div style="padding:20px 24px;border-bottom:1px solid #F1F5F9;display:flex;align-items:center;justify-content:space-between;background:#EFF6FF;">
                    <h3 style="margin:0;font-size:16px;font-weight:700;color:#0F172A;">Detail Lokasi Stasiun</h3>
                    <button onclick="closeModal('modalDetailLokasi')" style="background:#F1F5F9;border:none;border-radius:8px;width:32px;height:32px;font-size:16px;cursor:pointer;color:#64748B;display:flex;align-items:center;justify-content:center;">✕</button>
                </div>
                <div id="detailLocContent" style="padding:24px;">
                    {{-- Diisi via JavaScript --}}
                </div>
                <div style="padding:16px 24px;border-top:1px solid #F1F5F9;display:flex;justify-content:space-between;align-items:center;">
                    <a id="detailMapBtn" href="#" target="_blank"
                       style="font-size:13px;font-weight:700;color:#0284C7;text-decoration:none;display:flex;align-items:center;gap:4px;">
                        🗺️ Google Maps ↗
                    </a>
                    <button onclick="closeModal('modalDetailLokasi')"
                        style="padding:10px 22px;font-size:13px;font-weight:600;color:#64748B;background:#F1F5F9;border:none;border-radius:10px;cursor:pointer;">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- JavaScript Modal & Event Handler --}}
    <script>
        function openModal(id) {
            var el = document.getElementById(id);
            if (el) el.style.display = 'flex';
        }
        function closeModal(id) {
            var el = document.getElementById(id);
            if (el) el.style.display = 'none';
        }

        ['modalTambahLokasi','modalEditLokasi','modalDetailLokasi'].forEach(function(id) {
            var modal = document.getElementById(id);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) closeModal(id);
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Edit
            document.querySelectorAll('.btn-edit-loc').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var baseUrl = "{{ url('admin/lokasi') }}/" + this.dataset.id;
                    document.getElementById('formEditLokasi').action = baseUrl;
                    document.getElementById('editName').value = this.dataset.name;
                    document.getElementById('editCode').value = this.dataset.code;
                    document.getElementById('editLat').value = this.dataset.lat;
                    document.getElementById('editLong').value = this.dataset.long;
                    document.getElementById('editActive').checked = (this.dataset.active === '1');
                    openModal('modalEditLokasi');
                });
            });

            // Detail
            document.querySelectorAll('.btn-detail-loc').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var statusBadge = this.dataset.status === 'Aktif'
                        ? '<span style="background-color:#ECFDF5;color:#059669;border:1px solid #A7F3D0;padding:4px 12px;border-radius:9999px;font-size:12px;font-weight:700;">Aktif</span>'
                        : '<span style="background-color:#FEF2F2;color:#DC2626;border:1px solid #FECACA;padding:4px 12px;border-radius:9999px;font-size:12px;font-weight:700;">Non-Aktif</span>';

                    var html = `
                        <table style="width:100%;font-size:13.5px;border-collapse:collapse;">
                            <tr style="border-bottom:1px solid #F1F5F9;">
                                <td style="padding:10px 8px;color:#64748B;font-weight:700;width:130px;">Nama Lokasi</td>
                                <td style="padding:10px 8px;font-weight:700;color:#0F172A;">${this.dataset.name}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #F1F5F9;">
                                <td style="padding:10px 8px;color:#64748B;font-weight:700;">Kode Stasiun</td>
                                <td style="padding:10px 8px;"><span style="background:#E0ECFF;color:#1D61E7;padding:2px 8px;border-radius:6px;font-weight:700;">${this.dataset.code}</span></td>
                            </tr>
                            <tr style="border-bottom:1px solid #F1F5F9;">
                                <td style="padding:10px 8px;color:#64748B;font-weight:700;">Koordinat</td>
                                <td style="padding:10px 8px;font-family:monospace;">${this.dataset.lat}, ${this.dataset.long}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #F1F5F9;">
                                <td style="padding:10px 8px;color:#64748B;font-weight:700;">Institusi</td>
                                <td style="padding:10px 8px;">${this.dataset.inst}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 8px;color:#64748B;font-weight:700;">Status</td>
                                <td style="padding:10px 8px;">${statusBadge}</td>
                            </tr>
                        </table>
                    `;
                    document.getElementById('detailLocContent').innerHTML = html;
                    document.getElementById('detailMapBtn').href = `https://www.google.com/maps?q=${this.dataset.lat},${this.dataset.long}`;
                    openModal('modalDetailLokasi');
                });
            });

            // Delete
            document.querySelectorAll('.form-delete-loc').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    var name = this.dataset.name || 'lokasi ini';
                    if (!confirm('Apakah Anda yakin ingin menghapus lokasi ' + name + '?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</x-app-layout>