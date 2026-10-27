<x-app-layout>
    <div class="bg-[#F8FAFC] min-h-screen font-sans" style="background-color: #F8FAFC;">
    <div class="max-w-7xl mx-auto px-6 py-8 space-y-6" style="padding-top: 32px;">

        <!-- Title Section -->
        <div>
            <h1 class="text-3xl font-bold text-[#0F172A] tracking-tight" style="color: #0F172A; font-weight: 700;">Profil Admin</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Kelola informasi profil dan keamanan akun anda</p>
        </div>

        <!-- Flash Alert Notification -->
        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-sm mb-4">
                <span>✔ {{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs space-y-1 shadow-sm mb-4">
                @foreach($errors->all() as $error)
                    <div>❌ {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- Layout Grid 2 Kolom -->
        <div style="display: grid; grid-template-columns: 360px 1fr; gap: 24px; align-items: start;">

            <!-- ================= KARTU KIRI: INFORMASI PROFIL ================= -->
            <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 28px; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 24px;">
                
                <h2 style="font-size: 16px; font-weight: 700; color: #0F172A; margin: 0;">Informasi Profil</h2>

                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                    <div style="width: 110px; height: 110px; background-color: #D1D5DB; border-radius: 9999px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <svg style="width: 70px; height: 70px; color: #000000;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>

                    <div style="font-size: 18px; font-weight: 800; color: #0F172A; margin-top: 4px;">{{ auth()->user()->name ?? 'Admin BMKG' }}</div>

                    <button type="button" style="background-color: #DBEAFE; color: #1D61E7; font-size: 12px; font-weight: 700; padding: 4px 16px; border-radius: 9999px; border: none; cursor: pointer;">
                        Edit Foto
                    </button>
                </div>

                <div style="display: flex; flex-direction: column; gap: 16px; border-top: 1px solid #F1F5F9; padding-top: 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 13px;">
                        <div style="display: flex; align-items: center; gap: 10px; color: #64748B; font-weight: 500;">
                            <span>👤</span> <span>Username</span>
                        </div>
                        <div style="font-weight: 700; color: #0F172A;">{{ auth()->user()->username ?? auth()->user()->name ?? 'adminbmkg' }}</div>
                    </div>

                    <div style="border-bottom: 1px solid #F8FAFC;"></div>

                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 13px;">
                        <div style="display: flex; align-items: center; gap: 10px; color: #64748B; font-weight: 500;">
                            <span>✉️</span> <span>Email</span>
                        </div>
                        <div style="font-weight: 700; color: #0F172A;">{{ auth()->user()->email ?? 'adminbmkg@gmail.com' }}</div>
                    </div>

                    <div style="border-bottom: 1px solid #F8FAFC;"></div>

                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 13px;">
                        <div style="display: flex; align-items: center; gap: 10px; color: #64748B; font-weight: 500;">
                            <span>💻</span> <span>IP Address</span>
                        </div>
                        <div style="font-weight: 700; color: #0F172A;">{{ request()->ip() ?? '192.168.1.10' }}</div>
                    </div>
                </div>

                <!-- Tombol Logout -->
                <form method="POST" action="{{ route('logout') }}" style="margin-top: 8px;">
                    @csrf
                    <button type="submit" style="width: 100%; border: 1px solid #1D61E7; background-color: #FFFFFF; color: #1D61E7; font-weight: 700; font-size: 14px; padding: 10px; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <span>🚪</span> <span>Logout</span>
                    </button>
                </form>
            </div>

            <!-- ================= KARTU KANAN: EDIT PROFIL & UBAH PASSWORD ================= -->
            <div style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 28px; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05);">
                
                <form action="{{ route('profile.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 24px;">
                    @csrf
                    @method('patch')

                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <h2 style="font-size: 16px; font-weight: 700; color: #0F172A; margin: 0;">Edit Profil</h2>

                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Username</label>
                            <input type="text" name="username" value="{{ old('username', auth()->user()->username ?? auth()->user()->name ?? 'adminbmkg') }}" 
                                style="width: 100%; border: 1px solid #CBD5E1; border-radius: 10px; padding: 12px 14px; font-size: 14px; font-weight: 700; color: #0F172A; background-color: #FFFFFF; outline: none;">
                        </div>

                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Email</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? 'adminbmkg@gmail.com') }}" 
                                style="width: 100%; border: 1px solid #CBD5E1; border-radius: 10px; padding: 12px 14px; font-size: 14px; font-weight: 700; color: #0F172A; background-color: #FFFFFF; outline: none;">
                        </div>
                    </div>

                    <div style="border-bottom: 1px solid #F1F5F9;"></div>

                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <h2 style="font-size: 16px; font-weight: 700; color: #0F172A; margin: 0;">Ubah Password</h2>

                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Password Lama</label>
                            <input type="password" name="current_password" placeholder="Masukkan password lama" 
                                style="width: 100%; border: 1px solid #CBD5E1; border-radius: 10px; padding: 12px 14px; font-size: 14px; font-weight: 500; color: #0F172A; background-color: #FFFFFF; outline: none;">
                        </div>

                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Password Baru</label>
                            <input type="password" name="password" placeholder="Masukkan password baru" 
                                style="width: 100%; border: 1px solid #CBD5E1; border-radius: 10px; padding: 12px 14px; font-size: 14px; font-weight: 500; color: #0F172A; background-color: #FFFFFF; outline: none;">
                        </div>

                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" placeholder="Konfirmasi password baru" 
                                style="width: 100%; border: 1px solid #CBD5E1; border-radius: 10px; padding: 12px 14px; font-size: 14px; font-weight: 500; color: #0F172A; background-color: #FFFFFF; outline: none;">
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px;">
                        <span style="font-size: 12px; font-weight: 600; color: #64748B;">Kosongkan jika tidak ingin merubah password</span>

                        <button type="submit" style="background-color: #1D61E7; color: #FFFFFF; font-weight: 700; font-size: 14px; padding: 12px 24px; border-radius: 10px; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <span>💾</span> <span>Simpan Perubahan</span>
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>
</x-app-layout>