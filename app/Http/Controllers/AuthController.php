<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            return $user->hasRole('admin') || $user->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('dashboard.index');
        }

        return view('admin.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            return $user->hasRole('admin') || $user->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('dashboard.index');
        }

        return view('admin.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
        ]);

        // Assign Spatie Role
        $user->assignRole('admin');

        Auth::login($user);

        ActivityLog::record(
            'register',
            "Admin baru {$user->name} ({$user->email}) berhasil mendaftar.",
            $user
        );

        return redirect()->route('admin.dashboard')
            ->with('success', "Selamat datang di SIPASUT BMKG, {$user->name}! Akun Admin berhasil dibuat.");
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::validate($credentials)) {
            ActivityLog::record(
                'failed_login',
                "Percobaan login gagal untuk email: {$request->email} dari IP " . $request->ip()
            );

            return back()->withErrors([
                'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
            ])->onlyInput('email');
        }

        $user = User::where('email', $credentials['email'])->first();

        $code = (string) random_int(100000, 999999);

        $user->update([
            'otp_code' => Hash::make($code),
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($user->email)->send(new OtpMail($code));

        $request->session()->put('otp_user_id', $user->id);
        $request->session()->put('otp_remember', $remember);

        return redirect()->route('otp.show');
    }

    public function showOtp(Request $request)
{
    if (! $request->session()->has('otp_user_id')) {
        return redirect()->route('login');
    }

    return view('admin.otp');
}

public function verifyOtp(Request $request)
{
    $request->validate([
        'code' => ['required', 'digits:6'],
    ]);

    $userId = $request->session()->get('otp_user_id');
    $user = $userId ? User::find($userId) : null;

    if (! $user || ! $user->otp_code || now()->gt($user->otp_expires_at)) {
        return redirect()->route('login')
            ->withErrors(['email' => 'Kode OTP sudah kadaluarsa. Silakan login ulang.']);
    }

    if (! Hash::check($request->code, $user->otp_code)) {
        return back()->withErrors(['code' => 'Kode OTP salah.']);
    }

    $user->update(['otp_code' => null, 'otp_expires_at' => null]);

    $remember = $request->session()->pull('otp_remember', false);
    $request->session()->forget('otp_user_id');

    Auth::login($user, $remember);
    $request->session()->regenerate();

    ActivityLog::record('login_otp', "User {$user->name} berhasil verifikasi OTP.", $user);

    return $user->hasRole('admin') || $user->isAdmin()
        ? redirect()->intended(route('admin.dashboard'))
        : redirect()->intended(route('dashboard.index'));
}

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            ActivityLog::record(
                'logout',
                "User {$user->name} ({$user->role}) telah keluar dari sistem.",
                $user
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil logout.');
    }
}
