<x-app-layout>
    <div class="bg-[#F8FAFC] min-h-screen font-sans" style="background-color: #F8FAFC;">
        <div class="max-w-7xl mx-auto px-6 py-8 space-y-6" style="padding-top: 32px;">

            <!-- Title & Tombol Tambah Notifikasi -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                <div>
                    <h1 class="text-3xl font-bold text-[#0F172A] tracking-tight" style="color: #0F172A; font-weight: 700; font-size: 26px; margin: 0 0 4px;">Kelola Notifikasi</h1>
                    <p class="text-sm font-medium text-slate-500 mt-1" style="color: #64748B; font-size: 13px; margin: 0;">Mengelola informasi dan peringatan yang ditampilkan kepada pengguna website monitoring</p>
                </div>
                
                <!-- Tombol Tambah Notifikasi Biru Solid Figma -->
                <button onclick="openModal('modalTambahNotif')"
                    style="background-color: #1D61E7; color: #FFFFFF; font-weight: 700; padding: 12px 24px; border-radius: 12px; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; box-shadow: 0 2px 8px rgba(29, 97, 231, 0.25);">
                    <span style="font-size: 18px; font-weight: 800; line-height: 1;">+</span>
                    <span style="font-size: 14px; font-weight: 700;">Tambah Notifikasi</span>
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
                        <div style="margin-bottom: 2px;">❌ {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- 4 Stat Cards Mendatar Kesamping (4 Kolom 1 Baris Sejajar) -->
            <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 24px;">
                
                <!-- Card 1: Total Notifikasi -->
                <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="width: 52px; height: 52px; background-color: #E0ECFF; color: #1D61E7; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <div>
                        <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">Total Notifikasi</div>
                        <div style="font-size: 28px; font-weight: 900; color: #0F172A; line-height: 1.2;">{{ $totalNotifikasi }}</div>
                        <div style="font-size: 12px; font-weight: 600; color: #64748B;">Pada Sistem</div>
                    </div>
                </div>

                <!-- Card 2: Peringatan Aktif -->
                <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="width: 52px; height: 52px; background-color: #FEF2F2; color: #DC2626; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">Peringatan Aktif</div>
                        <div style="font-size: 28px; font-weight: 900; color: #DC2626; line-height: 1.2;">{{ $peringatanAktif }}</div>
                        <div style="font-size: 12px; font-weight: 600; color: #DC2626;">Perlu Pemantauan</div>
                    </div>
                </div>

                <!-- Card 3: Informasi Aktif -->
                <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="width: 52px; height: 52px; background-color: #EFF6FF; color: #1D4ED8; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">Informasi Aktif</div>
                        <div style="font-size: 28px; font-weight: 900; color: #1D4ED8; line-height: 1.2;">{{ $informasiAktif }}</div>
                        <div style="font-size: 12px; font-weight: 600; color: #1D4ED8;">Pemberitahuan</div>
                    </div>
                </div>

                <!-- Card 4: Notifikasi Terkirim -->
                <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="width: 52px; height: 52px; background-color: #ECFDF5; color: #059669; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">Notifikasi Aktif</div>
                        <div style="font-size: 28px; font-weight: 900; color: #059669; line-height: 1.2;">{{ $notifikasiTerkirim }}</div>
                        <div style="font-size: 12px; font-weight: 600; color: #059669;">Tampil di Web Publik</div>
                    </div>
                </div>

            </div>

            <!-- Tab Category Filter & Input Pencarian (Sejajar Horizontal Sesuai Figma) -->
            <div style="display: flex; flex-direction: row; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 24px;">
                
                <!-- Tab Kategori Pill (Kiri) -->
                <div style="display: flex; align-items: center; gap: 10px;">
                    <a href="{{ route('admin.notif', ['tab' => 'semua', 'search' => request('search')]) }}"
                        style="padding: 10px 22px; border-radius: 9999px; font-size: 13.5px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; {{ $tab === 'semua' ? 'background-color: #93C5FD; color: #1E3A8A; font-weight: 700;' : 'background-color: #FFFFFF; color: #334155; border: 1px solid #CBD5E1; font-weight: 600;' }}">
                        Semua ({{ $countSemua }})
                    </a>
                    <a href="{{ route('admin.notif', ['tab' => 'peringatan', 'search' => request('search')]) }}"
                        style="padding: 10px 22px; border-radius: 9999px; font-size: 13.5px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; {{ $tab === 'peringatan' ? 'background-color: #FEF2F2; color: #DC2626; font-weight: 700; border: 1px solid #FCA5A5;' : 'background-color: #FFFFFF; color: #334155; border: 1px solid #CBD5E1; font-weight: 600;' }}">
                        ⚠️ Peringatan ({{ $countPeringatan }})
                    </a>
                    <a href="{{ route('admin.notif', ['tab' => 'informasi', 'search' => request('search')]) }}"
                        style="padding: 10px 22px; border-radius: 9999px; font-size: 13.5px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; {{ $tab === 'informasi' ? 'background-color: #EFF6FF; color: #1D4ED8; font-weight: 700; border: 1px solid #93C5FD;' : 'background-color: #FFFFFF; color: #334155; border: 1px solid #CBD5E1; font-weight: 600;' }}">
                        ℹ️ Informasi ({{ $countInformasi }})
                    </a>
                </div>

                <!-- Cari, Urutkan, & Reset (Kanan) -->
                <form action="{{ route('admin.notif') }}" method="GET" style="display: flex; align-items: center; gap: 10px; flex: 1; max-width: 600px; justify-content: flex-end;">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    
                    <!-- Input Search -->
                    <div style="position: relative; flex: 1;">
                        <span style="position: absolute; top: 50%; transform: translateY(-50%); left: 12px; color: #94A3B8; font-size: 14px;">
                            🔍
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau isi notifikasi..." 
                            style="width: 100%; border: 1px solid #CBD5E1; border-radius: 10px; padding: 10px 14px 10px 36px; font-size: 13px; font-weight: 500; color: #334155; background-color: #FFFFFF; outline: none; box-sizing: border-box;">
                    </div>

                    <!-- Dropdown Urutkan -->
                    <div style="width: 140px;">
                        <select name="order" style="width: 100%; border: 1px solid #CBD5E1; border-radius: 10px; padding: 10px 12px; font-size: 13px; font-weight: 500; color: #334155; background-color: #FFFFFF; outline: none;">
                            <option value="latest" {{ request('order', 'latest') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('order') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                        </select>
                    </div>

                    <!-- Tombol Cari -->
                    <button type="submit" 
                        style="border: 1.5px solid #93C5FD; background-color: #EFF6FF; color: #1D4ED8; font-weight: 700; font-size: 13px; padding: 10px 16px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 4px; white-space: nowrap;">
                        🔍 Cari
                    </button>

                    @if(request('search') || request('tab') !== 'semua' || request('order'))
                        <a href="{{ route('admin.notif') }}" 
                            style="border: 1.5px solid #CBD5E1; background-color: #F8FAFC; color: #64748B; font-weight: 700; font-size: 13px; padding: 10px 14px; border-radius: 10px; text-decoration: none; display: flex; align-items: center; gap: 4px; white-space: nowrap;">
                            ↺ Reset
                        </a>
                    @endif
                </form>

            </div>

            <!-- Tabel Kelola Notifikasi (Sesuai Presisi Gambar Figma) -->
            <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); margin-bottom: 24px;">
                <div style="padding: 16px 20px; border-bottom: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="font-size: 15px; font-weight: 700; color: #0F172A;">Daftar Notifikasi & Peringatan</span>
                        <span style="margin-left: 10px; font-size: 12px; color: #94A3B8;">({{ $notifications->total() }} data)</span>
                    </div>
                    <span style="font-size: 12px; color: #94A3B8;">
                        Halaman {{ $notifications->currentPage() }} dari {{ $notifications->lastPage() }}
                    </span>
                </div>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; text-align: left; border-collapse: collapse; font-size: 13.5px;">
                        <!-- Header Tabel Biru Muda Figma BMKG -->
                        <thead style="background-color: #D7E5F9; color: #0F172A; font-weight: 700; font-size: 13px;">
                            <tr>
                                <th style="padding: 14px 18px; width: 60px;">No</th>
                                <th style="padding: 14px 18px;">Judul Notifikasi</th>
                                <th style="padding: 14px 18px;">Kategori</th>
                                <th style="padding: 14px 18px;">Lokasi Terkait</th>
                                <th style="padding: 14px 18px; text-align: center;">Status</th>
                                <th style="padding: 14px 18px;">Tanggal Dibuat</th>
                                <th style="padding: 14px 18px; text-align: center; width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody style="font-weight: 500; color: #334155;">
                            @forelse($notifications as $index => $item)
                                @php
                                    $rowNum = ($notifications->currentPage() - 1) * $notifications->perPage() + $index + 1;
                                    $isWarning = in_array(strtolower($item->type), ['peringatan', 'warning', 'danger']);
                                @endphp
                                <tr style="border-top: 1px solid #F1F5F9;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 14px 18px; color: #64748B;">{{ $rowNum }}</td>
                                    <td style="padding: 14px 18px; max-width: 280px;">
                                        <div style="font-weight: 700; color: #0F172A;">{{ $item->title }}</div>
                                        <div style="font-size: 12px; color: #64748B; margin-top: 2px;">{{ Str::limit($item->message, 45) }}</div>
                                    </td>
                                    <td style="padding: 14px 18px;">
                                        @if($isWarning)
                                            <span style="background-color: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 700;">
                                                ⚠️ Peringatan
                                            </span>
                                        @else
                                            <span style="background-color: #EFF6FF; color: #1D4ED8; border: 1px solid #93C5FD; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 700;">
                                                ℹ️ Informasi
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 18px; font-weight: 600; color: #0369A1;">
                                        {{ $item->location->name ?? 'Semua Lokasi' }}
                                    </td>
                                    <td style="padding: 14px 18px; text-align: center;">
                                        <form action="{{ route('admin.notif.toggle', $item->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" title="Klik untuk mengubah status"
                                                style="background: transparent; border: none; cursor: pointer; padding: 0;">
                                                @if($item->is_active)
                                                    <span style="background-color: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; padding: 4px 12px; border-radius: 9999px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                                        ● Aktif
                                                    </span>
                                                @else
                                                    <span style="background-color: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; padding: 4px 12px; border-radius: 9999px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                                        ○ Non-Aktif
                                                    </span>
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                    <td style="padding: 14px 18px;">
                                        <div style="font-weight: 600; color: #334155;">{{ $item->created_at->format('d M Y') }}</div>
                                        <div style="font-size: 11.5px; color: #94A3B8;">{{ $item->created_at->format('H:i') }} WIB</div>
                                    </td>
                                    <td style="padding: 14px 18px; text-align: center;">
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                            {{-- Detail Button --}}
                                            <button type="button" class="btn-detail-notif"
                                                style="border: 1px solid #93C5FD; background-color: #EFF6FF; color: #1D4ED8; padding: 6px 10px; border-radius: 8px; cursor: pointer;"
                                                title="Detail"
                                                data-title="{{ $item->title }}"
                                                data-type="{{ $isWarning ? 'Peringatan' : 'Informasi' }}"
                                                data-location="{{ $item->location->name ?? 'Semua Lokasi Monitoring' }}"
                                                data-message="{{ $item->message }}"
                                                data-status="{{ $item->is_active ? 'Aktif' : 'Non-Aktif' }}"
                                                data-date="{{ $item->created_at->format('d M Y H:i') }} WIB">
                                                👁️
                                            </button>

                                            {{-- Edit Button --}}
                                            <button type="button" class="btn-edit-notif"
                                                style="border: 1px solid #FDE68A; background-color: #FEF3C7; color: #D97706; padding: 6px 10px; border-radius: 8px; cursor: pointer;"
                                                title="Edit"
                                                data-id="{{ $item->id }}"
                                                data-title="{{ $item->title }}"
                                                data-type="{{ $isWarning ? 'Peringatan' : 'Informasi' }}"
                                                data-location-id="{{ $item->location_id }}"
                                                data-message="{{ $item->message }}"
                                                data-active="{{ $item->is_active ? '1' : '0' }}">
                                                ✏️
                                            </button>

                                            {{-- Delete Button --}}
                                            <form action="{{ route('admin.notif.delete', $item->id) }}" method="POST"
                                                  class="form-delete-notif"
                                                  data-title="{{ $item->title }}">
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
                                    <td colspan="7" style="padding: 48px 20px; text-align: center; color: #94A3B8;">
                                        <div style="font-size: 32px; margin-bottom: 8px;">🔔</div>
                                        Tidak ada notifikasi atau peringatan yang ditemukan.<br>
                                        <button onclick="openModal('modalTambahNotif')" style="background: none; border: none; color: #1D61E7; font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: underline; margin-top: 6px;">
                                            + Tambah Notifikasi Baru
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($notifications->hasPages())
                    <div style="padding: 16px 20px; border-top: 1px solid #F1F5F9;">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>

        </div>

        {{-- ================================================================
             MODAL: TAMBAH NOTIFIKASI
             ================================================================ --}}
        <div id="modalTambahNotif"
            style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.55);z-index:9000;align-items:center;justify-content:center;padding:20px;">
            <div style="background:#fff;border-radius:20px;width:100%;max-width:520px;box-shadow:0 25px 60px rgba(0,0,0,0.2);overflow:hidden;">
                <div style="padding:20px 24px;border-bottom:1px solid #F1F5F9;display:flex;align-items:center;justify-content:space-between;background:#F8FAFC;">
                    <h3 style="margin:0;font-size:16px;font-weight:700;color:#0F172A;">➕ Tambah Notifikasi Baru</h3>
                    <button onclick="closeModal('modalTambahNotif')" style="background:#F1F5F9;border:none;border-radius:8px;width:32px;height:32px;font-size:16px;cursor:pointer;color:#64748B;display:flex;align-items:center;justify-content:center;">✕</button>
                </div>
                <form action="{{ route('admin.notif.store') }}" method="POST" style="padding:24px;">
                    @csrf
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Judul Notifikasi <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="title" required placeholder="Contoh: Pasang Maksimum Waspada"
                            style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Kategori <span style="color:#ef4444;">*</span></label>
                            <select name="type" required
                                style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;background:#fff;">
                                <option value="Informasi">ℹ️ Informasi</option>
                                <option value="Peringatan">⚠️ Peringatan</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Lokasi Monitoring</label>
                            <select name="location_id"
                                style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;background:#fff;">
                                <option value="">Semua Lokasi</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Isi Pesan Notifikasi <span style="color:#ef4444;">*</span></label>
                        <textarea name="message" rows="3" required placeholder="Tuliskan pesan detail peringatan atau informasi..."
                            style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;resize:vertical;"></textarea>
                    </div>
                    <div style="margin-bottom:20px;">
                        <label style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#374151;cursor:pointer;">
                            <input type="checkbox" name="is_active" value="1" checked style="width:16px;height:16px;">
                            <span>Aktifkan langsung di web publik</span>
                        </label>
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:16px;border-top:1px solid #F1F5F9;">
                        <button type="button" onclick="closeModal('modalTambahNotif')"
                            style="padding:10px 20px;font-size:13px;font-weight:600;color:#64748B;background:#F1F5F9;border:none;border-radius:10px;cursor:pointer;">Batal</button>
                        <button type="submit"
                            style="padding:10px 22px;font-size:13px;font-weight:700;color:#fff;background:#1D61E7;border:none;border-radius:10px;cursor:pointer;box-shadow:0 2px 8px rgba(29,97,231,0.25);">Simpan & Terbitkan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================================================================
             MODAL: EDIT NOTIFIKASI
             ================================================================ --}}
        <div id="modalEditNotif"
            style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.55);z-index:9000;align-items:center;justify-content:center;padding:20px;">
            <div style="background:#fff;border-radius:20px;width:100%;max-width:520px;box-shadow:0 25px 60px rgba(0,0,0,0.2);overflow:hidden;">
                <div style="padding:20px 24px;border-bottom:1px solid #F1F5F9;display:flex;align-items:center;justify-content:space-between;background:#FFFBEB;">
                    <h3 style="margin:0;font-size:16px;font-weight:700;color:#0F172A;">✏️ Edit Notifikasi</h3>
                    <button onclick="closeModal('modalEditNotif')" style="background:#F1F5F9;border:none;border-radius:8px;width:32px;height:32px;font-size:16px;cursor:pointer;color:#64748B;display:flex;align-items:center;justify-content:center;">✕</button>
                </div>
                <form id="formEditNotif" method="POST" style="padding:24px;">
                    @csrf
                    @method('PUT')
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Judul Notifikasi <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="title" id="editNotifTitle" required
                            style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Kategori <span style="color:#ef4444;">*</span></label>
                            <select name="type" id="editNotifType" required
                                style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;background:#fff;">
                                <option value="Informasi">ℹ️ Informasi</option>
                                <option value="Peringatan">⚠️ Peringatan</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Lokasi Monitoring</label>
                            <select name="location_id" id="editNotifLocationId"
                                style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;background:#fff;">
                                <option value="">Semua Lokasi</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Isi Pesan Notifikasi <span style="color:#ef4444;">*</span></label>
                        <textarea name="message" id="editNotifMessage" rows="3" required
                            style="width:100%;border:1px solid #CBD5E1;border-radius:10px;padding:11px 14px;font-size:13px;color:#334155;outline:none;box-sizing:border-box;resize:vertical;"></textarea>
                    </div>
                    <div style="margin-bottom:20px;">
                        <label style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#374151;cursor:pointer;">
                            <input type="checkbox" name="is_active" id="editNotifActive" value="1" style="width:16px;height:16px;">
                            <span>Status Aktif di Web</span>
                        </label>
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:16px;border-top:1px solid #F1F5F9;">
                        <button type="button" onclick="closeModal('modalEditNotif')"
                            style="padding:10px 20px;font-size:13px;font-weight:600;color:#64748B;background:#F1F5F9;border:none;border-radius:10px;cursor:pointer;">Batal</button>
                        <button type="submit"
                            style="padding:10px 22px;font-size:13px;font-weight:700;color:#fff;background:#D97706;border:none;border-radius:10px;cursor:pointer;box-shadow:0 2px 8px rgba(217,119,6,0.25);">Update Notifikasi</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================================================================
             MODAL: DETAIL NOTIFIKASI
             ================================================================ --}}
        <div id="modalDetailNotif"
            style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.55);z-index:9000;align-items:center;justify-content:center;padding:20px;">
            <div style="background:#fff;border-radius:20px;width:100%;max-width:500px;box-shadow:0 25px 60px rgba(0,0,0,0.2);overflow:hidden;">
                <div style="padding:20px 24px;border-bottom:1px solid #F1F5F9;display:flex;align-items:center;justify-content:space-between;background:#EFF6FF;">
                    <h3 style="margin:0;font-size:16px;font-weight:700;color:#0F172A;">Detail Notifikasi</h3>
                    <button onclick="closeModal('modalDetailNotif')" style="background:#F1F5F9;border:none;border-radius:8px;width:32px;height:32px;font-size:16px;cursor:pointer;color:#64748B;display:flex;align-items:center;justify-content:center;">✕</button>
                </div>
                <div id="detailNotifContent" style="padding:24px;">
                    {{-- Diisi via JavaScript --}}
                </div>
                <div style="padding:16px 24px;border-top:1px solid #F1F5F9;text-align:right;">
                    <button onclick="closeModal('modalDetailNotif')"
                        style="padding:10px 22px;font-size:13px;font-weight:600;color:#64748B;background:#F1F5F9;border:none;border-radius:10px;cursor:pointer;">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- JavaScript Modal & Event Delegation --}}
    <script>
        function openModal(id) {
            var el = document.getElementById(id);
            if (el) el.style.display = 'flex';
        }
        function closeModal(id) {
            var el = document.getElementById(id);
            if (el) el.style.display = 'none';
        }

        ['modalTambahNotif','modalEditNotif','modalDetailNotif'].forEach(function(id) {
            var modal = document.getElementById(id);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) closeModal(id);
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Edit Notif
            document.querySelectorAll('.btn-edit-notif').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var baseUrl = "{{ url('admin/notifikasi') }}/" + this.dataset.id;
                    document.getElementById('formEditNotif').action = baseUrl;
                    document.getElementById('editNotifTitle').value = this.dataset.title;
                    document.getElementById('editNotifType').value = this.dataset.type;
                    document.getElementById('editNotifLocationId').value = this.dataset.locationId || '';
                    document.getElementById('editNotifMessage').value = this.dataset.message;
                    document.getElementById('editNotifActive').checked = (this.dataset.active === '1');
                    openModal('modalEditNotif');
                });
            });

            // Detail Notif
            document.querySelectorAll('.btn-detail-notif').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var isWarn = (this.dataset.type === 'Peringatan');
                    var categoryBadge = isWarn
                        ? '<span style="background-color:#FEF2F2;color:#DC2626;border:1px solid #FCA5A5;padding:3px 12px;border-radius:8px;font-size:12px;font-weight:700;">⚠️ Peringatan</span>'
                        : '<span style="background-color:#EFF6FF;color:#1D4ED8;border:1px solid #93C5FD;padding:3px 12px;border-radius:8px;font-size:12px;font-weight:700;">ℹ️ Informasi</span>';

                    var statusBadge = this.dataset.status === 'Aktif'
                        ? '<span style="background-color:#ECFDF5;color:#059669;border:1px solid #A7F3D0;padding:3px 12px;border-radius:9999px;font-size:12px;font-weight:700;">● Aktif</span>'
                        : '<span style="background-color:#FEF2F2;color:#DC2626;border:1px solid #FECACA;padding:3px 12px;border-radius:9999px;font-size:12px;font-weight:700;">○ Non-Aktif</span>';

                    var html = `
                        <table style="width:100%;font-size:13.5px;border-collapse:collapse;">
                            <tr style="border-bottom:1px solid #F1F5F9;">
                                <td style="padding:10px 8px;color:#64748B;font-weight:700;width:130px;">Judul</td>
                                <td style="padding:10px 8px;font-weight:700;color:#0F172A;">${this.dataset.title}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #F1F5F9;">
                                <td style="padding:10px 8px;color:#64748B;font-weight:700;">Kategori</td>
                                <td style="padding:10px 8px;">${categoryBadge}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #F1F5F9;">
                                <td style="padding:10px 8px;color:#64748B;font-weight:700;">Lokasi Terkait</td>
                                <td style="padding:10px 8px;font-weight:600;color:#0369A1;">${this.dataset.location}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #F1F5F9;">
                                <td style="padding:10px 8px;color:#64748B;font-weight:700;">Status</td>
                                <td style="padding:10px 8px;">${statusBadge}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #F1F5F9;">
                                <td style="padding:10px 8px;color:#64748B;font-weight:700;">Tanggal Dibuat</td>
                                <td style="padding:10px 8px;color:#475569;">${this.dataset.date}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 8px;color:#64748B;font-weight:700;vertical-align:top;">Isi Pesan</td>
                                <td style="padding:10px 8px;color:#1E293B;line-height:1.5;">${this.dataset.message}</td>
                            </tr>
                        </table>
                    `;
                    document.getElementById('detailNotifContent').innerHTML = html;
                    openModal('modalDetailNotif');
                });
            });

            // Delete Notif
            document.querySelectorAll('.form-delete-notif').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    var title = this.dataset.title || 'notifikasi ini';
                    if (!confirm('Apakah Anda yakin ingin menghapus notifikasi "' + title + '"?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</x-app-layout>