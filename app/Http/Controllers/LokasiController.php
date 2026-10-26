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
                'sensor_update' => '26 Oktober 2026, 11:00 WIB',
                'image'         => '/images/kalianget_new.jpg',
                'areas'         => [
                    ['name' => 'Pamekasan', 'lat' => -7.1568, 'lng' => 113.4824],
                ],
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
                'sensor_update' => '26 Oktober 2026, 11:00 WIB',
                'image'         => '/images/surabaya_pelabuhan_new.webp',
                'areas'         => [
                    ['name' => 'Surabaya Utara', 'lat' => -7.2150, 'lng' => 112.7400],
                    ['name' => 'Benowo',         'lat' => -7.2370, 'lng' => 112.6200],
                ],
            ],
            'surabaya-timur' => [
                'slug'          => 'surabaya-timur',
                'name'          => 'Surabaya Timur',
                'kabupaten'     => 'Surabaya, Jawa Timur',
                'status'        => 'normal',
                'status_label'  => 'Normal',
                'lat'           => -7.2854,
                'lng'           => 112.7947,
                'id_lokasi'     => 'LOC-TPK-003',
                'jenis_lokasi'  => 'Stasiun Pengamatan',
                'sensor_update' => '26 Oktober 2026, 11:00 WIB',
                'image'         => '/images/surabaya-timur.jpg',
                'areas'         => [
                    ['name' => 'Pasuruan',               'lat' => -7.6453, 'lng' => 112.9075],
                    ['name' => 'Sidoarjo',               'lat' => -7.4478, 'lng' => 112.7183],
                    ['name' => 'Probolinggo',            'lat' => -7.7543, 'lng' => 113.2159],
                    ['name' => 'Jember',                 'lat' => -8.1845, 'lng' => 113.6681],
                    ['name' => 'Kenjeran',               'lat' => -7.2400, 'lng' => 112.7900],
                    ['name' => 'Bangkalan Selatan',      'lat' => -7.1000, 'lng' => 112.7500],
                    ['name' => 'Kuanyar',                'lat' => -7.1280, 'lng' => 112.9150],
                    ['name' => 'Sukolilo',               'lat' => -7.2950, 'lng' => 112.7950],
                    ['name' => 'Sampang',                'lat' => -7.1887, 'lng' => 113.2397],
                    ['name' => 'Sepanjang Selat Madura', 'lat' => -7.2000, 'lng' => 112.8500],
                    ['name' => 'Kreseh (barat Sampang)', 'lat' => -7.2100, 'lng' => 113.0800],
                ],
            ],
            'surabaya-barat' => [
                'slug'          => 'surabaya-barat',
                'name'          => 'Surabaya Barat',
                'kabupaten'     => 'Surabaya, Jawa Timur',
                'status'        => 'tinggi',
                'status_label'  => 'Tinggi',
                'lat'           => -7.2429,
                'lng'           => 112.6913,
                'id_lokasi'     => 'LOC-TPK-004',
                'jenis_lokasi'  => 'Stasiun Pengamatan',
                'sensor_update' => '26 Oktober 2026, 11:00 WIB',
                'image'         => '/images/surabaya-barat.jpg',
                'areas'         => [
                    ['name' => 'Gresik',   'lat' => -7.1539, 'lng' => 112.6561],
                    ['name' => 'Lamongan', 'lat' => -6.8730, 'lng' => 112.2060],
                    ['name' => 'Tuban',    'lat' => -6.8976, 'lng' => 112.0647],
                ],
            ],
            'banyuwangi' => [
                'slug'          => 'banyuwangi',
                'name'          => 'Banyuwangi',
                'kabupaten'     => 'Banyuwangi, Jawa Timur',
                'status'        => 'normal',
                'status_label'  => 'Normal',
                'lat'           => -8.2192,
                'lng'           => 114.3691,
                'id_lokasi'     => 'LOC-TPK-005',
                'jenis_lokasi'  => 'Pelabuhan',
                'sensor_update' => '26 Oktober 2026, 11:00 WIB',
                'image'         => '/images/banyuwangi_new.jpg',
                'areas'         => [
                    ['name' => 'Banyuwangi', 'lat' => -8.2192, 'lng' => 114.3691],
                ],
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