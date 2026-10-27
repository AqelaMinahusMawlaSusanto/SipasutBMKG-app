<?php

namespace App\Support;

use App\Models\Location;
use App\Models\Prediction;
use App\Models\TidalData;
use Carbon\Carbon;

/**
 * Membangun data grafik pasang surut 24 jam untuk satu lokasi.
 *
 * Sumber data (diisi admin lewat Kelola Data):
 *  1. `tidal_data`  : tinggi air per jam (cm) -> dipakai kalau ada.
 *  2. `predictions` : satu pasang + satu surut per hari -> kurvanya DIPERKIRAKAN
 *                     (halus, dan pasti melewati titik pasang/surut di tabel).
 *  3. Tidak ada dua-duanya -> grafik kosong (tidak ada titik contoh).
 *
 * Tanggal yang ditampilkan: hari ini (WIB) kalau ada datanya; kalau tidak,
 * tanggal terdekat yang punya data (utamakan yang sudah lewat).
 */
class TideChart
{
    /** Periode pasang surut semi-diurnal (jam). */
    private const PERIOD = 12.42;

    /** Bentuk data grafik kosong. */
    public static function emptyChart(): array
    {
        return [
            'date'      => null,
            'dateLabel' => null,
            'isToday'   => false,
            'nowH'      => null,
            'nowTime'   => null,
            'source'    => 'none',   // hourly | estimated | none
            'points'    => [],
            'high'      => null,
            'low'       => null,
        ];
    }

    public static function forLocation(Location $loc): array
    {
        $now   = now('Asia/Jakarta');
        $today = $now->toDateString();

        $date = self::pickDate(self::availableDates($loc->id), $today);
        if ($date === null) {
            return self::emptyChart();
        }

        $hourly = TidalData::where('location_id', $loc->id)
            ->whereDate('record_date', $date)
            ->orderBy('hour')
            ->get();

        $prediction = Prediction::where('location_id', $loc->id)
            ->whereDate('record_date', $date)
            ->orderByDesc('id')
            ->first();

        $points = [];
        $high   = null;
        $low    = null;
        $source = 'none';

        if ($hourly->count() >= 2) {
            $source = 'hourly';
            foreach ($hourly as $row) {
                $p = ['h' => (float) $row->hour, 'v' => round($row->water_level / 100, 2)]; // cm -> meter
                $points[] = $p;
                if ($high === null || $p['v'] > $high['v']) {
                    $high = $p;
                }
                if ($low === null || $p['v'] < $low['v']) {
                    $low = $p;
                }
            }
        } elseif ($prediction) {
            $tHigh = self::timeToHours($prediction->high_tide_time);
            $tLow  = self::timeToHours($prediction->low_tide_time);

            if ($tHigh !== null && $tLow !== null) {
                $source   = 'estimated';
                $hi       = (float) $prediction->high_tide_level;
                $lo       = (float) $prediction->low_tide_level;
                $estimate = self::estimateCurve($tHigh, $hi, $tLow, $lo);
                $points   = $estimate['points'];
                $high     = ['h' => $tHigh, 'v' => $hi];
                $low      = ['h' => $estimate['lowTime'], 'v' => $lo];
            }
        }

        if ($high !== null) {
            $high['time'] = self::formatTime($high['h']);
        }
        if ($low !== null) {
            $low['time'] = self::formatTime($low['h']);
        }

        $isToday = $date === $today;

        return [
            'date'      => $date,
            'dateLabel' => Carbon::parse($date)->locale('id')->translatedFormat('d F Y'),
            'isToday'   => $isToday,
            'nowH'      => $isToday ? round($now->hour + $now->minute / 60, 2) : null,
            'nowTime'   => $isToday ? $now->format('H.i') . ' WIB' : null,
            'source'    => $source,
            'points'    => $points,
            'high'      => $high,
            'low'       => $low,
        ];
    }

    /** Semua tanggal (Y-m-d, urut naik) yang punya data per jam atau prediksi untuk lokasi ini. */
    private static function availableDates(int $locationId): array
    {
        $hourly = TidalData::where('location_id', $locationId)
            ->selectRaw('DATE(record_date) as d')
            ->distinct()
            ->pluck('d')
            ->all();

        $daily = Prediction::where('location_id', $locationId)
            ->selectRaw('DATE(record_date) as d')
            ->distinct()
            ->pluck('d')
            ->all();

        $all = array_values(array_unique(array_merge($hourly, $daily)));
        sort($all);

        return $all;
    }

    /** Hari ini kalau ada; kalau tidak, tanggal terakhir yang sudah lewat; kalau semuanya di masa depan, yang paling awal. */
    private static function pickDate(array $dates, string $today): ?string
    {
        if (empty($dates)) {
            return null;
        }

        $past = array_filter($dates, fn ($d) => $d <= $today);

        return $past ? max($past) : min($dates);
    }

    /** "05.00 WIB" atau "05:15" -> 5.0 / 5.25 (jam desimal), null kalau tidak terbaca. */
    private static function timeToHours(?string $time): ?float
    {
        if ($time && preg_match('/(\d{1,2})[.:](\d{2})/', $time, $m)) {
            return round((int) $m[1] + ((int) $m[2]) / 60, 2);
        }

        return null;
    }

    /** 5.25 -> "05.15 WIB" */
    private static function formatTime(float $hours): string
    {
        $minutes = (int) round($hours * 60);

        return sprintf('%02d.%02d WIB', intdiv($minutes, 60), $minutes % 60);
    }

    /**
     * Kurva halus yang PASTI melewati titik pasang & surut dari tabel
     * (interpolasi cosinus antar titik ekstrem yang diulang tiap periode).
     * Ini perkiraan, bukan data pengukuran per jam.
     */
    private static function estimateCurve(float $tHigh, float $hi, float $tLow, float $lo): array
    {
        $period = self::PERIOD;

        $s = fmod(($tLow - $tHigh) + 4 * $period, $period);
        if ($s < 0.5 || $s > $period - 0.5) {
            $s = $period / 2;
        }

        $extremes = [];
        $lowTimes = [];
        for ($k = -3; $k <= 3; $k++) {
            $th = $tHigh + $k * $period;
            $extremes[] = [$th, $hi];
            $extremes[] = [$th + $s, $lo];
            $lowTimes[] = $th + $s;
        }
        usort($extremes, fn ($a, $b) => $a[0] <=> $b[0]);

        $valueAt = function (float $t) use ($extremes) {
            $n = count($extremes);
            for ($i = 0; $i < $n - 1; $i++) {
                if ($t >= $extremes[$i][0] && $t <= $extremes[$i + 1][0]) {
                    [$t1, $v1] = $extremes[$i];
                    [$t2, $v2] = $extremes[$i + 1];
                    $f = ($t - $t1) / ($t2 - $t1);

                    return $v1 + ($v2 - $v1) * (1 - cos(M_PI * $f)) / 2;
                }
            }

            return $extremes[0][1];
        };

        $grid = [];
        for ($t = 0.0; $t <= 24.0001; $t += 0.25) {
            $grid[] = round($t, 2);
        }
        foreach ($extremes as [$te]) {
            if ($te >= 0 && $te <= 24) {
                $grid[] = round($te, 2);
            }
        }
        $grid = array_values(array_unique($grid));
        sort($grid);

        $points = [];
        foreach ($grid as $t) {
            $points[] = ['h' => (float) $t, 'v' => round($valueAt((float) $t), 2)];
        }

        $lowTime = round($tLow, 2);
        $best = null;
        foreach ($lowTimes as $lt) {
            if ($lt >= 0 && $lt <= 24 && ($best === null || abs($lt - $tLow) < abs($best - $tLow))) {
                $best = $lt;
            }
        }
        if ($best !== null) {
            $lowTime = round($best, 2);
        }

        return ['points' => $points, 'lowTime' => $lowTime];
    }
}