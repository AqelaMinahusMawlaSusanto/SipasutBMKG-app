<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Prediction;
use Illuminate\Http\Request;

class KalenderController extends Controller
{
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
    // Sumber data = tabel predictions (yang dikelola admin di /admin/data)
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

        $rows = Prediction::where('location_id', $lokasi->id)
            ->whereYear('record_date', $request->tahun)
            ->whereMonth('record_date', $request->bulan)
            ->get();

        $hasil = [];
        foreach ($rows as $row) {
            $hasil[$row->record_date->format('Y-m-d')] = [
                'jam_pasang'    => $row->high_tide_time,
                'tinggi_pasang' => $this->formatMeter($row->high_tide_level),
                'jam_surut'     => $row->low_tide_time,
                'tinggi_surut'  => $this->formatMeter($row->low_tide_level),
                'status'        => $row->status,
            ];
        }

        return response()->json($hasil);
    }

    // high_tide_level / low_tide_level sudah dalam meter di tabel predictions
    private function formatMeter($m): string
    {
        return number_format((float) $m, 2, ',', '.') . ' m';
    }
}