<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LokasiController extends Controller
{
    /**
     * Data dummy lokasi monitoring pasang surut.
     * TODO: nanti ganti isi method ini dengan query ke tabel `monitoring_locations`
     * (hasil import dari Google Colab -> Excel -> database) alih-alih data statis.
     * Struktur array di bawah (key & isi) sudah dirancang mengikuti bentuk yang
     * dipakai di resources/views/lokasi.blade.php, jadi kalau sudah ganti ke
     * database, cukup pastikan setiap baris punya kolom yang sama persis.
     */
    private function locations(): array
    {
        return [
            'kalianget' => [
                'slug'          => 'kalianget',
                'name'          => 'Kalianget',
                'kabupaten'     => 'Madura, Jawa Timur',
                'status'        => 'normal',   // tinggi | normal | rendah
                'status_label'  => 'Normal',
                'lat'           => -7.0426,
                'lng'           => 113.9276,
                'id_lokasi'     => 'LOC-TPK-001',
                'jenis_lokasi'  => 'Pelabuhan',
                'sensor_update' => '19 Agustus 2026, 18:25 WIB',
                'image'         => '/images/kalianget_new.jpg',
            ],
            'surabaya-pelabuhan' => [
                'slug'          => 'surabaya-pelabuhan',
                'name'          => 'Surabaya Pelabuhan',
                'kabupaten'     => 'Surabaya, Jawa Timur',
                'status'        => 'normal',
                'status_label'  => 'Normal',
                'lat'           => -7.2019,
                'lng'           => 112.7379,
                'id_lokasi'     => 'LOC-TPK-002',
                'jenis_lokasi'  => 'Pelabuhan',
                'sensor_update' => '18 Agustus 2026, 18:20 WIB',
                'image'         => '/images/surabaya_pelabuhan_new.webp',
            ],
            'surabaya-timur' => [
                'slug'          => 'surabaya-timur',
                'name'          => 'Surabaya Timur',
                'kabupaten'     => 'Surabaya, Jawa Timur',
                'status'        => 'rendah',
                'status_label'  => 'Rendah',
                'lat'           => -7.2854,
                'lng'           => 112.7947,
                'id_lokasi'     => 'LOC-TPK-003',
                'jenis_lokasi'  => 'Stasiun Pengamatan',
                'sensor_update' => '18 Agustus 2026, 18:30 WIB',
                'image'         => '/images/surabaya-timur.jpg',
            ],
            'surabaya-barat' => [
                'slug'          => 'surabaya-barat',
                'name'          => 'Surabaya Barat',
                'kabupaten'     => 'Surabaya, Jawa Timur',
                'status'        => 'rendah',
                'status_label'  => 'Rendah',
                'lat'           => -7.2429,
                'lng'           => 112.6913,
                'id_lokasi'     => 'LOC-TPK-004',
                'jenis_lokasi'  => 'Stasiun Pengamatan',
                'sensor_update' => '18 Agustus 2026, 18:30 WIB',
                'image'         => '/images/surabaya-barat.jpg',
            ],
            'banyuwangi' => [
                'slug'          => 'banyuwangi',
                'name'          => 'Banyuwangi',
                'kabupaten'     => 'Banyuwangi, Jawa Timur',
                'status'        => 'tinggi',
                'status_label'  => 'Tinggi',
                'lat'           => -8.2192,
                'lng'           => 114.3691,
                'id_lokasi'     => 'LOC-TPK-005',
                'jenis_lokasi'  => 'Pelabuhan',
                'sensor_update' => '19 Agustus 2026, 18:25 WIB',
                'image'         => '/images/banyuwangi_new.jpg',
            ],
        ];
    }

    /**
     * GET /lokasi
     * GET /lokasi?lokasi=banyuwangi
     */
    public function index(Request $request)
    {
        $locations = $this->locations();
        $selected  = $request->query('lokasi', 'kalianget');

        if (! array_key_exists($selected, $locations)) {
            $selected = 'kalianget';
        }

        return view('lokasi', [
            'locations' => $locations,
            'selected'  => $selected,
            'active'    => $locations[$selected],
        ]);
    }
}