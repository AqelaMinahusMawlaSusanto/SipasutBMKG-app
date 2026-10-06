<x-app-layout>
<div style="background-color:#F8FAFC;min-height:100vh;padding:32px 40px;font-family:'Poppins',sans-serif;">

    {{-- ===== FLASH ALERTS ===== --}}
    @if(session('success'))
        <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:13px 18px;border-radius:12px;margin-bottom:20px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:8px;">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;padding:13px 18px;border-radius:12px;margin-bottom:20px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:8px;">
            ⚠️ {{ session('warning') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:13px 18px;border-radius:12px;margin-bottom:20px;font-size:13px;">
            @foreach($errors->all() as $err)
                <div style="margin-bottom:2px;">❌ {{ $err }}</div>
            @endforeach
        </div>
    @endif

    {{-- ===== PAGE HEADER ===== --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;">
        <div>
            <h1 style="font-size:26px;font-weight:800;color:#0F172A;margin:0 0 4px;">Kelola Prediksi Pasang Surut</h1>
            <p style="font-size:13px;color:#64748B;margin:0;font-weight:500;">Kelola data prediksi pasang surut di seluruh lokasi monitoring</p>
        </div>
        <div style="display:flex;align-items:center;gap:12px;">
            <button onclick="openModal('modalUpload')"
                style="background:#0284C7;color:#fff;font-weight:700;font-size:13.5px;padding:12px 20px;border-radius:12px;border:none;cursor:pointer;display:flex;align-items:center;gap:8px;box-shadow:0 2px 8px rgba(2,132,199,0.25);white-space:nowrap;">
                <span>📥</span> Import Berkas (Excel/CSV/PDF)
            </button>
            <button onclick="openModal('modalTambah')"
                style="background:#1D61E7;color:#fff;font-weight:700;font-size:13.5px;padding:12px 20px;border-radius:12px;border:none;cursor:pointer;display:flex;align-items:center;gap:8px;box-shadow:0 2px 8px rgba(29,97,231,0.25);white-space:nowrap;">
                <span style="font-size:16px;line-height:1;font-weight:800;">+</span> Tambah Manual
            </button>
        </div>
    </div>

    {{-- ===== 4 STAT CARDS ===== --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">

        {{-- Card 1: Total Data --}}
        <div style="background:#fff;border:1px solid #E2E8F0;border-radius:16px;padding:20px;display:flex;align-items:center;gap:16px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="width:52px;height:52px;background:#E0ECFF;color:#1D61E7;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <div style="font-size:12px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.5px;">Total Data</div>
                <div style="font-size:30px;font-weight:900;color:#0F172A;line-height:1.1;">{{ $totalData }}</div>
                <div style="font-size:12px;color:#64748B;font-weight:600;">Data Pasang Surut</div>
            </div>
        </div>

        {{-- Card 2: Lokasi Monitoring --}}
        <div style="background:#fff;border:1px solid #E2E8F0;border-radius:16px;padding:20px;display:flex;align-items:center;gap:16px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="width:52px;height:52px;background:#E0ECFF;color:#1D61E7;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div style="font-size:12px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.5px;">Lokasi Monitoring</div>
                <div style="font-size:30px;font-weight:900;color:#0F172A;line-height:1.1;">{{ $totalLokasi }}</div>
                <div style="font-size:12px;color:#64748B;font-weight:600;">Lokasi Aktif</div>
            </div>
        </div>

        {{-- Card 3: Data Hari Ini --}}
        <div style="background:#fff;border:1px solid #E2E8F0;border-radius:16px;padding:20px;display:flex;align-items:center;gap:16px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="width:52px;height:52px;background:#E0ECFF;color:#1D61E7;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <div style="font-size:12px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.5px;">Data Hari Ini</div>
                <div style="font-size:30px;font-weight:900;color:#0F172A;line-height:1.1;">{{ $dataHariIni }}</div>
                <div style="font-size:12px;color:#64748B;font-weight:600;">Data Terbaru</div>
            </div>
        </div>

        {{-- Card 4: Update Terakhir --}}
        <div style="background:#fff;border:1px solid #E2E8F0;border-radius:16px;padding:20px;display:flex;align-items:center;gap:16px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="width:52px;height:52px;background:#E0ECFF;color:#1D61E7;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div style="font-size:12px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.5px;">Update Terakhir</div>
                <div style="font-size:22px;font-weight:900;color:#0F172A;line-height:1.2;">{{ $updateTerakhir }}</div>
                <div style="font-size:12px;color:#64748B;font-weight:600;">Data Terbaru</div>
            </div>
        </div>

    </div>

    {{-- ===== FILTER BAR ===== --}}
    <div style="background:#fff;border:1px solid #E2E8F0;border-radius:16px;padding:18px 20px;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <form action="{{ route('admin.data') }}" method="GET"
              style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:12px;">

            {{-- Lokasi --}}
            <div style="flex:1;min-width:150px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Cari Lokasi</label>
                <select name="location_id"
                    style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:10px 12px;font-size:13px;color:#334155;background:#fff;outline:none;">
                    <option value="">Pilih Lokasi</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal --}}
            <div style="flex:1;min-width:150px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}"
                    style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:10px 12px;font-size:13px;color:#334155;background:#fff;outline:none;">
            </div>

            {{-- Status --}}
            <div style="flex:1;min-width:150px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Status Kondisi</label>
                <select name="status"
                    style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:10px 12px;font-size:13px;color:#334155;background:#fff;outline:none;">
                    <option value="">Semua Kondisi</option>
                    <option value="Aman" {{ request('status') === 'Aman' ? 'selected' : '' }}>Aman</option>
                    <option value="Waspada" {{ request('status') === 'Waspada' ? 'selected' : '' }}>Waspada</option>
                    <option value="Bahaya" {{ request('status') === 'Bahaya' ? 'selected' : '' }}>Bahaya</option>
                </select>
            </div>

            {{-- Urutkan --}}
            <div style="flex:1;min-width:140px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Urutkan</label>
                <select name="sort"
                    style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:10px 12px;font-size:13px;color:#334155;background:#fff;outline:none;">
                    <option value="terbaru" {{ request('sort', 'terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>Terlama</option>
                    <option value="lokasi" {{ request('sort') === 'lokasi' ? 'selected' : '' }}>Lokasi A-Z</option>
                </select>
            </div>

            {{-- Tombol Cari --}}
            <div>
                <button type="submit"
                    style="border:1.5px solid #93C5FD;background:#EFF6FF;color:#1D4ED8;font-weight:700;font-size:13px;padding:10px 20px;border-radius:10px;cursor:pointer;display:flex;align-items:center;gap:6px;white-space:nowrap;">
                    🔍 Cari
                </button>
            </div>

            {{-- Tombol Reset --}}
            <div>
                <a href="{{ route('admin.data') }}"
                    style="border:1.5px solid #CBD5E1;background:#F8FAFC;color:#64748B;font-weight:700;font-size:13px;padding:10px 20px;border-radius:10px;cursor:pointer;display:flex;align-items:center;gap:6px;text-decoration:none;white-space:nowrap;">
                    ↺ Reset
                </a>
            </div>
        </form>
    </div>

    {{-- ===== TABEL PREDIKSI ===== --}}
    <div style="background:#fff;border:1px solid #E2E8F0;border-radius:16px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:24px;">

        {{-- Table Header Info --}}
        <div style="padding:16px 20px;border-bottom:1px solid #F1F5F9;display:flex;justify-content:space-between;align-items:center;">
            <div>
                <span style="font-size:15px;font-weight:700;color:#0F172A;">Daftar Prediksi Pasang Surut</span>
                <span style="margin-left:10px;font-size:12px;color:#94A3B8;">({{ $predictions->total() }} data)</span>
            </div>
            <span style="font-size:12px;color:#94A3B8;">
                Halaman {{ $predictions->currentPage() }} dari {{ $predictions->lastPage() }}
            </span>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13.5px;font-weight:500;color:#334155;">
                <thead>
                    <tr style="background:#D7E5F9;font-size:12.5px;font-weight:700;color:#0F172A;">
                        <th style="padding:14px 16px;text-align:left;white-space:nowrap;">No</th>
                        <th style="padding:14px 16px;text-align:left;white-space:nowrap;">Lokasi</th>
                        <th style="padding:14px 16px;text-align:left;white-space:nowrap;">Tanggal</th>
                        <th style="padding:14px 16px;text-align:left;white-space:nowrap;">Jam Pasang</th>
                        <th style="padding:14px 16px;text-align:left;white-space:nowrap;">Tinggi Pasang</th>
                        <th style="padding:14px 16px;text-align:left;white-space:nowrap;">Jam Surut</th>
                        <th style="padding:14px 16px;text-align:left;white-space:nowrap;">Tinggi Surut</th>
                        <th style="padding:14px 16px;text-align:left;white-space:nowrap;">Status</th>
                        <th style="padding:14px 16px;text-align:left;white-space:nowrap;">Update</th>
                        <th style="padding:14px 16px;text-align:center;white-space:nowrap;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($predictions as $i => $p)
                        @php
                            $rowNum = ($predictions->currentPage() - 1) * $predictions->perPage() + $i + 1;
                            $statusColor = match($p->status) {
                                'Aman'    => ['bg' => '#ECFDF5', 'color' => '#059669', 'border' => '#A7F3D0'],
                                'Waspada' => ['bg' => '#FFFBEB', 'color' => '#D97706', 'border' => '#FDE68A'],
                                'Bahaya'  => ['bg' => '#FEF2F2', 'color' => '#DC2626', 'border' => '#FECACA'],
                                default   => ['bg' => '#F1F5F9', 'color' => '#64748B', 'border' => '#CBD5E1'],
                            };
                        @endphp
                        <tr style="border-top:1px solid #F1F5F9;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                            <td style="padding:14px 16px;color:#94A3B8;font-size:13px;">{{ $rowNum }}</td>
                            <td style="padding:14px 16px;font-weight:700;color:#0F172A;">{{ $p->location->name ?? '-' }}</td>
                            <td style="padding:14px 16px;">{{ $p->record_date->format('d M Y') }}</td>
                            <td style="padding:14px 16px;">{{ $p->high_tide_time }}</td>
                            <td style="padding:14px 16px;font-weight:700;color:#1D61E7;">{{ number_format($p->high_tide_level, 2) }} m</td>
                            <td style="padding:14px 16px;">{{ $p->low_tide_time }}</td>
                            <td style="padding:14px 16px;font-weight:700;color:#7C3AED;">{{ number_format($p->low_tide_level, 2) }} m</td>
                            <td style="padding:14px 16px;">
                                <span style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['color'] }};border:1px solid {{ $statusColor['border'] }};padding:4px 12px;border-radius:9999px;font-size:11.5px;font-weight:700;">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td style="padding:14px 16px;color:#94A3B8;font-size:12px;">{{ $p->updated_at->format('H.i \W\I\B') }}</td>
                            <td style="padding:14px 16px;text-align:center;">
                                <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                    {{-- Detail --}}
                                    <button type="button" title="Detail" class="btn-detail"
                                        style="border:1px solid #93C5FD;background:#EFF6FF;color:#1D4ED8;padding:6px 9px;border-radius:8px;cursor:pointer;font-size:14px;line-height:1;"
                                        data-lokasi="{{ $p->location->name ?? '-' }}"
                                        data-tanggal="{{ $p->record_date->format('d M Y') }}"
                                        data-high-time="{{ $p->high_tide_time }}"
                                        data-high-level="{{ number_format($p->high_tide_level, 2) }}"
                                        data-low-time="{{ $p->low_tide_time }}"
                                        data-low-level="{{ number_format($p->low_tide_level, 2) }}"
                                        data-status="{{ $p->status }}"
                                        data-update="{{ $p->updated_at->format('d/m/Y H.i') }} WIB">👁️</button>
                                    {{-- Edit --}}
                                    <button type="button" title="Edit" class="btn-edit"
                                        style="border:1px solid #FDE68A;background:#FFFBEB;color:#D97706;padding:6px 9px;border-radius:8px;cursor:pointer;font-size:14px;line-height:1;"
                                        data-id="{{ $p->id }}"
                                        data-location-id="{{ $p->location_id }}"
                                        data-record-date="{{ $p->record_date->format('Y-m-d') }}"
                                        data-high-tide-time="{{ $p->high_tide_time }}"
                                        data-high-tide-level="{{ $p->high_tide_level }}"
                                        data-low-tide-time="{{ $p->low_tide_time }}"
                                        data-low-tide-level="{{ $p->low_tide_level }}"
                                        data-status="{{ $p->status }}">✏️</button>
                                    {{-- Hapus --}}
                                    <form action="{{ route('admin.data.destroy', $p->id) }}" method="POST"
                                          class="form-delete"
                                          data-info="{{ $p->location->name ?? 'prediksi ini' }} tgl {{ $p->record_date->format('d/m/Y') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus"
                                            style="border:1px solid #FECACA;background:#FEF2F2;color:#DC2626;padding:6px 9px;border-radius:8px;cursor:pointer;font-size:14px;line-height:1;">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="padding:48px 20px;text-align:center;color:#94A3B8;font-size:14px;">
                                <div style="margin-bottom:8px;font-size:36px;">🌊</div>
                                Tidak ada data prediksi ditemukan.<br>
                                <span style="font-size:12px;">Klik <strong>+ Tambah Prediksi</strong> untuk menambahkan data baru.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($predictions->hasPages())
            <div style="padding:14px 20px;border-top:1px solid #F1F5F9;">
                {{ $predictions->links() }}
            </div>
        @endif
    </div>

    {{-- ===== RIWAYAT UPLOAD BERKAS DATA ===== --}}
    @if(isset($uploads) && $uploads->count() > 0)
    <div style="background:#fff;border:1px solid #E2E8F0;border-radius:16px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:24px;">
        <div style="padding:16px 20px;border-bottom:1px solid #F1F5F9;display:flex;justify-content:space-between;align-items:center;">
            <div>
                <span style="font-size:15px;font-weight:700;color:#0F172A;">📁 Riwayat Import Berkas Data</span>
                <span style="margin-left:10px;font-size:12px;color:#94A3B8;">(5 Berkas Terbaru)</span>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;color:#334155;">
                <thead>
                    <tr style="background:#F8FAFC;font-size:12px;font-weight:700;color:#64748B;border-bottom:1px solid #E2E8F0;">
                        <th style="padding:12px 16px;text-align:left;">Nama Berkas</th>
                        <th style="padding:12px 16px;text-align:left;">Lokasi</th>
                        <th style="padding:12px 16px;text-align:left;">Periode</th>
                        <th style="padding:12px 16px;text-align:left;">Total Data</th>
                        <th style="padding:12px 16px;text-align:left;">Status</th>
                        <th style="padding:12px 16px;text-align:left;">Waktu Unggah</th>
                        <th style="padding:12px 16px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($uploads as $up)
                        <tr style="border-top:1px solid #F1F5F9;">
                            <td style="padding:12px 16px;">
                                <span style="text-transform:uppercase;font-size:10.5px;font-weight:700;background:#E0ECFF;color:#1D61E7;padding:3px 7px;border-radius:6px;margin-right:6px;">
                                    {{ $up->file_type }}
                                </span>
                                <strong style="color:#0F172A;">{{ $up->file_name }}</strong>
                            </td>
                            <td style="padding:12px 16px;">{{ $up->location->name ?? '-' }}</td>
                            <td style="padding:12px 16px;">{{ \Carbon\Carbon::create(null, $up->period_month, 1)->translatedFormat('F') }} {{ $up->period_year }}</td>
                            <td style="padding:12px 16px;font-weight:700;color:#0F172A;">{{ number_format($up->total_records) }} titik</td>
                            <td style="padding:12px 16px;">
                                @if($up->status === 'completed')
                                    <span style="background:#ECFDF5;color:#059669;border:1px solid #A7F3D0;padding:3px 10px;border-radius:9999px;font-size:11px;font-weight:700;">✅ Selesai</span>
                                @elseif($up->status === 'processing')
                                    <span style="background:#FFFBEB;color:#D97706;border:1px solid #FDE68A;padding:3px 10px;border-radius:9999px;font-size:11px;font-weight:700;">⏳ Proses</span>
                                @else
                                    <span style="background:#FEF2F2;color:#DC2626;border:1px solid #FECACA;padding:3px 10px;border-radius:9999px;font-size:11px;font-weight:700;">❌ Gagal</span>
                                @endif
                            </td>
                            <td style="padding:12px 16px;color:#94A3B8;font-size:12px;">{{ $up->created_at->format('d/m/Y H:i') }}</td>
                            <td style="padding:12px 16px;text-align:center;">
                                <form action="{{ route('admin.data.delete', $up->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus riwayat berkas {{ addslashes($up->file_name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="border:1px solid #FECACA;background:#FEF2F2;color:#DC2626;padding:4px 8px;border-radius:6px;cursor:pointer;font-size:12px;">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

{{-- ================================================================
     MODAL: IMPORT BERKAS (EXCEL / CSV / PDF)
     ================================================================ --}}
<div id="modalUpload"
    style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.55);z-index:9000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:20px;width:100%;max-width:540px;box-shadow:0 25px 60px rgba(0,0,0,0.2);overflow:hidden;">

        {{-- Header --}}
        <div style="padding:20px 24px;border-bottom:1px solid #F1F5F9;display:flex;align-items:center;justify-content:space-between;background:#F0F9FF;">
            <div>
                <h3 style="margin:0;font-size:16px;font-weight:800;color:#0F172A;">📥 Import Data Pasang Surut</h3>
                <p style="margin:3px 0 0;font-size:12px;color:#64748B;">Upload file Excel (.xlsx, .xls), CSV, atau PDF untuk diekstrak otomatis oleh Python parser.</p>
            </div>
            <button onclick="closeModal('modalUpload')"
                style="background:#F1F5F9;border:none;border-radius:8px;width:32px;height:32px;font-size:16px;cursor:pointer;color:#64748B;display:flex;align-items:center;justify-content:center;">✕</button>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.data.upload') }}" method="POST" enctype="multipart/form-data" style="padding:24px;">
            @csrf

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Pilih Lokasi Monitoring <span style="color:#ef4444;">*</span></label>
                <select name="location_id" required
                    style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;background:#fff;">
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->code }})</option>
                    @endforeach
                </select>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Bulan Periode <span style="color:#ef4444;">*</span></label>
                    <select name="period_month" required
                        style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;background:#fff;">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == 7 ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Tahun Periode <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="period_year" required value="2026" min="2020" max="2035"
                        style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Pilih Berkas (.csv, .xlsx, .xls, .pdf) <span style="color:#ef4444;">*</span></label>
                <div style="border:2px dashed #93C5FD;border-radius:12px;padding:24px;text-align:center;background:#F8FAFC;cursor:pointer;"
                     onclick="document.getElementById('uploadFileInput').click()">
                    <input type="file" name="file_data" id="uploadFileInput" accept=".csv,.xlsx,.xls,.pdf" required
                           style="display:none;"
                           onchange="document.getElementById('uploadFileText').innerText = this.files[0] ? '📄 ' + this.files[0].name : 'Klik untuk memilih berkas (.csv, .xlsx, .pdf)'">
                    <div style="font-size:32px;margin-bottom:6px;">📁</div>
                    <div id="uploadFileText" style="font-size:13px;font-weight:700;color:#0284C7;">
                        Klik untuk memilih berkas (.csv, .xlsx, .pdf)
                    </div>
                    <div style="font-size:11.5px;color:#94A3B8;margin-top:4px;">Ukuran maksimal: 15 MB per file</div>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:16px;border-top:1px solid #F1F5F9;">
                <button type="button" onclick="closeModal('modalUpload')"
                    style="padding:10px 20px;font-size:13px;font-weight:600;color:#64748B;background:#F1F5F9;border:none;border-radius:10px;cursor:pointer;">
                    Batal
                </button>
                <button type="submit"
                    style="padding:10px 22px;font-size:13px;font-weight:700;color:#fff;background:#0284C7;border:none;border-radius:10px;cursor:pointer;box-shadow:0 2px 8px rgba(2,132,199,0.25);">
                    ⚡ Proses & Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================================
     MODAL: TAMBAH PREDIKSI (MANUAL)
     ================================================================ --}}
<div id="modalTambah"
    style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.55);z-index:9000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:20px;width:100%;max-width:560px;box-shadow:0 25px 60px rgba(0,0,0,0.2);overflow:hidden;">

        {{-- Header --}}
        <div style="padding:20px 24px;border-bottom:1px solid #F1F5F9;display:flex;align-items:center;justify-content:space-between;background:#F8FAFC;">
            <div>
                <h3 style="margin:0;font-size:16px;font-weight:800;color:#0F172A;">➕ Tambah Prediksi Pasang Surut</h3>
                <p style="margin:3px 0 0;font-size:12px;color:#94A3B8;">Isi semua field di bawah ini untuk menambah data baru</p>
            </div>
            <button onclick="closeModal('modalTambah')"
                style="background:#F1F5F9;border:none;border-radius:8px;width:32px;height:32px;font-size:16px;cursor:pointer;color:#64748B;display:flex;align-items:center;justify-content:center;">✕</button>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.data.store') }}" method="POST" style="padding:24px;">
            @csrf

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Lokasi Stasiun <span style="color:#ef4444;">*</span></label>
                <select name="location_id" required
                    style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;background:#fff;">
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->code }})</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Tanggal Prediksi <span style="color:#ef4444;">*</span></label>
                <input type="date" name="record_date" required value="{{ now()->format('Y-m-d') }}"
                    style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Jam Pasang Tertinggi <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="high_tide_time" required placeholder="Contoh: 01.00 WIB"
                        style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Tinggi Pasang (meter) <span style="color:#ef4444;">*</span></label>
                    <input type="number" step="0.01" name="high_tide_level" required placeholder="Contoh: 0.81"
                        style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Jam Surut Terendah <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="low_tide_time" required placeholder="Contoh: 12.00 WIB"
                        style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Tinggi Surut (meter) <span style="color:#ef4444;">*</span></label>
                    <input type="number" step="0.01" name="low_tide_level" required placeholder="Contoh: -0.91"
                        style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                </div>
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Status Kondisi <span style="color:#ef4444;">*</span></label>
                <select name="status" required
                    style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;background:#fff;">
                    <option value="Aman">🟢 Aman</option>
                    <option value="Waspada">🟡 Waspada</option>
                    <option value="Bahaya">🔴 Bahaya</option>
                </select>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:16px;border-top:1px solid #F1F5F9;">
                <button type="button" onclick="closeModal('modalTambah')"
                    style="padding:10px 20px;font-size:13px;font-weight:600;color:#64748B;background:#F1F5F9;border:none;border-radius:10px;cursor:pointer;">
                    Batal
                </button>
                <button type="submit"
                    style="padding:10px 22px;font-size:13px;font-weight:700;color:#fff;background:#1D61E7;border:none;border-radius:10px;cursor:pointer;box-shadow:0 2px 8px rgba(29,97,231,0.25);">
                    💾 Simpan Prediksi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================================
     MODAL: EDIT PREDIKSI
     ================================================================ --}}
<div id="modalEdit"
    style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.55);z-index:9000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:20px;width:100%;max-width:560px;box-shadow:0 25px 60px rgba(0,0,0,0.2);overflow:hidden;">

        <div style="padding:20px 24px;border-bottom:1px solid #F1F5F9;display:flex;align-items:center;justify-content:space-between;background:#FFFBEB;">
            <div>
                <h3 style="margin:0;font-size:16px;font-weight:800;color:#0F172A;">✏️ Edit Prediksi Pasang Surut</h3>
                <p style="margin:3px 0 0;font-size:12px;color:#94A3B8;">Perbarui data prediksi yang dipilih</p>
            </div>
            <button onclick="closeModal('modalEdit')"
                style="background:#F1F5F9;border:none;border-radius:8px;width:32px;height:32px;font-size:16px;cursor:pointer;color:#64748B;display:flex;align-items:center;justify-content:center;">✕</button>
        </div>

        <form id="formEdit" method="POST" style="padding:24px;">
            @csrf
            @method('PUT')

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Lokasi Stasiun <span style="color:#ef4444;">*</span></label>
                <select name="location_id" id="editLocId" required
                    style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;background:#fff;">
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->code }})</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Tanggal Prediksi <span style="color:#ef4444;">*</span></label>
                <input type="date" name="record_date" id="editDate" required
                    style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Jam Pasang Tertinggi</label>
                    <input type="text" name="high_tide_time" id="editHighTime" required
                        style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Tinggi Pasang (meter)</label>
                    <input type="number" step="0.01" name="high_tide_level" id="editHighLevel" required
                        style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Jam Surut Terendah</label>
                    <input type="text" name="low_tide_time" id="editLowTime" required
                        style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Tinggi Surut (meter)</label>
                    <input type="number" step="0.01" name="low_tide_level" id="editLowLevel" required
                        style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                </div>
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Status Kondisi</label>
                <select name="status" id="editStatus" required
                    style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;background:#fff;">
                    <option value="Aman">🟢 Aman</option>
                    <option value="Waspada">🟡 Waspada</option>
                    <option value="Bahaya">🔴 Bahaya</option>
                </select>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:16px;border-top:1px solid #F1F5F9;">
                <button type="button" onclick="closeModal('modalEdit')"
                    style="padding:10px 20px;font-size:13px;font-weight:600;color:#64748B;background:#F1F5F9;border:none;border-radius:10px;cursor:pointer;">
                    Batal
                </button>
                <button type="submit"
                    style="padding:10px 22px;font-size:13px;font-weight:700;color:#fff;background:#D97706;border:none;border-radius:10px;cursor:pointer;box-shadow:0 2px 8px rgba(217,119,6,0.25);">
                    💾 Update Data
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================================
     MODAL: DETAIL PREDIKSI
     ================================================================ --}}
<div id="modalDetail"
    style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.55);z-index:9000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:20px;width:100%;max-width:480px;box-shadow:0 25px 60px rgba(0,0,0,0.2);overflow:hidden;">

        <div style="padding:20px 24px;border-bottom:1px solid #F1F5F9;display:flex;align-items:center;justify-content:space-between;background:#EFF6FF;">
            <div>
                <h3 style="margin:0;font-size:16px;font-weight:800;color:#0F172A;">👁️ Detail Prediksi</h3>
                <p style="margin:3px 0 0;font-size:12px;color:#94A3B8;">Informasi lengkap data prediksi</p>
            </div>
            <button onclick="closeModal('modalDetail')"
                style="background:#F1F5F9;border:none;border-radius:8px;width:32px;height:32px;font-size:16px;cursor:pointer;color:#64748B;display:flex;align-items:center;justify-content:center;">✕</button>
        </div>

        <div id="detailBody" style="padding:24px;">
            {{-- Diisi JavaScript --}}
        </div>

        <div style="padding:16px 24px;border-top:1px solid #F1F5F9;text-align:right;">
            <button onclick="closeModal('modalDetail')"
                style="padding:10px 22px;font-size:13px;font-weight:600;color:#64748B;background:#F1F5F9;border:none;border-radius:10px;cursor:pointer;">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ================================================================
     JAVASCRIPT
     ================================================================ --}}
<script>
    // --- Modal Helpers ---
    function openModal(id) {
        var el = document.getElementById(id);
        el.style.display = 'flex';
        setTimeout(function() { el.style.opacity = 1; }, 10);
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    // Tutup saat klik backdrop
    ['modalUpload','modalTambah','modalEdit','modalDetail'].forEach(function(id) {
        var modal = document.getElementById(id);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeModal(id);
            });
        }
    });

    // --- Edit Modal ---
    function openEdit(data) {
        var baseUrl = "{{ url('admin/data/prediksi') }}/" + data.id;
        document.getElementById('formEdit').action = baseUrl;
        document.getElementById('editLocId').value    = data.location_id;
        document.getElementById('editDate').value     = data.record_date;
        document.getElementById('editHighTime').value = data.high_tide_time;
        document.getElementById('editHighLevel').value= data.high_tide_level;
        document.getElementById('editLowTime').value  = data.low_tide_time;
        document.getElementById('editLowLevel').value = data.low_tide_level;
        document.getElementById('editStatus').value   = data.status;
        openModal('modalEdit');
    }

    // --- Detail Modal ---
    function openDetail(d) {
        var badgeStyle = {
            'Aman':    'background:#ECFDF5;color:#059669;border:1px solid #A7F3D0;',
            'Waspada': 'background:#FFFBEB;color:#D97706;border:1px solid #FDE68A;',
            'Bahaya':  'background:#FEF2F2;color:#DC2626;border:1px solid #FECACA;',
        };
        var bs = badgeStyle[d.status] || 'background:#F1F5F9;color:#64748B;border:1px solid #CBD5E1;';

        var rows = [
            ['📍 Lokasi',         '<strong style="color:#0369A1;">' + d.lokasi + '</strong>'],
            ['📅 Tanggal',        d.tanggal],
            ['🌊 Jam Pasang',     d.high_time],
            ['📈 Tinggi Pasang',  '<strong style="color:#1D61E7;">' + d.high_level + ' m</strong>'],
            ['⬇️ Jam Surut',      d.low_time],
            ['📉 Tinggi Surut',   '<strong style="color:#7C3AED;">' + d.low_level + ' m</strong>'],
            ['🚦 Status',         '<span style="' + bs + 'padding:3px 12px;border-radius:9999px;font-size:12px;font-weight:700;">' + d.status + '</span>'],
            ['🕐 Diperbarui',     '<span style="color:#94A3B8;">' + d.update + '</span>'],
        ];

        var html = '<table style="width:100%;border-collapse:collapse;font-size:13.5px;">';
        rows.forEach(function(r) {
            html += '<tr style="border-bottom:1px solid #F8FAFC;">'
                  + '<td style="padding:10px 8px;color:#64748B;font-weight:700;width:155px;white-space:nowrap;">' + r[0] + '</td>'
                  + '<td style="padding:10px 8px;">' + r[1] + '</td>'
                  + '</tr>';
        });
        html += '</table>';

        document.getElementById('detailBody').innerHTML = html;
        openModal('modalDetail');
    }

    // Event Delegation for Table Buttons
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-detail').forEach(function(btn) {
            btn.addEventListener('click', function() {
                openDetail({
                    lokasi: this.dataset.lokasi,
                    tanggal: this.dataset.tanggal,
                    high_time: this.dataset.highTime,
                    high_level: this.dataset.highLevel,
                    low_time: this.dataset.lowTime,
                    low_level: this.dataset.lowLevel,
                    status: this.dataset.status,
                    update: this.dataset.update
                });
            });
        });

        document.querySelectorAll('.btn-edit').forEach(function(btn) {
            btn.addEventListener('click', function() {
                openEdit({
                    id: this.dataset.id,
                    location_id: this.dataset.locationId,
                    record_date: this.dataset.recordDate,
                    high_tide_time: this.dataset.highTideTime,
                    high_tide_level: this.dataset.highTideLevel,
                    low_tide_time: this.dataset.lowTideTime,
                    low_tide_level: this.dataset.lowTideLevel,
                    status: this.dataset.status
                });
            });
        });

        document.querySelectorAll('.form-delete').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                var info = this.dataset.info || 'data ini';
                if (!confirm('Hapus prediksi ' + info + '?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>

</x-app-layout>