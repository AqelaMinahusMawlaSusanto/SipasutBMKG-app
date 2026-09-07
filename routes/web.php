<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

// Redirect root ke Dashboard User
Route::get('/', function () {
    return redirect()->route('user.dashboard');
});

// =====================================
// AUTHENTICATION
// =====================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// =====================================
// 🔵 USER MODULES (Sesuai Desain Figma)
// =====================================
Route::get('/dashboard', [UserDashboardController::class, 'dashboard'])->name('user.dashboard');
Route::get('/kondisi-pasang-surut', [UserDashboardController::class, 'tideConditions'])->name('user.kondisi');
Route::get('/lokasi-monitoring', [UserDashboardController::class, 'monitoring'])->name('user.monitoring');
Route::get('/kalender', [UserDashboardController::class, 'calendar'])->name('user.calendar');
Route::get('/api/day-details', [UserDashboardController::class, 'apiDayDetails'])->name('api.day-details');

// =====================================
// 🔴 ADMIN MODULES (Sesuai Desain Figma)
// =====================================
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.data');
    });

    // 1. Kelola Data (Upload & Riwayat)
    Route::get('/data', [AdminController::class, 'kelolaData'])->name('admin.data');
    Route::post('/data/upload', [AdminController::class, 'storeDataUpload'])->name('admin.data.upload');
    Route::delete('/data/{id}', [AdminController::class, 'deleteDataUpload'])->name('admin.data.delete');

    // 2. Kelola Lokasi (CRUD 5 Titik Pantai)
    Route::get('/lokasi', [AdminController::class, 'kelolaLokasi'])->name('admin.lokasi');
    Route::post('/lokasi', [AdminController::class, 'storeLocation'])->name('admin.lokasi.store');
    Route::put('/lokasi/{id}', [AdminController::class, 'updateLocation'])->name('admin.lokasi.update');
    Route::delete('/lokasi/{id}', [AdminController::class, 'deleteLocation'])->name('admin.lokasi.delete');

    // 3. Kelola Profil
    Route::get('/profil', [AdminController::class, 'kelolaProfil'])->name('admin.profil');
    Route::post('/profil', [AdminController::class, 'updateProfil'])->name('admin.profil.update');

    // 4. Kelola Notif
    Route::get('/notifikasi', [AdminController::class, 'kelolaNotif'])->name('admin.notif');
    Route::post('/notifikasi', [AdminController::class, 'storeNotif'])->name('admin.notif.store');
    Route::post('/notifikasi/{id}/toggle', [AdminController::class, 'toggleNotif'])->name('admin.notif.toggle');
    Route::delete('/notifikasi/{id}', [AdminController::class, 'deleteNotif'])->name('admin.notif.delete');

    // 5. Login Aktivitas
    Route::get('/aktivitas', [AdminController::class, 'loginAktivitas'])->name('admin.aktivitas');
});
