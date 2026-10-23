<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            ActivityLog::record(
                'login',
                "User {$user->name} ({$user->role}) berhasil login ke sistem.",
                $user
            );

            if ($user->hasRole('admin') || $user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', "Selamat datang kembali, {$user->name}!");
            }

            return redirect()->intended(route('dashboard.index'))
                ->with('success', "Selamat datang di SIPASUT, {$user->name}!");
        }

        ActivityLog::record(
            'failed_login',
            "Percobaan login gagal untuk email: {$request->email} dari IP " . $request->ip()
        );

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
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
