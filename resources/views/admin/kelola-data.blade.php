<x-app-layout>
    <div class="p-8 space-y-6 bg-[#F8FAFC] min-h-screen font-sans" style="background-color: #F8FAFC;" x-data="{ openUploadModal: false }">

        <!-- Title & Tombol Tambah Prediksi -->
        <div class="flex items-center justify-between">
            <div>
                <!-- Font ditipiskan sedikit saja (font-weight: 700) -->
                <h1 class="text-3xl font-bold text-[#0F172A] tracking-tight" style="color: #0F172A; font-weight: 700;">Kelola Prediksi Pasang Surut</h1>
                <p class="text-sm font-medium text-slate-500 mt-1">Kelola data prediksi pasang surut di seluruh lokasi monitoring</p>
            </div>
            
            <!-- Tombol Tambah Prediksi digeser agak ke kiri sedikit (mr-4) -->
            <button @click="openUploadModal = true"
                style="background-color: #1D61E7; color: #FFFFFF; font-weight: 700; padding: 12px 24px; border-radius: 12px; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; margin-right: 16px;"
                class="hover:bg-blue-700 shadow-md transition-all active:scale-95 mr-4">
                <span style="font-size: 18px; font-weight: 800; line-height: 1;">+</span>
                <span style="font-size: 14px; font-weight: 700;">Tambah Prediksi</span>
            </button>
        </div>

        <!-- Flash Alert Notification -->
        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-sm">
                <span>✔ {{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-3.5 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-sm">
                <span>⚠️ {{ session('warning') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs space-y-1 shadow-sm">
                @foreach($errors->all() as $error)
                    <div>❌ {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- 4 Stat Cards Mendatar Kesamping -->
        <div class="grid grid-cols-4 gap-4" style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px;">
            
            <!-- Card 1: Total Data -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px;">
                <div style="width: 56px; height: 56px; background-color: #E0ECFF; color: #1D61E7; border-radius: 9999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg class="w-7 h-7" style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <div>
                    <div style="font-size: 12px; font-weight: 700; color: #64748B;">Total Data</div>
                    <div style="font-size: 28px; font-weight: 900; color: #0F172A; line-height: 1.2;">{{ $totalData ?? 25 }}</div>
                    <div style="font-size: 12px; font-weight: 600; color: #64748B;">Data Pasang Surut</div>
                </div>
            </div>

            <!-- Card 2: Lokasi Monitoring -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px;">
                <div style="width: 56px; height: 56px; background-color: #E0ECFF; color: #1D61E7; border-radius: 9999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg class="w-7 h-7" style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                </div>
                <div>
                    <div style="font-size: 12px; font-weight: 700; color: #64748B;">Lokasi Monitoring</div>
                    <div style="font-size: 28px; font-weight: 900; color: #0F172A; line-height: 1.2;">{{ $totalLokasi ?? 5 }}</div>
                    <div style="font-size: 12px; font-weight: 600; color: #64748B;">Lokasi Aktif</div>
                </div>
            </div>

            <!-- Card 3: Data Hari Ini -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px;">
                <div style="width: 56px; height: 56px; background-color: #E0ECFF; color: #1D61E7; border-radius: 9999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg class="w-7 h-7" style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <div style="font-size: 12px; font-weight: 700; color: #64748B;">Data Hari Ini</div>
                    <div style="font-size: 28px; font-weight: 900; color: #0F172A; line-height: 1.2;">{{ $lokasiTerkonfirmasi ?? 5 }}</div>
                    <div style="font-size: 12px; font-weight: 600; color: #64748B;">Data Terbaru</div>
                </div>
            </div>

            <!-- Card 4: Update Terakhir -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px;">
                <div style="width: 56px; height: 56px; background-color: #E0ECFF; color: #1D61E7; border-radius: 9999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg class="w-7 h-7" style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <div style="font-size: 12px; font-weight: 700; color: #64748B;">Update Terakhir</div>
                    <div style="font-size: 24px; font-weight: 900; color: #0F172A; line-height: 1.2;">{{ $lastUpdated ?? '09.00' }} <span style="font-size: 14px; font-weight: 700;">WIB</span></div>
                    <div style="font-size: 12px; font-weight: 600; color: #64748B;">Data Terbaru</div>
                </div>
            </div>

        </div>

        <!-- Filter Bar Horizontal -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 16px;">
            <form action="{{ route('admin.data') }}" method="GET" style="display: flex; flex-direction: row; align-items: flex-end; gap: 12px; width: 100%;">
                
                <!-- Cari Lokasi -->
                <div style="flex: 1;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">Cari Lokasi</label>
                    <select name="station_id" style="width: 100%; border: 1px solid #CBD5E1; border-radius: 12px; padding: 12px; font-size: 14px; font-weight: 500; color: #334155; background-color: #FFFFFF; outline: none;">
                        <option value="">Pilih Lokasi</option>
                        @if(isset($stations))
                            @foreach ($stations as $station)
                                <option value="{{ $station->id }}">{{ $station->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Tanggal -->
                <div style="flex: 1;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">Tanggal</label>
                    <input type="date" name="tanggal" value="2026-09-07" style="width: 100%; border: 1px solid #CBD5E1; border-radius: 12px; padding: 12px; font-size: 14px; font-weight: 500; color: #334155; background-color: #FFFFFF; outline: none;">
                </div>

                <!-- Status Kondisi -->
                <div style="flex: 1;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">Status Kondisi</label>
                    <select name="status" style="width: 100%; border: 1px solid #CBD5E1; border-radius: 12px; padding: 12px; font-size: 14px; font-weight: 500; color: #334155; background-color: #FFFFFF; outline: none;">
                        <option value="">Semua Kondisi</option>
                        <option value="Aman">Aman</option>
                        <option value="Waspada">Waspada</option>
                        <option value="Bahaya">Bahaya</option>
                    </select>
                </div>

                <!-- Urutkan -->
                <div style="flex: 1;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">Urutkan</label>
                    <select name="order" style="width: 100%; border: 1px solid #CBD5E1; border-radius: 12px; padding: 12px; font-size: 14px; font-weight: 500; color: #334155; background-color: #FFFFFF; outline: none;">
                        <option value="latest">Terbaru</option>
                        <option value="oldest">Terlama</option>
                    </select>
                </div>

                <!-- Tombol Cari -->
                <div style="width: 120px;">
                    <button type="submit" style="width: 100%; border: 1px solid #93C5FD; background-color: #FFFFFF; color: #1D61E7; font-weight: 700; font-size: 14px; padding: 12px; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        🔍 Cari
                    </button>
                </div>

                <!-- Tombol Reset -->
                <div style="width: 120px;">
                    <a href="{{ route('admin.data') }}" style="width: 100%; border: 1px solid #93C5FD; background-color: #FFFFFF; color: #1D61E7; font-weight: 700; font-size: 14px; padding: 12px; border-radius: 12px; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        🔄 Reset
                    </a>
                </div>

            </form>
        </div>

        <!-- Tabel Pasang Surut -->
        <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
            <div style="overflow-x: auto;">
                <table style="width: 100%; text-align: left; border-collapse: collapse; font-size: 14px;">
                    <thead style="background-color: #D7E5F9; color: #0F172A; font-weight: 700; font-size: 13px;">
                        <tr>
                            <th style="padding: 16px 20px;">No</th>
                            <th style="padding: 16px 20px;">Lokasi</th>
                            <th style="padding: 16px 20px;">Tanggal</th>
                            <th style="padding: 16px 20px;">Jam Pasang</th>
                            <th style="padding: 16px 20px;">Tinggi Pasang</th>
                            <th style="padding: 16px 20px;">Jam Surut</th>
                            <th style="padding: 16px 20px;">Tinggi Surut</th>
                            <th style="padding: 16px 20px;">Status</th>
                            <th style="padding: 16px 20px;">Update</th>
                            <th style="padding: 16px 20px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="font-weight: 500; color: #334155;">
                        @if(isset($records) && $records->count() > 0)
                            @foreach ($records as $index => $record)
                                <tr style="border-top: 1px solid #F1F5F9;">
                                    <td style="padding: 16px 20px; color: #64748B;">{{ $index + 1 }}.</td>
                                    <td style="padding: 16px 20px; font-weight: 700; color: #0F172A;">{{ $record->station->name ?? 'Surabaya Timur' }}</td>
                                    <td style="padding: 16px 20px;">{{ \Carbon\Carbon::parse($record->recorded_at)->format('d Juli Y') }}</td>
                                    <td style="padding: 16px 20px;">{{ \Carbon\Carbon::parse($record->recorded_at)->format('H.i') }} WIB</td>
                                    <td style="padding: 16px 20px; font-weight: 700; color: #1D61E7;">{{ number_format($record->water_level_cm / 100, 2) }} m</td>
                                    <td style="padding: 16px 20px;">12.00 WIB</td>
                                    <td style="padding: 16px 20px; font-weight: 700; color: #1D61E7;">-0.91 m</td>
                                    <td style="padding: 16px 20px;">
                                        <span style="background-color: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 700;">
                                            Aman
                                        </span>
                                    </td>
                                    <td style="padding: 16px 20px; color: #64748B;">10.00 WIB</td>
                                    <td style="padding: 16px 20px; text-align: center;">
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                            <button style="border: 1px solid #93C5FD; background-color: #EFF6FF; color: #1D4ED8; padding: 6px 10px; border-radius: 8px; cursor: pointer;">👁️</button>
                                            <button style="border: 1px solid #FDE68A; background-color: #FEF3C7; color: #D97706; padding: 6px 10px; border-radius: 8px; cursor: pointer;">✏️</button>
                                            <form action="{{ route('admin.data.delete', $record->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="border: 1px solid #FECACA; background-color: #FEE2E2; color: #DC2626; padding: 6px 10px; border-radius: 8px; cursor: pointer;">🗑️</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr style="border-top: 1px solid #F1F5F9;">
                                <td style="padding: 16px 20px; color: #64748B;">1.</td>
                                <td style="padding: 16px 20px; font-weight: 700; color: #0F172A;">Surabaya Timur</td>
                                <td style="padding: 16px 20px;">08 Juli 2026</td>
                                <td style="padding: 16px 20px;">01.00 WIB</td>
                                <td style="padding: 16px 20px; font-weight: 700; color: #1D61E7;">0.61 m</td>
                                <td style="padding: 16px 20px;">12.00 WIB</td>
                                <td style="padding: 16px 20px; font-weight: 700; color: #1D61E7;">-0.91 m</td>
                                <td style="padding: 16px 20px;">
                                    <span style="background-color: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 700;">
                                        Aman
                                    </span>
                                </td>
                                <td style="padding: 16px 20px; color: #64748B;">10.00 WIB</td>
                                <td style="padding: 16px 20px; text-align: center;">
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                        <button style="border: 1px solid #93C5FD; background-color: #EFF6FF; color: #1D4ED8; padding: 6px 10px; border-radius: 8px;">👁️</button>
                                        <button style="border: 1px solid #FDE68A; background-color: #FEF3C7; color: #D97706; padding: 6px 10px; border-radius: 8px;">✏️</button>
                                        <button style="border: 1px solid #FECACA; background-color: #FEE2E2; color: #DC2626; padding: 6px 10px; border-radius: 8px;">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                            <tr style="border-top: 1px solid #F1F5F9;">
                                <td style="padding: 16px 20px; color: #64748B;">2.</td>
                                <td style="padding: 16px 20px; font-weight: 700; color: #0F172A;">Surabaya Barat</td>
                                <td style="padding: 16px 20px;">08 Juli 2026</td>
                                <td style="padding: 16px 20px;">03.15 WIB</td>
                                <td style="padding: 16px 20px; font-weight: 700; color: #1D61E7;">0.62 m</td>
                                <td style="padding: 16px 20px;">12.45 WIB</td>
                                <td style="padding: 16px 20px; font-weight: 700; color: #1D61E7;">0.13 m</td>
                                <td style="padding: 16px 20px;">
                                    <span style="background-color: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 700;">
                                        Aman
                                    </span>
                                </td>
                                <td style="padding: 16px 20px; color: #64748B;">11.00 WIB</td>
                                <td style="padding: 16px 20px; text-align: center;">
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                        <button style="border: 1px solid #93C5FD; background-color: #EFF6FF; color: #1D4ED8; padding: 6px 10px; border-radius: 8px;">👁️</button>
                                        <button style="border: 1px solid #FDE68A; background-color: #FEF3C7; color: #D97706; padding: 6px 10px; border-radius: 8px;">✏️</button>
                                        <button style="border: 1px solid #FECACA; background-color: #FEE2E2; color: #DC2626; padding: 6px 10px; border-radius: 8px;">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                            <tr style="border-top: 1px solid #F1F5F9;">
                                <td style="padding: 16px 20px; color: #64748B;">3.</td>
                                <td style="padding: 16px 20px; font-weight: 700; color: #0F172A;">Surabaya Pelabuhan</td>
                                <td style="padding: 16px 20px;">08 Juli 2026</td>
                                <td style="padding: 16px 20px;">03.30 WIB</td>
                                <td style="padding: 16px 20px; font-weight: 700; color: #1D61E7;">0.63 m</td>
                                <td style="padding: 16px 20px;">13.25 WIB</td>
                                <td style="padding: 16px 20px; font-weight: 700; color: #1D61E7;">0.10 m</td>
                                <td style="padding: 16px 20px;">
                                    <span style="background-color: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 700;">
                                        Aman
                                    </span>
                                </td>
                                <td style="padding: 16px 20px; color: #64748B;">12.00 WIB</td>
                                <td style="padding: 16px 20px; text-align: center;">
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                        <button style="border: 1px solid #93C5FD; background-color: #EFF6FF; color: #1D4ED8; padding: 6px 10px; border-radius: 8px;">👁️</button>
                                        <button style="border: 1px solid #FDE68A; background-color: #FEF3C7; color: #D97706; padding: 6px 10px; border-radius: 8px;">✏️</button>
                                        <button style="border: 1px solid #FECACA; background-color: #FEE2E2; color: #DC2626; padding: 6px 10px; border-radius: 8px;">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px; border-top: 1px solid #E2E8F0;">
                @if(isset($records))
                    {{ $records->links() }}
                @endif
            </div>
        </div>

        <!-- Modal Upload File Excel / PDF -->
        <div x-show="openUploadModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden" @click.away="openUploadModal = false">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Input Data Pasang Surut (Excel / PDF)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Unggah file Excel/PDF untuk diekstrak otomatis oleh parser Python.</p>
                    </div>
                    <button @click="openUploadModal = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <form action="{{ route('admin.data.upload') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Lokasi Pantai</label>
                        <select name="station_id" class="w-full border border-slate-300 rounded-xl p-3 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none" required>
                            <option value="">-- Pilih Lokasi --</option>
                            @if(isset($stations))
                                @foreach ($stations as $station)
                                    <option value="{{ $station->id }}">{{ $station->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Upload File Data (.xlsx, .csv, .pdf)</label>
                        <div class="border-2 border-dashed border-slate-300 hover:border-blue-500 bg-slate-50 rounded-xl p-6 text-center transition cursor-pointer">
                            <input type="file" name="file_data" accept=".xlsx,.xls,.csv,.pdf" required class="hidden" id="file_data_input"
                                onchange="document.getElementById('file_name_display').innerText = this.files[0] ? this.files[0].name : 'Pilih file Excel / PDF'">
                            <label for="file_data_input" class="cursor-pointer space-y-2 block">
                                <svg class="w-10 h-10 text-slate-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <span id="file_name_display" class="block text-xs font-semibold text-[#1D61E7]">Klik untuk memilih file Excel / PDF</span>
                                <span class="block text-[11px] text-slate-400">Ukuran file maksimal: 10 MB</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="openUploadModal = false" class="px-4 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 text-xs font-semibold text-white bg-[#1D61E7] rounded-xl hover:bg-blue-700 transition shadow">
                            Proses & Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>