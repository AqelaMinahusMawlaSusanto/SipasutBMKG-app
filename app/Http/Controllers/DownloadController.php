<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    /**
     * 5 lokasi dasar (nilai jam/tinggi pasang-surut & status masih dummy,
     * sesuai desain Figma). TODO: ganti dengan data asli dari hasil
     * import Google Colab begitu sudah tersedia.
     */
    private function baseLocations(): array
    {
        return [
            ['slug' => 'surabaya-timur',      'label' => 'Surabaya Timur',      'lat' => -7.2498, 'lng' => 112.7501, 'jam_pasang' => '01.00 WIB', 'tinggi_pasang' => 0.61,  'jam_surut' => '12.00 WIB', 'tinggi_surut' => -0.91, 'status' => 'aman',    'update' => '10.00 WIB'],
            ['slug' => 'surabaya-barat',      'label' => 'Surabaya Barat',      'lat' => -7.2650, 'lng' => 112.6900, 'jam_pasang' => '03.15 WIB', 'tinggi_pasang' => 0.62,  'jam_surut' => '12.45 WIB', 'tinggi_surut' => 0.13,  'status' => 'aman',    'update' => '11.00 WIB'],
            ['slug' => 'surabaya-pelabuhan',  'label' => 'Surabaya Pelabuhan',  'lat' => -7.2050, 'lng' => 112.7350, 'jam_pasang' => '03.30 WIB', 'tinggi_pasang' => 0.63,  'jam_surut' => '13.25 WIB', 'tinggi_surut' => 0.10,  'status' => 'aman',    'update' => '12.00 WIB'],
            ['slug' => 'kalianget',           'label' => 'Kalianget',           'lat' => -7.0500, 'lng' => 113.9200, 'jam_pasang' => '04.20 WIB', 'tinggi_pasang' => 0.64,  'jam_surut' => '13.25 WIB', 'tinggi_surut' => -0.98, 'status' => 'waspada', 'update' => '13.00 WIB'],
            ['slug' => 'banyuwangi',          'label' => 'Banyuwangi',          'lat' => -8.2192, 'lng' => 114.3691, 'jam_pasang' => '05.15 WIB', 'tinggi_pasang' => 0.65,  'jam_surut' => '13.55 WIB', 'tinggi_surut' => -0.02, 'status' => 'bahaya',  'update' => '14.00 WIB'],
        ];
    }

    /** 5 tanggal dummy, urutannya sesuai desain (bukan urut kronologis murni) */
    private function dummyDates(): array
    {
        return ['08 Juli 2026', '01 Juli 2026', '08 Juni 2026', '15 Juni 2026', '01 Mei 2026'];
    }

    /** Bangun 25 baris data (5 lokasi x 5 tanggal) */
    private function allRows(): array
    {
        $rows = [];
        $no = 1;

        foreach ($this->dummyDates() as $tanggal) {
            foreach ($this->baseLocations() as $loc) {
                $rows[] = [
                    'no'            => $no++,
                    'lokasi'        => $loc['label'],
                    'lokasi_slug'   => $loc['slug'],
                    'tanggal'       => $tanggal,
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

        if ($lokasi) {
            $rows = array_values(array_filter($rows, fn ($r) => $r['lokasi_slug'] === $lokasi));
        }

        if ($status && $status !== 'semua') {
            $rows = array_values(array_filter($rows, fn ($r) => $r['status'] === $status));
        }

        // Catatan: filter tanggal_mulai / tanggal_akhir belum diterapkan ke data
        // dummy karena tanggalnya masih string acak, bukan objek tanggal asli.
        // Setelah data asli (dari Colab) tersedia dalam kolom tanggal beneran,
        // tinggal tambahkan whereBetween tanggal_mulai & tanggal_akhir di sini.

        if ($urutkan === 'terlama') {
            $rows = array_reverse($rows);
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