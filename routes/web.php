<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\KalenderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

Route::get('/download-pasang-surut', [DownloadController::class, 'index'])->name('download.index');
Route::get('/download-pasang-surut/export', [DownloadController::class, 'export'])->name('download.export');

Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index');
Route::get('/kalender', [KalenderController::class, 'index'])->name('kalender.index');

