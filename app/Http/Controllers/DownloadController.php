<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Prediction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    /**
     * Mengambil daftar lokasi untuk filter dropdown.
     */
    private function baseLocations(): array
    {
        $locations = Location::where('is_active', true)->get();

        if ($locations->isEmpty()) {
            return [
                ['slug' => 'surabaya-timur',      'label' => 'Surabaya Timur',      'lat' => -7.2498, 'lng' => 112.7501],
                ['slug' => 'surabaya-barat',      'label' => 'Surabaya Barat',      'lat' => -7.2650, 'lng' => 112.6900],
                ['slug' => 'surabaya-pelabuhan',  'label' => 'Surabaya Pelabuhan',  'lat' => -7.2050, 'lng' => 112.7350],
                ['slug' => 'kalianget',           'label' => 'Kalianget',           'lat' => -7.0500, 'lng' => 113.9200],
                ['slug' => 'banyuwangi',          'label' => 'Banyuwangi',          'lat' => -8.2192, 'lng' => 114.3691],
            ];
        }

        return $locations->map(function ($loc) {
            return [
                'id'    => $loc->id,
                'slug'  => DashboardController::slugifyLocation($loc),
                'label' => $loc->name,
                'lat'   => (float) $loc->latitude,
                'lng'   => (float) $loc->longitude,
            ];
        })->toArray();
    }

    /**
     * Mengambil kumpulan baris data dari database predictions.
     */
    private function allRows(): array
    {
        $predictions = Prediction::with('location')
            ->orderBy('record_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        if ($predictions->isEmpty()) {
            return $this->fallbackRows();
        }

        $rows = [];
        $no = 1;

        foreach ($predictions as $pred) {
            $loc = $pred->location;
            $slug = $loc ? DashboardController::slugifyLocation($loc) : 'surabaya-timur';
            $label = $loc ? $loc->name : 'Surabaya Timur';

            // Format tanggal Indonesia
            $dateStr = $pred->record_date ? Carbon::parse($pred->record_date)->translatedFormat('d F Y') : '-';
            $rawDate = $pred->record_date ? Carbon::parse($pred->record_date)->format('Y-m-d') : null;

            $updateTime = $pred->updated_at ? $pred->updated_at->format('H.i') . ' WIB' : '10.00 WIB';

            $rows[] = [
                'no'            => $no++,
                'lokasi'        => $label,
                'lokasi_slug'   => $slug,
                'tanggal'       => $dateStr,
                'raw_date'      => $rawDate,
                'jam_pasang'    => $pred->high_tide_time,
                'tinggi_pasang' => (float) $pred->high_tide_level,
                'jam_surut'     => $pred->low_tide_time,
                'tinggi_surut'  => (float) $pred->low_tide_level,
                'status'        => strtolower($pred->status),
                'update'        => $updateTime,
            ];
        }

        return $rows;
    }

    /**
     * Fallback baris dummy jika database belum memiliki baris prediksi.
     */
    private function fallbackRows(): array
    {
        $dummyDates = ['08 Juli 2026', '01 Juli 2026', '08 Juni 2026', '15 Juni 2026', '01 Mei 2026'];
        $base = [
            ['slug' => 'surabaya-timur',      'label' => 'Surabaya Timur',     'jam_pasang' => '01.00 WIB', 'tinggi_pasang' => 0.61,  'jam_surut' => '12.00 WIB', 'tinggi_surut' => -0.91, 'status' => 'aman',    'update' => '10.00 WIB'],
            ['slug' => 'surabaya-barat',      'label' => 'Surabaya Barat',     'jam_pasang' => '03.15 WIB', 'tinggi_pasang' => 0.62,  'jam_surut' => '12.45 WIB', 'tinggi_surut' => 0.13,  'status' => 'aman',    'update' => '11.00 WIB'],
            ['slug' => 'surabaya-pelabuhan',  'label' => 'Surabaya Pelabuhan', 'jam_pasang' => '03.30 WIB', 'tinggi_pasang' => 0.63,  'jam_surut' => '13.25 WIB', 'tinggi_surut' => 0.10,  'status' => 'aman',    'update' => '12.00 WIB'],
            ['slug' => 'kalianget',           'label' => 'Kalianget',          'jam_pasang' => '04.20 WIB', 'tinggi_pasang' => 0.64,  'jam_surut' => '13.25 WIB', 'tinggi_surut' => -0.98, 'status' => 'waspada', 'update' => '13.00 WIB'],
            ['slug' => 'banyuwangi',          'label' => 'Banyuwangi',         'jam_pasang' => '05.15 WIB', 'tinggi_pasang' => 0.65,  'jam_surut' => '13.55 WIB', 'tinggi_surut' => -0.02, 'status' => 'bahaya',  'update' => '14.00 WIB'],
        ];

        $rows = [];
        $no = 1;
        foreach ($dummyDates as $tanggal) {
            foreach ($base as $loc) {
                $rows[] = [
                    'no'            => $no++,
                    'lokasi'        => $loc['label'],
                    'lokasi_slug'   => $loc['slug'],
                    'tanggal'       => $tanggal,
                    'raw_date'      => null,
                    'jam_pasang'    => $loc['jam_pasang'],
                    'tinggi_pasang' => $loc['tinggi_pasang'],
                    'jam_surut'     => $loc['jam_surut'],
                    'tinggi_surut'  => $loc['tinggi_surut'],
                    'status'        => $loc['status'],
                    'update'        => $loc['update'],
                ];
            }
        }

        return $rows;
    }

    /** Terapkan filter dari query string ke kumpulan baris */
    private function applyFilters(array $rows, Request $request): array
    {
        $lokasi = $request->query('lokasi');
        $status = $request->query('status');
        $urutkan = $request->query('urutkan', 'terkini');
        $tglMulai = $request->query('tanggal_mulai');
        $tglAkhir = $request->query('tanggal_akhir');

        if ($lokasi) {
            $rows = array_values(array_filter($rows, fn ($r) => $r['lokasi_slug'] === $lokasi));
        }

        if ($status && $status !== 'semua') {
            $rows = array_values(array_filter($rows, fn ($r) => strtolower($r['status']) === strtolower($status)));
        }

        if ($tglMulai) {
            $rows = array_values(array_filter($rows, function ($r) use ($tglMulai) {
                return $r['raw_date'] ? ($r['raw_date'] >= $tglMulai) : true;
            }));
        }

        if ($tglAkhir) {
            $rows = array_values(array_filter($rows, function ($r) use ($tglAkhir) {
                return $r['raw_date'] ? ($r['raw_date'] <= $tglAkhir) : true;
            }));
        }

        if ($urutkan === 'terlama') {
            $rows = array_reverse($rows);
        }

        // Re-index nomor urut
        $num = 1;
        foreach ($rows as &$item) {
            $item['no'] = $num++;
        }

        return $rows;
    }

    public function index(Request $request)
    {
        $rows = $this->applyFilters($this->allRows(), $request);

        // Pagination manual (5 baris per halaman) supaya cocok dengan desain
        $perPage = 5;
        $page = max(1, (int) $request->query('page', 1));
        $totalRows = count($rows);
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        $page = min($page, $totalPages);
        $paged = array_slice($rows, ($page - 1) * $perPage, $perPage);

        return view('download', [
            'rows'        => $paged,
            'locations'   => $this->baseLocations(),
            'page'        => $page,
            'totalPages'  => $totalPages,
            'totalRows'   => $totalRows,
            'perPage'     => $perPage,
            'filters'     => $request->only(['lokasi', 'tanggal_mulai', 'tanggal_akhir', 'urutkan', 'status', 'zona', 'datum']),
        ]);
    }

    /** Export data (yang sedang difilter) sebagai CSV */
    public function export(Request $request): StreamedResponse
    {
        $rows = $this->applyFilters($this->allRows(), $request);

        $filename = 'pasang-surut-' . now()->format('Y-m-d_His') . '.csv';

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'Lokasi', 'Tanggal', 'Jam Pasang', 'Tinggi Pasang (m)', 'Jam Surut', 'Tinggi Surut (m)', 'Status', 'Update']);

            foreach ($rows as $r) {
                fputcsv($handle, [
                    $r['no'], $r['lokasi'], $r['tanggal'], $r['jam_pasang'],
                    $r['tinggi_pasang'], $r['jam_surut'], $r['tinggi_surut'],
                    ucfirst($r['status']), $r['update'],
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}