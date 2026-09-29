<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Prediction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PredictionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = Location::all()->keyBy('code');

        if ($locations->isEmpty()) {
            return;
        }

        // 25 data sampel (5 hari x 5 lokasi)
        // Hari utama: 08 Juli 2026 (sesuai screenshot Figma)
        $dates = [
            '2026-07-08',
            '2026-07-07',
            '2026-07-06',
            '2026-07-05',
            '2026-07-04',
        ];

        // Template konfigurasi per lokasi
        $locTemplates = [
            'SBYTIM' => [
                'high_time' => '01.00 WIB',
                'high_level' => 0.81,
                'low_time' => '12.00 WIB',
                'low_level' => -0.81,
                'status' => 'Aman',
            ],
            'SBYBRT' => [
                'high_time' => '00.15 WIB',
                'high_level' => 0.82,
                'low_time' => '12.24 WIB',
                'low_level' => -0.19,
                'status' => 'Aman',
            ],
            'SBYPLB' => [
                'high_time' => '00.30 WIB',
                'high_level' => 0.83,
                'low_time' => '13.25 WIB',
                'low_level' => -0.10,
                'status' => 'Aman',
            ],
            'KAL' => [
                'high_time' => '02.10 WIB',
                'high_level' => 0.79,
                'low_time' => '14.15 WIB',
                'low_level' => -0.25,
                'status' => 'Aman',
            ],
            'BWI' => [
                'high_time' => '01.45 WIB',
                'high_level' => 0.85,
                'low_time' => '13.50 WIB',
                'low_level' => -0.30,
                'status' => 'Aman',
            ],
        ];

        foreach ($dates as $index => $date) {
            foreach ($locTemplates as $code => $tpl) {
                if (!isset($locations[$code])) {
                    continue;
                }

                $loc = $locations[$code];
                $variation = $index * 0.02; // sedikit variasi per hari

                Prediction::updateOrCreate(
                    [
                        'location_id' => $loc->id,
                        'record_date' => $date,
                    ],
                    [
                        'high_tide_time' => $tpl['high_time'],
                        'high_tide_level' => round($tpl['high_level'] + $variation, 2),
                        'low_tide_time' => $tpl['low_time'],
                        'low_tide_level' => round($tpl['low_level'] - $variation, 2),
                        'status' => $tpl['status'],
                        'updated_at' => Carbon::parse("{$date} 09:00:00")->addMinutes(rand(5, 120)),
                    ]
                );
            }
        }
    }
}
