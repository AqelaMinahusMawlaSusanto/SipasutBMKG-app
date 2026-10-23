<x-app-layout>
    <div class="bg-[#F8FAFC] min-h-screen font-sans" style="background-color: #F8FAFC;">
        <div class="max-w-7xl mx-auto px-6 py-8 space-y-6" style="padding-top: 32px;">

            <!-- Title Section -->
            <div style="margin-bottom: 24px;">
                <h1 class="text-3xl font-bold text-[#0F172A] tracking-tight" style="color: #0F172A; font-weight: 700; font-size: 26px; margin: 0 0 4px;">Log Aktivitas</h1>
                <p class="text-sm font-medium text-slate-500 mt-1" style="color: #64748B; font-size: 13px; margin: 0;">Menampilkan riwayat aktivitas pengguna dalam sistem monitoring pasang surut BMKG</p>
            </div>

            <!-- Flash Alert Notification -->
            @if(session('success'))
                <div style="background-color: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 12px 18px; border-radius: 12px; font-size: 13px; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <span>✔ {{ session('success') }}</span>
                </div>
            @endif

            <!-- 4 Stat Cards Mendatar Kesamping (4 Kolom 1 Baris Sejajar) -->
            <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 24px;">
                
                <!-- Card 1: Total Aktivitas -->
                <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="width: 52px; height: 52px; background-color: #E0ECFF; color: #1D61E7; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div>
                        <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">Total Aktivitas</div>
                        <div style="font-size: 28px; font-weight: 900; color: #0F172A; line-height: 1.2;">{{ $totalAktivitas }}</div>
                        <div style="font-size: 12px; font-weight: 600; color: #64748B;">Aktivitas</div>
                    </div>
                </div>

                <!-- Card 2: Login Berhasil -->
                <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="width: 52px; height: 52px; background-color: #ECFDF5; color: #059669; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">Login Berhasil</div>
                        <div style="font-size: 28px; font-weight: 900; color: #059669; line-height: 1.2;">{{ $loginBerhasil }}</div>
                        <div style="font-size: 12px; font-weight: 600; color: #059669;">Aktivitas</div>
                    </div>
                </div>

                <!-- Card 3: Login Gagal -->
                <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="width: 52px; height: 52px; background-color: #FEF2F2; color: #DC2626; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">Login Gagal</div>
                        <div style="font-size: 28px; font-weight: 900; color: #DC2626; line-height: 1.2;">{{ $loginGagal }}</div>
                        <div style="font-size: 12px; font-weight: 600; color: #DC2626;">Aktivitas</div>
                    </div>
                </div>

                <!-- Card 4: Aktivitas Lainnya -->
                <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="width: 52px; height: 52px; background-color: #F8FAFC; color: #475569; border: 1px solid #E2E8F0; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">Aktivitas Lainnya</div>
                        <div style="font-size: 28px; font-weight: 900; color: #0F172A; line-height: 1.2;">{{ $aktivitasLainnya }}</div>
                        <div style="font-size: 12px; font-weight: 600; color: #64748B;">Aktivitas</div>
                    </div>
                </div>

            </div>

            <!-- Filter Bar Horizontal (1 Baris Kesamping) -->
            <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px;">
                <form action="{{ route('admin.aktivitas') }}" method="GET" style="display: flex; flex-wrap: wrap; align-items: flex-end; gap: 12px; width: 100%;">
                    
                    <!-- Cari Aktivitas (Select) -->
                    <div style="flex: 1.5; min-width: 170px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 6px;">Jenis Aktivitas</label>
                        <select name="action" style="width: 100%; border: 1px solid #CBD5E1; border-radius: 10px; padding: 10px 12px; font-size: 13px; font-weight: 500; color: #334155; background-color: #FFFFFF; outline: none;">
                            <option value="">Semua Aktivitas</option>
                            <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>🔑 Login & Autentikasi</option>
                            <option value="data" {{ request('action') === 'data' ? 'selected' : '' }}>📊 Data Pasang Surut</option>
                            <option value="lokasi" {{ request('action') === 'lokasi' ? 'selected' : '' }}>📍 Lokasi Monitoring</option>
                            <option value="notifikasi" {{ request('action') === 'notifikasi' ? 'selected' : '' }}>🔔 Notifikasi & Peringatan</option>
                            <option value="profil" {{ request('action') === 'profil' ? 'selected' : '' }}>👤 Profil Admin</option>
                            @if(isset($actions))
                                <optgroup label="Aksi Spesifik">
                                    @foreach($actions as $act)
                                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                    </div>

                    <!-- Tanggal -->
                    <div style="flex: 1; min-width: 150px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 6px;">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}" 
                            style="width: 100%; border: 1px solid #CBD5E1; border-radius: 10px; padding: 10px 12px; font-size: 13px; font-weight: 500; color: #334155; background-color: #FFFFFF; outline: none; box-sizing: border-box;">
                    </div>

                    <!-- Status Kondisi -->
                    <div style="flex: 1; min-width: 140px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 6px;">Status Kondisi</label>
                        <select name="status" style="width: 100%; border: 1px solid #CBD5E1; border-radius: 10px; padding: 10px 12px; font-size: 13px; font-weight: 500; color: #334155; background-color: #FFFFFF; outline: none;">
                            <option value="">Semua Kondisi</option>
                            <option value="Sukses" {{ request('status') === 'Sukses' ? 'selected' : '' }}>Sukses</option>
                            <option value="Gagal" {{ request('status') === 'Gagal' ? 'selected' : '' }}>Gagal</option>
                        </select>
                    </div>

                    <!-- Urutkan -->
                    <div style="flex: 1; min-width: 130px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 6px;">Urutkan</label>
                        <select name="order" style="width: 100%; border: 1px solid #CBD5E1; border-radius: 10px; padding: 10px 12px; font-size: 13px; font-weight: 500; color: #334155; background-color: #FFFFFF; outline: none;">
                            <option value="latest" {{ request('order', 'latest') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('order') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                        </select>
                    </div>

                    <!-- Kata Kunci Pencarian -->
                    <div style="flex: 1.5; min-width: 180px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 6px;">Cari Deskripsi / User / IP</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari teks log..."
                            style="width: 100%; border: 1px solid #CBD5E1; border-radius: 10px; padding: 10px 12px; font-size: 13px; font-weight: 500; color: #334155; background-color: #FFFFFF; outline: none; box-sizing: border-box;">
                    </div>

                    <!-- Tombol Cari -->
                    <div>
                        <button type="submit" 
                            style="border: 1.5px solid #93C5FD; background-color: #EFF6FF; color: #1D4ED8; font-weight: 700; font-size: 13px; padding: 10px 20px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; white-space: nowrap;">
                            🔍 Cari
                        </button>
                    </div>

                    <!-- Tombol Reset -->
                    <div>
                        <a href="{{ route('admin.aktivitas') }}" 
                            style="border: 1.5px solid #CBD5E1; background-color: #F8FAFC; color: #64748B; font-weight: 700; font-size: 13px; padding: 10px 20px; border-radius: 10px; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px; white-space: nowrap;">
                            ↺ Reset
                        </a>
                    </div>

                </form>
            </div>

            <!-- Tabel Log Aktivitas -->
            <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); margin-bottom: 24px;">
                <div style="padding: 16px 20px; border-bottom: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="font-size: 15px; font-weight: 700; color: #0F172A;">Daftar Riwayat Aktivitas</span>
                        <span style="margin-left: 10px; font-size: 12px; color: #94A3B8;">({{ $logs->total() }} aktivitas tercatat)</span>
                    </div>
                    <span style="font-size: 12px; color: #94A3B8;">
                        Halaman {{ $logs->currentPage() }} dari {{ $logs->lastPage() }}
                    </span>
                </div>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; text-align: left; border-collapse: collapse; font-size: 13.5px;">
                        <thead style="background-color: #D7E5F9; color: #0F172A; font-weight: 700; font-size: 13px;">
                            <tr>
                                <th style="padding: 14px 18px; width: 60px;">No</th>
                                <th style="padding: 14px 18px;">Jenis Aktivitas</th>
                                <th style="padding: 14px 18px;">Pengguna</th>
                                <th style="padding: 14px 18px;">Waktu & Tanggal</th>
                                <th style="padding: 14px 18px;">Detail Aktivitas</th>
                                <th style="padding: 14px 18px;">IP Address</th>
                                <th style="padding: 14px 18px; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody style="font-weight: 500; color: #334155;">
                            @forelse($logs as $index => $log)
                                @php
                                    $rowNum = ($logs->currentPage() - 1) * $logs->perPage() + $index + 1;
                                    
                                    // Formatting label jenis aktivitas
                                    $actionLabels = [
                                        'login' => ['text' => 'Login Masuk', 'bg' => '#ECFDF5', 'color' => '#059669'],
                                        'failed_login' => ['text' => 'Login Gagal', 'bg' => '#FEF2F2', 'color' => '#DC2626'],
                                        'logout' => ['text' => 'Logout Keluar', 'bg' => '#F1F5F9', 'color' => '#475569'],
                                        'create_prediction' => ['text' => 'Tambah Prediksi', 'bg' => '#EFF6FF', 'color' => '#1D4ED8'],
                                        'update_prediction' => ['text' => 'Edit Prediksi', 'bg' => '#FFFBEB', 'color' => '#D97706'],
                                        'delete_prediction' => ['text' => 'Hapus Prediksi', 'bg' => '#FEF2F2', 'color' => '#DC2626'],
                                        'upload_data' => ['text' => 'Import Berkas', 'bg' => '#F0FDF4', 'color' => '#16A34A'],
                                        'delete_upload' => ['text' => 'Hapus Berkas', 'bg' => '#FEF2F2', 'color' => '#DC2626'],
                                        'create_location' => ['text' => 'Tambah Lokasi', 'bg' => '#EFF6FF', 'color' => '#1D4ED8'],
                                        'update_location' => ['text' => 'Edit Lokasi', 'bg' => '#FFFBEB', 'color' => '#D97706'],
                                        'delete_location' => ['text' => 'Hapus Lokasi', 'bg' => '#FEF2F2', 'color' => '#DC2626'],
                                        'create_notification' => ['text' => 'Buat Notifikasi', 'bg' => '#FAF5FF', 'color' => '#9333EA'],
                                        'toggle_notification' => ['text' => 'Status Notif', 'bg' => '#F1F5F9', 'color' => '#475569'],
                                        'delete_notification' => ['text' => 'Hapus Notif', 'bg' => '#FEF2F2', 'color' => '#DC2626'],
                                        'update_profile' => ['text' => 'Edit Profil', 'bg' => '#F8FAFC', 'color' => '#0284C7'],
                                        'register' => ['text' => 'Daftar Akun', 'bg' => '#ECFDF5', 'color' => '#059669'],
                                    ];

                                    $badgeInfo = $actionLabels[$log->action] ?? ['text' => ucwords(str_replace('_', ' ', $log->action)), 'bg' => '#F1F5F9', 'color' => '#334155'];

                                    $isFailed = str_contains(strtolower($log->action), 'fail') || str_contains(strtolower($log->action), 'gagal') || str_contains(strtolower($log->description ?? ''), 'gagal');
                                @endphp
                                <tr style="border-top: 1px solid #F1F5F9;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 14px 18px; color: #94A3B8; font-size: 13px;">{{ $rowNum }}</td>
                                    <td style="padding: 14px 18px;">
                                        <span style="background-color: {{ $badgeInfo['bg'] }}; color: {{ $badgeInfo['color'] }}; font-weight: 700; font-size: 11.5px; padding: 4px 10px; border-radius: 6px; white-space: nowrap;">
                                            {{ $badgeInfo['text'] }}
                                        </span>
                                    </td>
                                    <td style="padding: 14px 18px;">
                                        <div style="font-weight: 700; color: #0F172A;">{{ $log->user_name ?? ($log->user->name ?? 'Guest') }}</div>
                                        @if($log->user_email)
                                            <div style="font-size: 11.5px; color: #94A3B8;">{{ $log->user_email }}</div>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 18px; white-space: nowrap;">
                                        <div style="font-weight: 600; color: #334155;">{{ $log->created_at->format('d M Y') }}</div>
                                        <div style="font-size: 11.5px; color: #94A3B8;">{{ $log->created_at->format('H:i:s') }} WIB</div>
                                    </td>
                                    <td style="padding: 14px 18px; color: #334155; max-width: 320px;">
                                        {{ $log->description ?? '-' }}
                                    </td>
                                    <td style="padding: 14px 18px; font-family: monospace; font-size: 12.5px; color: #64748B;">
                                        {{ $log->ip_address ?? '127.0.0.1' }}
                                    </td>
                                    <td style="padding: 14px 18px; text-align: center;">
                                        @if($isFailed)
                                            <span style="background-color: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; padding: 4px 14px; border-radius: 9999px; font-size: 11.5px; font-weight: 700;">
                                                Gagal
                                            </span>
                                        @else
                                            <span style="background-color: #EFF6FF; color: #1D4ED8; border: 1px solid #93C5FD; padding: 4px 14px; border-radius: 9999px; font-size: 11.5px; font-weight: 700;">
                                                Sukses
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="padding: 48px 20px; text-align: center; color: #94A3B8;">
                                        <div style="font-size: 32px; margin-bottom: 8px;">📋</div>
                                        Tidak ada catatan aktivitas yang sesuai dengan filter pencarian.<br>
                                        <a href="{{ route('admin.aktivitas') }}" style="color: #1D4ED8; font-size: 12px; font-weight: 600; text-decoration: underline; margin-top: 6px; display: inline-block;">Reset Filter</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div style="padding: 16px 20px; border-top: 1px solid #F1F5F9;">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>