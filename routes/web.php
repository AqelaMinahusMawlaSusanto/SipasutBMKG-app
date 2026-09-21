<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// =========================================================
// Helper: hitung jumlah baris di tabel dengan aman.
// Kalau tabelnya belum ada / kolomnya beda, tetap balik 0
// (tidak bikin error).
// =========================================================
function safeCount(string $table, ?array $where = null): int
{
    if (!Schema::hasTable($table)) {
        return 0;
    }
    $query = DB::table($table);
    if ($where) {
        $query->where($where[0], $where[1]);
    }
    return $query->count();
}

Route::get('/dashboard', function () {
    $totalStasiun    = safeCount('stasiun', ['status', 'aktif']);
    $totalData       = safeCount('data_pasang_surut');
    $totalPeringatan = safeCount('peringatan', ['status', 'aktif']);

    return view('dashboard', compact('totalStasiun', 'totalData', 'totalPeringatan'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';