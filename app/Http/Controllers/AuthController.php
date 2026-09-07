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
            return Auth::user()->isAdmin() 
                ? redirect()->route('admin.data') 
                : redirect()->route('user.dashboard');
        }

        return view('auth.login');
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

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.data'))
                    ->with('success', "Selamat datang kembali, {$user->name}!");
            }

            return redirect()->intended(route('user.dashboard'))
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
