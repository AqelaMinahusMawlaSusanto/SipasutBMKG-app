<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Prediction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Halaman Download Pasang Surut (sisi USER).
 *
 * Data TIDAK lagi dummy. Tabel di halaman ini membaca tabel `predictions`,
 * yaitu tabel yang sama yang diisi admin lewat menu Kelola Data
 * (AdminController@storePrediksi dan AdminController@storeDataUpload).
 * Jadi begitu admin menambah / meng-upload / menghapus data, halaman ini
 * langsung ikut berubah tanpa perlu menyalin data ke mana pun.
 */
class DownloadController extends Controller
{
    private const PER_PAGE = 5;

    /**
     * Lokasi aktif dari tabel `locations` (dikelola admin di Kelola Lokasi).
     * Dipakai untuk dropdown filter dan pin di peta.
     * Bentuk array sengaja dibuat sama seperti sebelumnya supaya
     * download.blade.php dan download.js tidak perlu diubah.
     */
    private function locations(): array
    {
        return Location::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function (Location $loc) {
                // Warna pin di peta = status prediksi terbaru lokasi itu
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

    /** Susun query dari filter di URL (dipakai tabel dan export CSV). */
    private function buildQuery(Request $request): Builder
    {
        $query = Prediction::with('location');

        $lokasi = $request->query('lokasi');
        if (is_string($lokasi) && $lokasi !== '') {
            $query->whereHas('location', fn ($q) => $q->where('code', strtoupper($lokasi)));
        }

        $status = $request->query('status');
        if (is_string($status) && $status !== '' && $status !== 'semua') {
            // Di database tersimpan "Aman" / "Waspada" / "Bahaya"
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

        return view('download', [
            'rows'       => $rows,
            'locations'  => $this->locations(),
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