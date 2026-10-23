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
                'status'        => $status,
                'status_label'  => $statusLabel,
                'lat'           => (float) $loc->latitude,
                'lng'           => (float) $loc->longitude,
                'id_lokasi'     => 'LOC-' . ($loc->code ?: '00' . $loc->id),
                'jenis_lokasi'  => $jenisLokasi,
                'sensor_update' => $sensorUpdate,
                'image'         => $image,
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
                'sensor_update' => '19 Agustus 2026, 18:25 WIB',
                'image'         => '/images/kalianget_new.jpg',
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