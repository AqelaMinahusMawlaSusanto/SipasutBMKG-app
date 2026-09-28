<x-guest-layout>
    <div class="auth-card">
        <img src="{{ asset('assets/img/logo-bmkg.png') }}" alt="" class="card-logo">
        <h2>Buat Akun Baru Admin BMKG</h2>
        <p class="card-sub">Monitoring Pasang Surut Air Laut<br>BMKG Tanjung Perak Surabaya</p>

        <form class="form" method="POST" action="{{ route('register') }}">
            @csrf

            <div class="field">
                <label for="name">Nama Pegawai</label>
                <div class="control">
                    <input type="text" id="name" name="name" placeholder="Masukkan Nama"
                           value="{{ old('name') }}" autocomplete="name" autofocus>
                </div>
                @error('name')
                    <div class="alert alert-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="email">Email</label>
                <div class="control">
                    <input type="email" id="email" name="email" placeholder="Masukkan Email"
                           value="{{ old('email') }}" autocomplete="username">
                </div>
                @error('email')
                    <div class="alert alert-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="control">
                    <input type="password" id="password" name="password" placeholder="Masukkan Password"
                           autocomplete="new-password">
                </div>
                @error('password')
                    <div class="alert alert-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">Konfirmasi Password</label>
                <div class="control">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           placeholder="Ulangi Password" autocomplete="new-password">
                </div>
                @error('password_confirmation')
                    <div class="alert alert-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn">Daftar</button>
        </form>

        <p class="card-foot">Sudah Punya Akun? <a href="{{ route('login') }}">Login di sini</a></p>
    </div>
</x-guest-layout>