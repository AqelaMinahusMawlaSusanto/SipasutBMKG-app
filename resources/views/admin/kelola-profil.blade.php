@extends('layouts.admin')

@section('title', 'Kelola Profil')

@section('content')
<div class="space-y-6 max-w-4xl">

    <!-- Page Header -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
        <span class="text-xs font-bold text-sky-600 uppercase tracking-wider">Pengaturan Akun</span>
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">Kelola Profil Administrator</h1>
        <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi identitas pribadi dan kredensial keamanan akun admin Anda.</p>
    </div>

    <!-- User Profile Overview Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center sm:items-start gap-6">
        <div class="w-20 h-20 rounded-2xl bg-sky-600 text-white font-extrabold text-3xl flex items-center justify-center shadow-lg shadow-sky-600/30 shrink-0">
            {{ strtoupper(substr($admin->name, 0, 1)) }}
        </div>
        <div class="flex-1 text-center sm:text-left">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $admin->name }}</h2>
                    <p class="text-xs text-slate-500">{{ $admin->email }}</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-800 self-center sm:self-auto">
                    Role: {{ ucfirst($admin->role) }}
                </span>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block font-medium">Total Berkas Diunggah</span>
                    <span class="font-bold text-slate-800 text-sm">{{ $totalUploads }} berkas</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Terdaftar Sejak</span>
                    <span class="font-bold text-slate-800 text-sm">{{ $admin->created_at->translatedFormat('d F Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Edit Profil & Password -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
        <h3 class="text-base font-bold text-slate-900 mb-4">Ubah Data Diri & Keamanan</h3>

        <form action="{{ route('admin.profil.update') }}" method="POST" class="space-y-5 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>

                <div>
                    <label for="email" class="block font-bold text-slate-700 mb-1.5">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label for="phone" class="block font-bold text-slate-700 mb-1.5">Nomor Telepon / WhatsApp (Opsional)</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $admin->phone) }}" placeholder="+62 8..."
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div class="pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Ganti Kata Sandi (Kosongkan jika tidak diubah)</h4>
                
                <div class="space-y-3">
                    <div>
                        <label for="current_password" class="block font-semibold text-slate-700 mb-1">Kata Sandi Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" placeholder="••••••••"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="new_password" class="block font-semibold text-slate-700 mb-1">Kata Sandi Baru</label>
                            <input type="password" name="new_password" id="new_password" placeholder="Minimal 6 karakter"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        </div>
                        <div>
                            <label for="new_password_confirmation" class="block font-semibold text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" placeholder="Ulangi kata sandi"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-semibold focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-3">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold shadow-md shadow-sky-600/20 transition">
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
