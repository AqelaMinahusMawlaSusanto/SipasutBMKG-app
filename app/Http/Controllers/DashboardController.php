<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Notification;
use App\Models\Prediction;
use App\Models\TidalData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Helper untuk memetakan nama / kode lokasi ke slug URL yang konsisten.
     */
    public static function slugifyLocation(Location $loc): string
    {
        return match (strtoupper($loc->code)) {
            'SBYTIM' => 'surabaya-timur',
            'SBYBRT' => 'surabaya-barat',
            'SBYPLB' => 'surabaya-pelabuhan',
            'KAL'    => 'kalianget',
            'BWI'    => 'banyuwangi',
            default  => Str::slug($loc->name),
        };
    }

    /**
     * Membangun representasi data untuk semua lokasi dari database (dengan fallback yang aman).
     */
    private function buildLocationsData(): array
    {
        $dbLocations = Location::where('is_active', true)->get();

        // Jika database belum memiliki lokasi, sediakan fallback default 5 lokasi
        if ($dbLocations->isEmpty()) {
            return $this->fallbackLocations();
        }

        $result = [];

        foreach ($dbLocations as $loc) {
            $slug = self::slugifyLocation($loc);

            // Cari prediksi pasang surut terbaru untuk lokasi ini
            $latestPrediction = Prediction::where('location_id', $loc->id)
                ->orderBy('record_date', 'desc')
                ->orderBy('updated_at', 'desc')
                ->first();

            // Status pasang surut
            $statusRaw = $latestPrediction ? strtolower($latestPrediction->status) : 'aman';
            if (!in_array($statusRaw, ['aman', 'waspada', 'bahaya'])) {
                $statusRaw = 'aman';
            }

            $statusLabel = match ($statusRaw) {
                'waspada' => 'WASPADA',
                'bahaya'  => 'BAHAYA',
                default   => 'AMAN',
            };

            $statusDesc = match ($statusRaw) {
                'waspada' => 'Kondisi Siaga',
                'bahaya'  => 'Kondisi Bahaya',
                default   => 'Kondisi Normal',
            };

            // Pasang saat ini & ketinggian
            $currentTide = $latestPrediction ? (float) $latestPrediction->high_tide_level : 2.3;
            $currentTime = now()->format('H.i') . ' WIB';

            // Ketinggian terakhir
            $lastHeight = $latestPrediction ? (float) $latestPrediction->high_tide_level : 2.3;

            // Updated at string
            if ($latestPrediction && $latestPrediction->updated_at) {
                $updatedAt = $latestPrediction->updated_at->format('d/m/Y, H.i') . ' WIB';
            } else {
                $updatedAt = now()->format('d/m/Y, H.i') . ' WIB';
            }

            // Koordinat
            $coord = sprintf('%.4f, %.4f', $loc->latitude, $loc->longitude);

            // Warning Title & Desc
            if ($statusRaw === 'bahaya') {
                $warningTitle = 'Terjadi pasang tinggi.';
                $warningDesc  = 'Bahaya pasang sangat tinggi berpotensi menyebabkan banjir pesisir.';
            } elseif ($statusRaw === 'waspada') {
                $warningTitle = 'Kemungkinan terjadinya pasang tinggi.';
                $warningDesc  = 'Waspada pasang tinggi yang dapat mempengaruhi aktivitas di pesisir.';
            } else {
                $warningTitle = 'Tidak ada peringatan pasang tinggi.';
                $warningDesc  = 'Kondisi aman untuk aktivitas di wilayah perairan.';
            }

            // Next high prediction time
            $nextHighTime = $latestPrediction ? $latestPrediction->high_tide_time : '17:40 WIB';
            $nextHighVal  = $latestPrediction ? (float) $latestPrediction->high_tide_level : 2.7;

            // Susun chart points dari TidalData (jika ada data per jam) atau dari titik prediksi
            $chart = $this->buildChartPoints($loc, $latestPrediction);

            // Kabupaten / Wilayah
            $kabupaten = str_contains(strtolower($loc->name), 'banyuwangi') ? 'Banyuwangi, Jawa Timur' :
                         (str_contains(strtolower($loc->name), 'kalianget') ? 'Sumenep, Jawa Timur' : 'Surabaya, Jawa Timur');

            $result[$slug] = [
                'id'            => $loc->id,
                'slug'          => $slug,
                'code'          => $loc->code,
                'label'         => $loc->name,
                'kabupaten'     => $kabupaten,
                'status'        => $statusRaw,
                'status_label'  => $statusLabel,
                'status_desc'   => $statusDesc,
                'current_tide'  => $currentTide,
                'current_time'  => $currentTime,
                'wind_speed'    => 12,
                'wind_dir'      => 'Timur Laut',
                'weather'       => 'Cerah',
                'temperature'   => 31,
                'updated_at'    => $updatedAt,
                'last_height'   => $lastHeight,
                'coordinate'    => $coord,
                'warning_title' => $warningTitle,
                'warning_desc'  => $warningDesc,
                'chart'         => $chart,
                'next_high'     => [
                    'value' => $nextHighVal,
                    'time'  => $nextHighTime,
                ],
            ];
        }

        return $result;
    }

    /**
     * Membangun titik grafik untuk lokasi dan tanggal terkini.
     */
    private function buildChartPoints(Location $loc, ?Prediction $prediction): array
    {
        // Coba cari data per jam dari TidalData
        $latestDate = TidalData::where('location_id', $loc->id)->max('record_date');

        if ($latestDate) {
            $hourly = TidalData::where('location_id', $loc->id)
                ->where('record_date', $latestDate)
                ->orderBy('hour')
                ->get();

            if ($hourly->isNotEmpty()) {
                $points = [];
                foreach ($hourly as $row) {
                    $time = sprintf('%02d:00', $row->hour);
                    // Ketinggian dalam satuan meter (water_level di DB tersimpan dalam cm)
                    $points[] = [
                        'time'  => $time,
                        'value' => round($row->water_level / 100, 2),
                    ];
                }

                return [
                    'labels' => ['00:00', '06:00', '12:00', '18:00', '24:00'],
                    'points' => $points,
                ];
            }
        }

        // Jika tidak ada data per jam, gunakan titik prediksi pasang & surut
        if ($prediction) {
            $highTime = str_replace([' WIB', '.'], ['', ':'], $prediction->high_tide_time);
            $lowTime  = str_replace([' WIB', '.'], ['', ':'], $prediction->low_tide_time);

            return [
                'labels' => ['00:00', '06:00', '12:00', '18:00', '24:00'],
                'points' => [
                    ['time' => '00:00', 'value' => round(($prediction->high_tide_level + $prediction->low_tide_level) / 2, 2)],
                    ['time' => $highTime ?: '06:00', 'value' => (float) $prediction->high_tide_level],
                    ['time' => '12:00', 'value' => round(($prediction->high_tide_level + $prediction->low_tide_level) / 2, 2)],
                    ['time' => $lowTime ?: '18:00',  'value' => (float) $prediction->low_tide_level],
                    ['time' => '24:00', 'value' => round(($prediction->high_tide_level + $prediction->low_tide_level) / 2, 2)],
                ],
            ];
        }

        // Fallback titik dummy
        return [
            'labels' => ['00:00', '06:00', '12:00', '18:00', '24:00'],
            'points' => [
                ['time' => '05:20', 'value' => 2.8],
                ['time' => '11:30', 'value' => 0.6],
                ['time' => '17:40', 'value' => 2.7],
                ['time' => '23:50', 'value' => 0.5],
            ],
        ];
    }

    /**
     * Data fallback statis jika database benar-benar kosong.
     */
    private function fallbackLocations(): array
    {
        return [
            'surabaya-timur' => [
                'id'           => 1,
                'slug'         => 'surabaya-timur',
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
        ];
    }

    /**
     * GET /dashboard
     * GET /dashboard?lokasi=kalianget
     */
    public function index(Request $request)
    {
        $locations = $this->buildLocationsData();

        $firstKey  = array_key_first($locations) ?? 'surabaya-timur';
        $selected  = $request->query('lokasi', $firstKey);

        if (! array_key_exists($selected, $locations)) {
            $selected = $firstKey;
        }

        $active = $locations[$selected];

        // Cari notifikasi aktif umum atau spesifik untuk lokasi yang dipilih
        $activeNotification = Notification::where('is_active', true)
            ->where(function ($q) use ($active) {
                $q->whereNull('location_id');
                if (isset($active['id'])) {
                    $q->orWhere('location_id', $active['id']);
                }
            })
            ->latest()
            ->first();

        // Jika request menginginkan JSON (untuk Polling Realtime)
        if ($request->wantsJson() || $request->ajax() || $request->query('format') === 'json') {
            return response()->json([
                'status'       => 'success',
                'timestamp'    => now()->toIso8601String(),
                'selected'     => $selected,
                'active'       => $active,
                'notification' => $activeNotification ? [
                    'id'      => $activeNotification->id,
                    'title'   => $activeNotification->title,
                    'message' => $activeNotification->message,
                    'type'    => $activeNotification->type,
                ] : null,
            ]);
        }

        return view('dashboard', [
            'locations'    => $locations,
            'selected'     => $selected,
            'active'       => $active,
            'notification' => $activeNotification,
        ]);
    }

    /**
     * Endpoint API Real-Time untuk auto polling dari user frontend
     * GET /dashboard/realtime?lokasi=surabaya-timur
     */
    public function realtime(Request $request)
    {
        $locations = $this->buildLocationsData();
        $firstKey  = array_key_first($locations) ?? 'surabaya-timur';
        $selected  = $request->query('lokasi', $firstKey);

        if (! array_key_exists($selected, $locations)) {
            $selected = $firstKey;
        }

        $active = $locations[$selected];

        $activeNotification = Notification::where('is_active', true)
            ->where(function ($q) use ($active) {
                $q->whereNull('location_id');
                if (isset($active['id'])) {
                    $q->orWhere('location_id', $active['id']);
                }
            })
            ->latest()
            ->first();

        return response()->json([
            'status'       => 'success',
            'timestamp'    => now()->toIso8601String(),
            'selected'     => $selected,
            'active'       => $active,
            'notification' => $activeNotification ? [
                'id'      => $activeNotification->id,
                'title'   => $activeNotification->title,
                'message' => $activeNotification->message,
                'type'    => $activeNotification->type,
            ] : null,
        ]);
    }
}
