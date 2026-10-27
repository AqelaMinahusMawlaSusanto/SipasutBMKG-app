<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Notification;
use App\Models\Prediction;
use App\Support\TideChart;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Memetakan kode lokasi ke slug URL yang konsisten.
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
     * Membangun data dashboard untuk semua lokasi aktif.
     */
    private function buildLocationsData(): array
    {
        $dbLocations = Location::where('is_active', true)->get();

        if ($dbLocations->isEmpty()) {
            return $this->fallbackLocations();
        }

        $result = [];

        foreach ($dbLocations as $loc) {
            $slug = self::slugifyLocation($loc);

            // Grafik dibangun dari data lokasi ini (per jam, atau perkiraan dari
            // prediksi harian). Kalau tidak ada data sama sekali, grafiknya kosong.
            $chart = TideChart::forLocation($loc);

            // Prediksi yang dipakai untuk kartu/status = prediksi pada TANGGAL YANG SAMA
            // dengan grafik, supaya angka kartu dan grafik konsisten.
            // Kalau tanggal itu tidak punya prediksi, pakai prediksi terbaru.
            $latestPrediction = null;

            if ($chart['date']) {
                $latestPrediction = Prediction::where('location_id', $loc->id)
                    ->whereDate('record_date', $chart['date'])
                    ->orderByDesc('updated_at')
                    ->first();
            }

            $latestPrediction ??= Prediction::where('location_id', $loc->id)
                ->orderBy('record_date', 'desc')
                ->orderBy('updated_at', 'desc')
                ->first();

            // Status kondisi pasang surut.
            $statusRaw = $latestPrediction
                ? strtolower($latestPrediction->status)
                : 'aman';

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

            // Data kondisi saat ini.
            // Nilai 2.3 merupakan nilai tampilan default,
            // bukan data pengukuran aktual.
            $currentTide = $latestPrediction
                ? (float) $latestPrediction->high_tide_level
                : 2.3;

            $currentTime = now()->format('H.i') . ' WIB';

            $lastHeight = $latestPrediction
                ? (float) $latestPrediction->high_tide_level
                : 2.3;

            if ($latestPrediction && $latestPrediction->updated_at) {
                $updatedAt = $latestPrediction->updated_at
                    ->format('d/m/Y, H.i') . ' WIB';
            } else {
                $updatedAt = now()->format('d/m/Y, H.i') . ' WIB';
            }

            // Koordinat lokasi.
            $coord = sprintf(
                '%.4f, %.4f',
                $loc->latitude,
                $loc->longitude
            );

            // Informasi peringatan.
            if ($statusRaw === 'bahaya') {
                $warningTitle = 'Terjadi pasang tinggi.';
                $warningDesc = 'Bahaya pasang sangat tinggi berpotensi menyebabkan banjir pesisir.';
            } elseif ($statusRaw === 'waspada') {
                $warningTitle = 'Kemungkinan terjadinya pasang tinggi.';
                $warningDesc = 'Waspada pasang tinggi yang dapat mempengaruhi aktivitas di pesisir.';
            } else {
                $warningTitle = 'Tidak ada peringatan pasang tinggi.';
                $warningDesc = 'Kondisi aman untuk aktivitas di wilayah perairan.';
            }

            // Pasang tertinggi diambil dari grafik, jadi selalu sama dengan yang tampil di grafik.
            $nextHighTime = $chart['high']['time']
                ?? ($latestPrediction ? $latestPrediction->high_tide_time : '-');

            $nextHighVal = $chart['high']['v']
                ?? ($latestPrediction ? (float) $latestPrediction->high_tide_level : 0.0);

            // Nama kabupaten/wilayah.
            $name = strtolower($loc->name);

            $kabupaten = str_contains($name, 'banyuwangi')
                ? 'Banyuwangi, Jawa Timur'
                : (str_contains($name, 'kalianget')
                    ? 'Sumenep, Jawa Timur'
                    : 'Surabaya, Jawa Timur');

            $result[$slug] = [
                'id'             => $loc->id,
                'slug'           => $slug,
                'code'           => $loc->code,
                'label'          => $loc->name,
                'kabupaten'      => $kabupaten,
                'status'         => $statusRaw,
                'status_label'   => $statusLabel,
                'status_desc'    => $statusDesc,
                'current_tide'   => $currentTide,
                'current_time'   => $currentTime,
                'wind_speed'     => 12,
                'wind_dir'       => 'Timur Laut',
                'weather'        => 'Cerah',
                'temperature'    => 31,
                'updated_at'     => $updatedAt,
                'last_height'    => $lastHeight,
                'coordinate'     => $coord,
                'warning_title'  => $warningTitle,
                'warning_desc'   => $warningDesc,
                'chart'          => $chart,
                'next_high'      => [
                    'value' => $nextHighVal,
                    'time'  => $nextHighTime,
                ],
            ];
        }

        return $result;
    }

    /**
     * Data fallback statis jika tabel lokasi benar-benar kosong.
     *
     * Grafik fallback juga dikosongkan agar tidak menampilkan
     * titik grafik buatan.
     */
    private function fallbackLocations(): array
    {
        return [
            'surabaya-timur' => [
                'id'             => 1,
                'slug'           => 'surabaya-timur',
                'label'          => 'Surabaya Timur',
                'kabupaten'      => 'Surabaya, Jawa Timur',
                'status'         => 'aman',
                'status_label'   => 'AMAN',
                'status_desc'    => 'Kondisi Normal',
                'current_tide'   => 2.3,
                'current_time'   => '15.30 WIB',
                'wind_speed'     => 12,
                'wind_dir'       => 'Timur Laut',
                'weather'        => 'Cerah',
                'temperature'    => 31,
                'updated_at'     => '18/08/2026, 18.30 WIB',
                'last_height'    => 2.3,
                'coordinate'     => '-7.2498, 112.7351',
                'warning_title'  => 'Tidak ada peringatan pasang tinggi.',
                'warning_desc'   => 'Kondisi aman untuk aktivitas di wilayah perairan.',
                'chart'          => TideChart::emptyChart(),
                'next_high'      => [
                    'value' => 0.0,
                    'time'  => '-',
                ],
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

        $firstKey = array_key_first($locations) ?? 'surabaya-timur';
        $selected = $request->query('lokasi', $firstKey);

        if (!array_key_exists($selected, $locations)) {
            $selected = $firstKey;
        }

        $active = $locations[$selected];

        // Cari notifikasi aktif umum atau khusus lokasi terpilih.
        $activeNotification = Notification::where('is_active', true)
            ->where(function ($q) use ($active) {
                $q->whereNull('location_id');

                if (isset($active['id'])) {
                    $q->orWhere('location_id', $active['id']);
                }
            })
            ->latest()
            ->first();

        // Respons JSON untuk polling realtime.
        if (
            $request->wantsJson()
            || $request->ajax()
            || $request->query('format') === 'json'
        ) {
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
     * Endpoint realtime untuk polling frontend.
     * GET /dashboard/realtime?lokasi=surabaya-timur
     */
    public function realtime(Request $request)
    {
        $locations = $this->buildLocationsData();

        $firstKey = array_key_first($locations) ?? 'surabaya-timur';
        $selected = $request->query('lokasi', $firstKey);

        if (!array_key_exists($selected, $locations)) {
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