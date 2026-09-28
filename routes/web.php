<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\KalenderController;
use Illuminate\Support\Facades\Route;

// =====================================
// PUBLIC MODULES
// =====================================
Route::get('/', function () {
    return redirect()->route('user.dashboard');
})->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/user/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');

Route::get('/download-pasang-surut', [DownloadController::class, 'index'])->name('download.index');
Route::get('/download-pasang-surut/export', [DownloadController::class, 'export'])->name('download.export');

Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index');
Route::get('/kalender', [KalenderController::class, 'index'])->name('kalender.index');

// =====================================
// AUTHENTICATION (Laravel Spatie Protected)
// =====================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// =====================================
// ADMIN MODULES (Protected with Spatie role:admin)
// =====================================
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.index');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

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
