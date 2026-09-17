<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Data dummy 5 lokasi pantau pasang surut.
     * TODO: nanti ganti isi method ini dengan query ke tabel `tide_readings`
     * (hasil import dari Google Colab -> Excel -> database) alih-alih data statis.
     */
    private function locations(): array
    {
        return [
            'surabaya-timur' => [
                'label'        => 'Surabaya Timur',
                'kabupaten'    => 'Surabaya, Jawa Timur',
                'status'       => 'aman',
                'status_label' => 'AMAN',
                'status_desc'  => 'Kondisi Normal',
                'current_tide' => 2.3,
                'current_time' => '15.30 WIB',
                'wind_speed'   => 12,
                'wind_dir'     => 'Timur Laut',
                'weather'      => 'Cerah',
                'temperature'  => 31,
                'updated_at'   => '18/08/2026, 18.30 WIB',
                'last_height'  => 2.3,
                'coordinate'   => '-7.2498, 112.7351',
                'warning_title' => 'Tidak ada peringatan pasang tinggi.',
                'warning_desc'  => 'Kondisi aman untuk aktivitas di wilayah perairan.',
                'chart' => [
                    'labels' => ['00:00', '06:00', '12:00', '18:00', '24:00'],
                    'points' => [
                        ['time' => '05:20', 'value' => 2.8],
                        ['time' => '11:30', 'value' => 0.6],
                        ['time' => '17:40', 'value' => 2.7],
                        ['time' => '23:50', 'value' => 0.5],
                    ],
                ],
                'next_high' => ['value' => 2.7, 'time' => '17:40 WIB'],
            ],
            'surabaya-barat' => [
                'label'        => 'Surabaya Barat',
                'kabupaten'    => 'Surabaya, Jawa Timur',
                'status'       => 'aman',
                'status_label' => 'AMAN',
                'status_desc'  => 'Kondisi Normal',
                'current_tide' => 2.3,
                'current_time' => '15.30 WIB',
                'wind_speed'   => 12,
                'wind_dir'     => 'Timur Laut',
                'weather'      => 'Cerah',
                'temperature'  => 31,
                'updated_at'   => '18/08/2026, 18.30 WIB',
                'last_height'  => 2.3,
                'coordinate'   => '-7.2498, 112.7351',
                'warning_title' => 'Tidak ada peringatan pasang tinggi.',
                'warning_desc'  => 'Kondisi aman untuk aktivitas di wilayah perairan.',
                'chart' => [
                    'labels' => ['00:00', '06:00', '12:00', '18:00', '24:00'],
                    'points' => [
                        ['time' => '05:20', 'value' => 2.8],
                        ['time' => '11:30', 'value' => 0.6],
                        ['time' => '17:40', 'value' => 2.7],
                        ['time' => '23:50', 'value' => 0.5],
                    ],
                ],
                'next_high' => ['value' => 2.7, 'time' => '17:40 WIB'],
            ],
            'surabaya-pelabuhan' => [
                'label'        => 'Surabaya Pelabuhan',
                'kabupaten'    => 'Surabaya, Jawa Timur',
                'status'       => 'aman',
                'status_label' => 'AMAN',
                'status_desc'  => 'Kondisi Normal',
                'current_tide' => 2.3,
                'current_time' => '15.30 WIB',
                'wind_speed'   => 12,
                'wind_dir'     => 'Timur Laut',
                'weather'      => 'Cerah',
                'temperature'  => 31,
                'updated_at'   => '18/08/2026, 18.30 WIB',
                'last_height'  => 2.3,
                'coordinate'   => '-7.2498, 112.7351',
                'warning_title' => 'Tidak ada peringatan pasang tinggi.',
                'warning_desc'  => 'Kondisi aman untuk aktivitas di wilayah perairan.',
                'chart' => [
                    'labels' => ['00:00', '06:00', '12:00', '18:00', '24:00'],
                    'points' => [
                        ['time' => '05:20', 'value' => 2.8],
                        ['time' => '11:30', 'value' => 0.6],
                        ['time' => '17:40', 'value' => 2.7],
                        ['time' => '23:50', 'value' => 0.5],
                    ],
                ],
                'next_high' => ['value' => 2.7, 'time' => '17:40 WIB'],
            ],
            'kalianget' => [
                'label'        => 'Kalianget',
                'kabupaten'    => 'Surabaya, Jawa Timur',
                'status'       => 'waspada',
                'status_label' => 'WASPADA',
                'status_desc'  => 'Kondisi Siaga',
                'current_tide' => 2.3,
                'current_time' => '15.30 WIB',
                'wind_speed'   => 12,
                'wind_dir'     => 'Timur Laut',
                'weather'      => 'Cerah',
                'temperature'  => 31,
                'updated_at'   => '18/08/2026, 18.30 WIB',
                'last_height'  => 2.3,
                'coordinate'   => '-7.2498, 112.7351',
                'warning_title' => 'Kemungkinan terjadinya pasang tinggi.',
                'warning_desc'  => 'Waspada pasang tinggi yang dapat mempengaruhi aktivitas di pesisir.',
                'chart' => [
                    'labels' => ['00:00', '06:00', '12:00', '18:00', '24:00'],
                    'points' => [
                        ['time' => '05:30', 'value' => 2.6],
                        ['time' => '11:40', 'value' => 0.7],
                        ['time' => '17:20', 'value' => 2.4],
                        ['time' => '23:40', 'value' => 0.6],
                    ],
                ],
                'next_high' => ['value' => null, 'time' => '17:40 WIB'],
            ],
            'banyuwangi' => [
                'label'        => 'Banyuwangi',
                'kabupaten'    => 'Banyuwangi, Jawa Timur',
                'status'       => 'bahaya',
                'status_label' => 'BAHAYA',
                'status_desc'  => 'Kondisi Bahaya',
                'current_tide' => 3.1,
                'current_time' => '15.30 WIB',
                'wind_speed'   => 18,
                'wind_dir'     => 'Timur Laut',
                'weather'      => 'Cerah',
                'temperature'  => 28,
                'updated_at'   => '18/08/2026, 18.30 WIB',
                'last_height'  => 3.1,
                'coordinate'   => '-7.2498, 112.7351',
                'warning_title' => 'Terjadi pasang tinggi.',
                'warning_desc'  => 'Bahaya pasang sangat tinggi berpotensi menyebabkan banjir pesisir.',
                'chart' => [
                    'labels' => ['00:00', '06:00', '12:00', '18:00', '24:00'],
                    'points' => [
                        ['time' => '05:10', 'value' => 3.4],
                        ['time' => '11:30', 'value' => 0.5],
                        ['time' => '17:00', 'value' => 3.2],
                        ['time' => '23:20', 'value' => 0.4],
                    ],
                ],
                'next_high' => ['value' => null, 'time' => '17:00 WIB'],
            ],
        ];
    }

    /**
     * GET /dashboard
     * GET /dashboard?lokasi=kalianget
     */
    public function index(Request $request)
    {
        $locations = $this->locations();
        $selected  = $request->query('lokasi', 'surabaya-timur');

        if (! array_key_exists($selected, $locations)) {
            $selected = 'surabaya-timur';
        }

        return view('dashboard', [
            'locations' => $locations,
            'selected'  => $selected,
            'active'    => $locations[$selected],
        ]);
    }
}
