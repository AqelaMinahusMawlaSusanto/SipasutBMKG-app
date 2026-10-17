<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KalenderController extends Controller
{
    /**
     * Daftar lokasi untuk dropdown "Cari Lokasi".
     * PENTING: slug & nama di sini harus tetap sama dengan
     * LokasiController::locations() supaya lokasi yang dipilih
     * di halaman Kalender konsisten dengan halaman Lokasi Monitoring.
     */
    private function locations(): array
    {
        return [
            ['slug' => 'surabaya-barat',     'name' => 'Surabaya Barat'],
            ['slug' => 'surabaya-timur',     'name' => 'Surabaya Timur'],
            ['slug' => 'surabaya-pelabuhan', 'name' => 'Surabaya Pelabuhan'],
            ['slug' => 'kalianget',          'name' => 'Kalianget'],
            ['slug' => 'banyuwangi',         'name' => 'Banyuwangi'],
        ];
    }

    /**
     * GET /kalender
     * Data pasang/surut per tanggal dihitung langsung di public/js/kalender.js
     * (dummy, dibuat konsisten per lokasi+tanggal), jadi controller ini
     * cuma perlu kirim daftar lokasi ke view.
     */
    public function index()
    {
        return view('kalender', [
            'locations' => $this->locations(),
        ]);
    }
}