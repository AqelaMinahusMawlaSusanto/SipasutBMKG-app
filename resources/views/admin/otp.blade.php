<x-guest-layout>
    <div class="auth-card">
        <img src="{{ asset('assets/img/logo-bmkg.png') }}" alt="" class="card-logo">
        <h2>Verifikasi OTP</h2>
        <p class="card-sub">Kode 6 digit sudah dikirim ke email kamu. Berlaku 5 menit.</p>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form class="form" method="POST" action="{{ route('otp.verify') }}">
            @csrf

            <div class="field">
                <label for="code">Kode OTP</label>
                <div class="control">
                    <input type="text" id="code" name="code" maxlength="6" inputmode="numeric"
                           placeholder="123456" autofocus>
                </div>
                @error('code')
                    <div class="alert alert-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn">Verifikasi</button>
        </form>
    </div>
</x-guest-layout>