<x-guest-layout>
    <div class="auth-card">
        <img src="{{ asset('assets/img/logo-bmkg.png') }}" alt="" class="card-logo">
        <h2>Login Admin BMKG</h2>
        <p class="card-sub">Monitoring Pasang Surut Air Laut<br>BMKG Tanjung Perak Surabaya</p>

        {{-- Pesan sukses (misal setelah reset password) --}}
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form class="form" method="POST" action="{{ route('login') }}">
            @csrf

            <div class="field">
                <label for="email">Email</label>
                <div class="control">
                    <input type="email" id="email" name="email" placeholder="Masukkan Email"
                           value="{{ old('email') }}" autocomplete="email" autofocus>
                </div>
                @error('email')
                    <div class="alert alert-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="control">
                    <input type="password" id="password" name="password" placeholder="Masukkan Password"
                           autocomplete="current-password">
                </div>
                @error('password')
                    <div class="alert alert-error">{{ $message }}</div>
                @enderror
            </div>

            <label class="remember">
                <input type="checkbox" name="remember">
                Ingat Saya
            </label>

            <button type="submit" class="btn">Login</button>
        </form>

        <p class="card-foot">Belum Punya Akun? <a href="{{ route('register') }}">Klik Disini</a></p>
    </div>
</x-guest-layout>