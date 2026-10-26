<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LokasiController extends Controller
{
    /**
     * Membangun daftar lokasi monitoring dari database dengan fallback aman.
     */
    private function locations(): array
    {
        $dbLocations = Location::where('is_active', true)->get();

        if ($dbLocations->isEmpty()) {
            return $this->fallbackLocations();
        }

        $result = [];
        $fallback = $this->fallbackLocations();

        foreach ($dbLocations as $loc) {
            $slug = DashboardController::slugifyLocation($loc);

            // Cari prediksi atau data terbaru lokasi ini
            $latestPrediction = Prediction::where('location_id', $loc->id)
                ->orderBy('record_date', 'desc')
                ->first();

            // Status pasang surut: normal / tinggi / rendah
            if ($latestPrediction) {
                $statusDb = strtolower($latestPrediction->status);
                if ($statusDb === 'bahaya') {
                    $status = 'tinggi';
                    $statusLabel = 'Tinggi';
                } elseif ($statusDb === 'waspada') {
                    $status = 'tinggi';
                    $statusLabel = 'Tinggi';
                } else {
                    $status = 'normal';
                    $statusLabel = 'Normal';
                }

                $sensorUpdate = $latestPrediction->updated_at
                    ? $latestPrediction->updated_at->format('d F Y, H:i') . ' WIB'
                    : now()->format('d F Y, H:i') . ' WIB';
            } else {
                $status = 'normal';
                $statusLabel = 'Normal';
                $sensorUpdate = now()->format('d F Y, H:i') . ' WIB';
            }

            // Image fallback per lokasi
            $image = match ($slug) {
                'kalianget'          => '/images/kalianget_new.jpg',
                'surabaya-pelabuhan' => '/images/surabaya_pelabuhan_new.webp',
                'surabaya-timur'     => '/images/surabaya-timur.jpg',
                'surabaya-barat'     => '/images/surabaya-barat.jpg',
                'banyuwangi'         => '/images/banyuwangi_new.jpg',
                default              => '/images/kalianget_new.jpg',
            };

            $kabupaten = str_contains(strtolower($loc->name), 'banyuwangi') ? 'Banyuwangi, Jawa Timur' :
                         (str_contains(strtolower($loc->name), 'kalianget') ? 'Madura, Jawa Timur' : 'Surabaya, Jawa Timur');

            $jenisLokasi = str_contains(strtolower($loc->name), 'pelabuhan') ? 'Pelabuhan' : 'Stasiun Pengamatan';

            $result[$slug] = [
                'id'            => $loc->id,
                'slug'          => $slug,
                'name'          => $loc->name,
                'kabupaten'     => $kabupaten,
                'status'        => $fallback[$slug]['status'] ?? $status,
                'status_label'  => $fallback[$slug]['status_label'] ?? $statusLabel,
                'lat'           => (float) $loc->latitude,
                'lng'           => (float) $loc->longitude,
                'id_lokasi'     => 'LOC-' . ($loc->code ?: '00' . $loc->id),
                'jenis_lokasi'  => $jenisLokasi,
                'sensor_update' => $fallback[$slug]['sensor_update'] ?? $sensorUpdate,
                'image'         => $image,
                'areas'         => $fallback[$slug]['areas'] ?? [
               ['name' => $loc->name, 'lat' => (float) $loc->latitude, 'lng' => (float) $loc->longitude],
],
            ];
        }

        return $result;
    }

    private function fallbackLocations(): array
    {
        return [
            'kalianget' => [
                'slug'          => 'kalianget',
                'name'          => 'Kalianget',
                'kabupaten'     => 'Madura, Jawa Timur',
                'status'        => 'normal',
                'status_label'  => 'Normal',
                'lat'           => -7.0426,
                'lng'           => 113.9276,
                'id_lokasi'     => 'LOC-KAL',
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
        $firstKey  = array_key_first($locations) ?? 'kalianget';
        $selected  = $request->query('lokasi', $firstKey);

        if (! array_key_exists($selected, $locations)) {
            $selected = $firstKey;
        }

        return view('lokasi', [
            'locations' => $locations,
            'selected'  => $selected,
            'active'    => $locations[$selected],
        ]);
    }
}