<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Prediction;
use App\Models\TidalData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Halaman Download Pasang Surut (sisi USER).
 *
 * Tabel membaca tabel `predictions` (diisi admin lewat Kelola Data).
 * Grafik mengikuti data yang sama:
 *  - 24 Jam : data per jam dari `tidal_data` (cm -> meter). Kalau data per jam
 *             belum ada, kurva diperkirakan dari titik pasang & surut di `predictions`.
 *  - 7 Hari : pasang & surut harian dari `predictions`.
 * Grafik menampilkan lokasi dan tanggal dari baris PERTAMA hasil tabel.
 */
class DownloadController extends Controller
{
    private const PER_PAGE = 5;

    /** Periode pasang surut semi-diurnal (jam) untuk memperkirakan kurva. */
    private const TIDE_PERIOD = 12.42;

    /** Lokasi aktif untuk dropdown filter dan pin di peta. */
    private function locations(): array
    {
        return Location::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function (Location $loc) {
                $status = $loc->predictions()
                    ->orderByDesc('record_date')
                    ->orderByDesc('id')
                    ->value('status');

                return [
                    'slug'   => strtolower($loc->code),
                    'label'  => $loc->name,
                    'lat'    => $loc->latitude,
                    'lng'    => $loc->longitude,
                    'status' => strtolower($status ?? 'Aman'),
                ];
            })
            ->all();
    }

    /** Terima hanya format YYYY-MM-DD (dari input type="date"), selain itu diabaikan. */
    private function validDate(mixed $value): ?string
    {
        return (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) ? $value : null;
    }

    /** Susun query dari filter di URL (dipakai tabel, grafik, dan export CSV). */
    private function buildQuery(Request $request): Builder
    {
        $query = Prediction::with('location');

        $lokasi = $request->query('lokasi');
        if (is_string($lokasi) && $lokasi !== '') {
            $query->whereHas('location', fn ($q) => $q->where('code', strtoupper($lokasi)));
        }

        $status = $request->query('status');
        if (is_string($status) && $status !== '' && $status !== 'semua') {
            $query->where('status', ucfirst(strtolower($status)));
        }

        if ($mulai = $this->validDate($request->query('tanggal_mulai'))) {
            $query->whereDate('record_date', '>=', $mulai);
        }

        if ($akhir = $this->validDate($request->query('tanggal_akhir'))) {
            $query->whereDate('record_date', '<=', $akhir);
        }

        if ($request->query('urutkan', 'terkini') === 'terlama') {
            $query->orderBy('record_date')->orderBy('location_id');
        } else {
            $query->orderByDesc('record_date')->orderBy('location_id');
        }

        return $query;
    }

    /** Ubah 1 baris database menjadi array yang dipakai view. */
    private function toRow(Prediction $p, int $no): array
    {
        return [
            'no'            => $no,
            'lokasi'        => $p->location->name ?? '-',
            'lokasi_slug'   => strtolower($p->location->code ?? ''),
            'tanggal'       => $p->record_date->locale('id')->translatedFormat('d F Y'),
            'jam_pasang'    => $p->high_tide_time,
            'tinggi_pasang' => $p->high_tide_level,
            'jam_surut'     => $p->low_tide_time,
            'tinggi_surut'  => $p->low_tide_level,
            'status'        => strtolower($p->status),
            'update'        => $p->updated_at
                ? $p->updated_at->copy()->timezone('Asia/Jakarta')->format('H.i') . ' WIB'
                : '-',
        ];
    }

    // ------------------------------------------------------------------
    //  GRAFIK
    // ------------------------------------------------------------------

    /** "05.00 WIB" atau "05:15" -> 5.0 / 5.25 (jam desimal), null kalau tidak terbaca. */
    private function timeToHours(?string $time): ?float
    {
        if ($time && preg_match('/(\d{1,2})[.:](\d{2})/', $time, $m)) {
            return round((int) $m[1] + ((int) $m[2]) / 60, 2);
        }

        return null;
    }

    /** 5.25 -> "05.15 WIB" */
    private function formatTime(float $hours): string
    {
        $minutes = (int) round($hours * 60);

        return sprintf('%02d.%02d WIB', intdiv($minutes, 60), $minutes % 60);
    }

    /**
     * Kurva halus yang PASTI melewati titik pasang & surut dari tabel.
     * Titik-titik ekstrem diulang tiap periode pasang surut, lalu di antara
     * dua titik ekstrem dihubungkan dengan interpolasi cosinus.
     * Ini perkiraan, bukan data pengukuran per jam.
     */
    private function estimateCurve(float $tHigh, float $hi, float $tLow, float $lo): array
    {
        $period = self::TIDE_PERIOD;

        // Jarak dari pasang ke surut berikutnya (0 s/d periode)
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

        // Titik tiap 15 menit + titik ekstrem yang jatuh di dalam 00:00-24:00
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

        // Surut yang ditandai = titik surut di dalam 00:00-24:00 yang paling dekat dengan jam di tabel
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

    /** Data untuk grafik (24 jam dan 7 hari) berdasarkan 1 baris prediksi. */
    private function buildChart(?Prediction $anchor): ?array
    {
        if (! $anchor || ! $anchor->location || ! $anchor->record_date) {
            return null;
        }

        $date = $anchor->record_date;

        // ---- 24 jam ----
        $hourly = TidalData::where('location_id', $anchor->location_id)
            ->whereDate('record_date', $date->toDateString())
            ->orderBy('hour')
            ->get();

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
        } else {
            $tHigh = $this->timeToHours($anchor->high_tide_time);
            $tLow  = $this->timeToHours($anchor->low_tide_time);

            if ($tHigh !== null && $tLow !== null) {
                $source   = 'estimated';
                $hi       = (float) $anchor->high_tide_level;
                $lo       = (float) $anchor->low_tide_level;
                $estimate = $this->estimateCurve($tHigh, $hi, $tLow, $lo);
                $points   = $estimate['points'];
                $high     = ['h' => $tHigh, 'v' => $hi];
                $low      = ['h' => $estimate['lowTime'], 'v' => $lo];
            }
        }

        if ($high !== null) {
            $high['time'] = $this->formatTime($high['h']);
        }
        if ($low !== null) {
            $low['time'] = $this->formatTime($low['h']);
        }

        // ---- penanda "tinggi saat ini" (hanya kalau tanggalnya hari ini, WIB) ----
        $now     = now('Asia/Jakarta');
        $isToday = $date->toDateString() === $now->toDateString();

        // ---- 7 hari (sampai tanggal grafik) ----
        $week = Prediction::where('location_id', $anchor->location_id)
            ->whereDate('record_date', '<=', $date->toDateString())
            ->whereDate('record_date', '>=', $date->copy()->subDays(6)->toDateString())
            ->orderBy('record_date')
            ->get()
            ->map(fn (Prediction $p) => [
                'd'  => $p->record_date->format('d/m'),
                'hi' => (float) $p->high_tide_level,
                'lo' => (float) $p->low_tide_level,
            ])
            ->all();

        return [
            'location'  => $anchor->location->name,
            'date'      => $date->copy()->locale('id')->translatedFormat('d F Y'),
            'weekStart' => $date->copy()->subDays(6)->locale('id')->translatedFormat('d F'),
            'isToday'   => $isToday,
            'nowH'      => $isToday ? round($now->hour + $now->minute / 60, 2) : null,
            'nowTime'   => $isToday ? $now->format('H.i') . ' WIB' : null,
            'source'    => $source,   // hourly | estimated | none
            'points'    => $points,
            'high'      => $high,
            'low'       => $low,
            'week'      => $week,
        ];
    }

    // ------------------------------------------------------------------

    public function index(Request $request)
    {
        $query = $this->buildQuery($request);

        $perPage    = self::PER_PAGE;
        $totalRows  = (clone $query)->count();
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        $page       = min(max(1, (int) $request->query('page', 1)), $totalPages);

        // Nomor urut berlanjut antar halaman: hal 1 = 1-5, hal 2 = 6-10, dst.
        $rows = $query->forPage($page, $perPage)->get()
            ->values()
            ->map(fn (Prediction $p, int $i) => $this->toRow($p, ($page - 1) * $perPage + $i + 1))
            ->all();

        // Grafik mengikuti baris pertama hasil tabel (lokasi + tanggal)
        $chart = $this->buildChart((clone $query)->first());

        return view('download', [
            'rows'       => $rows,
            'locations'  => $this->locations(),
            'chart'      => $chart,
            'page'       => $page,
            'totalPages' => $totalPages,
            'totalRows'  => $totalRows,
            'perPage'    => $perPage,
            'filters'    => $request->only(['lokasi', 'tanggal_mulai', 'tanggal_akhir', 'urutkan', 'status', 'zona', 'datum']),
        ]);
    }

    /** Export seluruh data yang sedang difilter (bukan cuma 1 halaman) sebagai CSV. */
    public function export(Request $request): StreamedResponse
    {
        $items    = $this->buildQuery($request)->get();
        $filename = 'pasang-surut-' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($items) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['No', 'Lokasi', 'Tanggal', 'Jam Pasang', 'Tinggi Pasang (m)', 'Jam Surut', 'Tinggi Surut (m)', 'Status', 'Update']);

            foreach ($items as $i => $p) {
                $r = $this->toRow($p, $i + 1);

                fputcsv($out, [
                    $r['no'], $r['lokasi'], $r['tanggal'], $r['jam_pasang'],
                    $r['tinggi_pasang'], $r['jam_surut'], $r['tinggi_surut'],
                    ucfirst($r['status']), $r['update'],
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}