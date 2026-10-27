<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\TidalData;
use Illuminate\Http\Request;

class KalenderController extends Controller
{
    // slug untuk dropdown, code mengikuti kolom `code` di tabel locations
    private function locations(): array
    {
        return [
            ['slug' => 'surabaya-barat',     'name' => 'Surabaya Barat',     'code' => 'SBYBRT'],
            ['slug' => 'surabaya-timur',     'name' => 'Surabaya Timur',     'code' => 'SBYTIM'],
            ['slug' => 'surabaya-pelabuhan', 'name' => 'Surabaya Pelabuhan', 'code' => 'SBYPLB'],
            ['slug' => 'kalianget',          'name' => 'Kalianget',          'code' => 'KAL'],
            ['slug' => 'banyuwangi',         'name' => 'Banyuwangi',         'code' => 'BWI'],
        ];
    }

    // GET /kalender
    public function index()
    {
        return view('kalender', ['locations' => $this->locations()]);
    }

    // GET /kalender/data?slug=...&tahun=...&bulan=...
    public function data(Request $request)
    {
        $request->validate([
            'slug'  => 'required|string',
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|between:1,12',
        ]);

        $item = collect($this->locations())->firstWhere('slug', $request->slug);
        abort_unless($item, 404);

        $lokasi = Location::where('code', $item['code'])->first();
        if (! $lokasi) {
            return response()->json([]);
        }

        $perTanggal = TidalData::where('location_id', $lokasi->id)
            ->whereYear('record_date', $request->tahun)
            ->whereMonth('record_date', $request->bulan)
            ->orderBy('hour')
            ->get()
            ->groupBy(fn ($r) => $r->record_date->format('Y-m-d'));

        $hasil = [];
        foreach ($perTanggal as $tanggal => $jamJam) {
            $max = $jamJam->max('water_level');
            $min = $jamJam->min('water_level');

            $pasang = $jamJam->firstWhere('water_level', $max); // jam pertama yang mencapai puncak
            $surut  = $jamJam->firstWhere('water_level', $min);

            $hasil[$tanggal] = [
                'jam_pasang'    => $this->formatJam($pasang->hour),
                'tinggi_pasang' => $this->formatMeter($max),
                'jam_surut'     => $this->formatJam($surut->hour),
                'tinggi_surut'  => $this->formatMeter($min),
                'status'        => $this->statusDari($max),
            ];
        }

        return response()->json($hasil);
    }

    private function formatJam(int $jam): string
    {
        return sprintf('%02d.00 WIB', $jam);
    }

    // water_level tersimpan dalam cm, ditampilkan dalam meter
    private function formatMeter(int $cm): string
    {
        return number_format($cm / 100, 2, ',', '.') . ' m';
    }

    // Ganti dengan aturan status yang dipakai halaman admin kalau sudah ada
    private function statusDari(int $cm): string
    {
        if ($cm >= 140) return 'Bahaya';
        if ($cm >= 120) return 'Waspada';
        return 'Aman';
    }
}