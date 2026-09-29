<x-app-layout>
<div class="content">

    {{-- Flash Alert --}}
    @if(session('success'))
        <div style="background:#dcfce7;border:1px solid #bbf7d0;color:#15803d;padding:12px 18px;border-radius:10px;margin-bottom:20px;font-size:13px;font-weight:600;">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div style="background:#fef3c7;border:1px solid #fde68a;color:#b45309;padding:12px 18px;border-radius:10px;margin-bottom:20px;font-size:13px;font-weight:600;">
            ⚠️ {{ session('warning') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background:#fee2e2;border:1px solid #fecaca;color:#b91c1c;padding:12px 18px;border-radius:10px;margin-bottom:20px;font-size:13px;">
            @foreach($errors->all() as $error)
                <div>❌ {{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Header Row --}}
    <div class="page-header-row">
        <div>
            <h1>Kelola Prediksi Pasang Surut</h1>
            <p>Kelola data prediksi pasang surut di seluruh lokasi monitoring</p>
        </div>
        <button onclick="openModal('modalTambah')" class="btn-primary-add">
            ＋ Tambah Prediksi
        </button>
    </div>

    {{-- 4 Stat Cards --}}
    <div class="stats-grid" style="margin-bottom:24px;">
        <div class="stat-card">
            <div class="icon blue">📊</div>
            <div>
                <div class="label">Total Data</div>
                <div class="value">{{ $totalData }}</div>
                <div class="sub">● Data Pasang Surut</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon indigo">📍</div>
            <div>
                <div class="label">Lokasi Monitoring</div>
                <div class="value">{{ $totalLokasi }}</div>
                <div class="sub muted">Lokasi Aktif</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon sky">📋</div>
            <div>
                <div class="label">Data Hari Ini</div>
                <div class="value">{{ $dataHariIni }}</div>
                <div class="sub muted">Data Terbaru</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon" style="background:#fff7ed;">🕐</div>
            <div>
                <div class="label">Update Terakhir</div>
                <div class="value" style="font-size:18px;">{{ $updateTerakhir }}</div>
                <div class="sub muted">Data Terbaru</div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <form method="GET" action="{{ route('admin.data') }}" id="filterForm">
        <div class="filter-card">
            <div class="filter-item">
                <label>Cari Lokasi</label>
                <select name="location_id" class="filter-input">
                    <option value="">Pilih Lokasi</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item">
                <label>Tanggal</label>
                <input type="date" name="date" class="filter-input"
                       value="{{ request('date') }}"
                       placeholder="{{ now()->format('d/m/Y') }}">
            </div>
            <div class="filter-item">
                <label>Status Kondisi</label>
                <select name="status" class="filter-input">
                    <option value="">Semua Kondisi</option>
                    <option value="Aman" {{ request('status') === 'Aman' ? 'selected' : '' }}>Aman</option>
                    <option value="Waspada" {{ request('status') === 'Waspada' ? 'selected' : '' }}>Waspada</option>
                    <option value="Bahaya" {{ request('status') === 'Bahaya' ? 'selected' : '' }}>Bahaya</option>
                </select>
            </div>
            <div class="filter-item">
                <label>Urutkan</label>
                <select name="sort" class="filter-input">
                    <option value="terbaru" {{ request('sort', 'terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>Terlama</option>
                    <option value="lokasi" {{ request('sort') === 'lokasi' ? 'selected' : '' }}>Lokasi A-Z</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-search">
                    🔍 Cari
                </button>
                <a href="{{ route('admin.data') }}" class="btn-reset">
                    ↺ Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Tabel Data Prediksi --}}
    <div class="table-card">
        <div style="padding:18px 20px 10px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <div>
                <span style="font-size:15px;font-weight:700;color:#1e3a8a;">Daftar Prediksi Pasang Surut</span>
                <span style="margin-left:10px;font-size:12px;color:#64748b;">({{ $predictions->total() }} data ditemukan)</span>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="sipasut-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Jam Pasang</th>
                        <th>Tinggi Pasang</th>
                        <th>Jam Surut</th>
                        <th>Tinggi Surut</th>
                        <th>Status</th>
                        <th>Update</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($predictions as $i => $p)
                        <tr>
                            <td>{{ ($predictions->currentPage() - 1) * $predictions->perPage() + $i + 1 }}</td>
                            <td style="font-weight:600;color:#0369a1;">
                                {{ $p->location->name ?? '-' }}
                            </td>
                            <td>{{ $p->record_date->format('d M Y') }}</td>
                            <td>{{ $p->high_tide_time }}</td>
                            <td>
                                <span style="font-weight:700;color:#15803d;">{{ number_format($p->high_tide_level, 2) }} m</span>
                            </td>
                            <td>{{ $p->low_tide_time }}</td>
                            <td>
                                <span style="font-weight:700;color:#b91c1c;">{{ number_format($p->low_tide_level, 2) }} m</span>
                            </td>
                            <td>
                                @php $st = strtolower($p->status); @endphp
                                <span class="badge-status {{ $st }}">{{ $p->status }}</span>
                            </td>
                            <td style="color:#64748b;font-size:11.5px;">
                                {{ $p->updated_at->format('H.i \W\I\B') }}
                            </td>
                            <td>
                                <div class="action-btns">
                                    {{-- Detail --}}
                                    <button type="button" class="action-btn view"
                                            title="Detail"
                                            onclick="openDetail({{ json_encode([
                                                'lokasi' => $p->location->name ?? '-',
                                                'tanggal' => $p->record_date->format('d M Y'),
                                                'high_time' => $p->high_tide_time,
                                                'high_level' => number_format($p->high_tide_level, 2),
                                                'low_time' => $p->low_tide_time,
                                                'low_level' => number_format($p->low_tide_level, 2),
                                                'status' => $p->status,
                                                'update' => $p->updated_at->format('d M Y H.i \W\I\B'),
                                            ]) }})">
                                        👁️
                                    </button>
                                    {{-- Edit --}}
                                    <button type="button" class="action-btn edit"
                                            title="Edit"
                                            onclick="openEdit({{ json_encode([
                                                'id' => $p->id,
                                                'location_id' => $p->location_id,
                                                'record_date' => $p->record_date->format('Y-m-d'),
                                                'high_tide_time' => $p->high_tide_time,
                                                'high_tide_level' => $p->high_tide_level,
                                                'low_tide_time' => $p->low_tide_time,
                                                'low_tide_level' => $p->low_tide_level,
                                                'status' => $p->status,
                                            ]) }})">
                                        ✏️
                                    </button>
                                    {{-- Hapus --}}
                                    <form action="{{ route('admin.data.destroy', $p->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus data prediksi {{ $p->location->name ?? '' }} tanggal {{ $p->record_date->format('d/m/Y') }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Hapus">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align:center;padding:40px;color:#94a3b8;">
                                Tidak ada data prediksi. Tambah prediksi baru dengan tombol <strong>+ Tambah Prediksi</strong>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($predictions->hasPages())
            <div style="padding:16px 20px;">
                {{ $predictions->links() }}
            </div>
        @endif
    </div>

    {{-- Riwayat Upload Berkas (Ringkas) --}}
    @if($uploads->count() > 0)
    <div class="table-card" style="margin-top:0;">
        <div style="padding:18px 20px 10px;border-bottom:1px solid #f1f5f9;">
            <span style="font-size:14px;font-weight:700;color:#475569;">📁 Riwayat Upload Berkas Data (5 Terbaru)</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="sipasut-table">
                <thead>
                    <tr>
                        <th>Berkas</th>
                        <th>Lokasi</th>
                        <th>Periode</th>
                        <th>Total Titik</th>
                        <th>Status</th>
                        <th>Waktu Unggah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($uploads as $up)
                        <tr>
                            <td>
                                <span style="text-transform:uppercase;font-size:10px;font-weight:700;background:#f1f5f9;color:#475569;padding:2px 6px;border-radius:4px;">{{ $up->file_type }}</span>
                                <span style="margin-left:6px;font-weight:600;">{{ $up->file_name }}</span>
                            </td>
                            <td>{{ $up->location->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::create(null, $up->period_month, 1)->translatedFormat('F') }} {{ $up->period_year }}</td>
                            <td>{{ number_format($up->total_records) }}</td>
                            <td>
                                @if($up->status === 'completed')
                                    <span class="badge-status aman">Selesai</span>
                                @elseif($up->status === 'processing')
                                    <span class="badge-status waspada">Proses</span>
                                @else
                                    <span class="badge-status bahaya">Gagal</span>
                                @endif
                            </td>
                            <td style="color:#64748b;font-size:12px;">{{ $up->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

{{-- ====================== MODAL TAMBAH ====================== --}}
<div class="modal-overlay" id="modalTambah">
    <div class="modal-content-box">
        <div class="modal-header-custom">
            <h3>➕ Tambah Prediksi Pasang Surut</h3>
            <button onclick="closeModal('modalTambah')" class="modal-close-btn">✕</button>
        </div>
        <form action="{{ route('admin.data.store') }}" method="POST">
            @csrf
            <div class="modal-body-custom">
                <div class="form-group-custom">
                    <label>Lokasi Stasiun</label>
                    <select name="location_id" required>
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group-custom">
                    <label>Tanggal Prediksi</label>
                    <input type="date" name="record_date" required value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="form-grid-2">
                    <div class="form-group-custom" style="margin-bottom:0;">
                        <label>Jam Pasang Tertinggi</label>
                        <input type="text" name="high_tide_time" required placeholder="01.00 WIB">
                    </div>
                    <div class="form-group-custom" style="margin-bottom:0;">
                        <label>Tinggi Pasang (meter)</label>
                        <input type="number" step="0.01" name="high_tide_level" required placeholder="0.81">
                    </div>
                </div>
                <div class="form-grid-2" style="margin-top:14px;">
                    <div class="form-group-custom" style="margin-bottom:0;">
                        <label>Jam Surut Terendah</label>
                        <input type="text" name="low_tide_time" required placeholder="12.00 WIB">
                    </div>
                    <div class="form-group-custom" style="margin-bottom:0;">
                        <label>Tinggi Surut (meter)</label>
                        <input type="number" step="0.01" name="low_tide_level" required placeholder="-0.81">
                    </div>
                </div>
                <div class="form-group-custom" style="margin-top:14px;">
                    <label>Status Kondisi</label>
                    <select name="status" required>
                        <option value="Aman">🟢 Aman</option>
                        <option value="Waspada">🟡 Waspada</option>
                        <option value="Bahaya">🔴 Bahaya</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" onclick="closeModal('modalTambah')" class="btn-reset">Batal</button>
                <button type="submit" class="btn-primary-add" style="border-radius:8px;">💾 Simpan Prediksi</button>
            </div>
        </form>
    </div>
</div>

{{-- ====================== MODAL EDIT ====================== --}}
<div class="modal-overlay" id="modalEdit">
    <div class="modal-content-box">
        <div class="modal-header-custom">
            <h3>✏️ Edit Prediksi Pasang Surut</h3>
            <button onclick="closeModal('modalEdit')" class="modal-close-btn">✕</button>
        </div>
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body-custom">
                <div class="form-group-custom">
                    <label>Lokasi Stasiun</label>
                    <select name="location_id" id="editLocationId" required>
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group-custom">
                    <label>Tanggal Prediksi</label>
                    <input type="date" name="record_date" id="editRecordDate" required>
                </div>
                <div class="form-grid-2">
                    <div class="form-group-custom" style="margin-bottom:0;">
                        <label>Jam Pasang Tertinggi</label>
                        <input type="text" name="high_tide_time" id="editHighTime" required>
                    </div>
                    <div class="form-group-custom" style="margin-bottom:0;">
                        <label>Tinggi Pasang (meter)</label>
                        <input type="number" step="0.01" name="high_tide_level" id="editHighLevel" required>
                    </div>
                </div>
                <div class="form-grid-2" style="margin-top:14px;">
                    <div class="form-group-custom" style="margin-bottom:0;">
                        <label>Jam Surut Terendah</label>
                        <input type="text" name="low_tide_time" id="editLowTime" required>
                    </div>
                    <div class="form-group-custom" style="margin-bottom:0;">
                        <label>Tinggi Surut (meter)</label>
                        <input type="number" step="0.01" name="low_tide_level" id="editLowLevel" required>
                    </div>
                </div>
                <div class="form-group-custom" style="margin-top:14px;">
                    <label>Status Kondisi</label>
                    <select name="status" id="editStatus" required>
                        <option value="Aman">🟢 Aman</option>
                        <option value="Waspada">🟡 Waspada</option>
                        <option value="Bahaya">🔴 Bahaya</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" onclick="closeModal('modalEdit')" class="btn-reset">Batal</button>
                <button type="submit" class="btn-primary-add" style="border-radius:8px;">💾 Update Data</button>
            </div>
        </form>
    </div>
</div>

{{-- ====================== MODAL DETAIL ====================== --}}
<div class="modal-overlay" id="modalDetail">
    <div class="modal-content-box">
        <div class="modal-header-custom">
            <h3>👁️ Detail Prediksi Pasang Surut</h3>
            <button onclick="closeModal('modalDetail')" class="modal-close-btn">✕</button>
        </div>
        <div class="modal-body-custom" id="detailContent">
            {{-- Diisi via JavaScript --}}
        </div>
        <div class="modal-footer-custom">
            <button onclick="closeModal('modalDetail')" class="btn-reset">Tutup</button>
        </div>
    </div>
</div>

<script>
    // === Modal Helpers ===
    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }
    // Close on backdrop click
    document.querySelectorAll('.modal-overlay').forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) modal.classList.remove('active');
        });
    });

    // === Open Edit Modal ===
    function openEdit(data) {
        var form = document.getElementById('formEdit');
        var baseUrl = "{{ route('admin.data.update', '__ID__') }}";
        form.action = baseUrl.replace('__ID__', data.id);

        document.getElementById('editLocationId').value = data.location_id;
        document.getElementById('editRecordDate').value = data.record_date;
        document.getElementById('editHighTime').value = data.high_tide_time;
        document.getElementById('editHighLevel').value = data.high_tide_level;
        document.getElementById('editLowTime').value = data.low_tide_time;
        document.getElementById('editLowLevel').value = data.low_tide_level;
        document.getElementById('editStatus').value = data.status;

        openModal('modalEdit');
    }

    // === Open Detail Modal ===
    function openDetail(d) {
        var statusClass = {'Aman': 'aman', 'Waspada': 'waspada', 'Bahaya': 'bahaya'}[d.status] || 'aman';
        var html = `
            <table style="width:100%;font-size:13px;border-collapse:collapse;">
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 8px;color:#64748b;font-weight:600;width:160px;">📍 Lokasi</td>
                    <td style="padding:10px 8px;font-weight:700;color:#0369a1;">${d.lokasi}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 8px;color:#64748b;font-weight:600;">📅 Tanggal</td>
                    <td style="padding:10px 8px;font-weight:600;">${d.tanggal}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 8px;color:#64748b;font-weight:600;">🌊 Jam Pasang</td>
                    <td style="padding:10px 8px;">${d.high_time}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 8px;color:#64748b;font-weight:600;">📈 Tinggi Pasang</td>
                    <td style="padding:10px 8px;font-weight:700;color:#15803d;">${d.high_level} m</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 8px;color:#64748b;font-weight:600;">⬇️ Jam Surut</td>
                    <td style="padding:10px 8px;">${d.low_time}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 8px;color:#64748b;font-weight:600;">📉 Tinggi Surut</td>
                    <td style="padding:10px 8px;font-weight:700;color:#b91c1c;">${d.low_level} m</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 8px;color:#64748b;font-weight:600;">🚦 Status</td>
                    <td style="padding:10px 8px;"><span class="badge-status ${statusClass}">${d.status}</span></td>
                </tr>
                <tr>
                    <td style="padding:10px 8px;color:#64748b;font-weight:600;">🕐 Diperbarui</td>
                    <td style="padding:10px 8px;color:#475569;">${d.update}</td>
                </tr>
            </table>
        `;
        document.getElementById('detailContent').innerHTML = html;
        openModal('modalDetail');
    }
</script>

</x-app-layout>
