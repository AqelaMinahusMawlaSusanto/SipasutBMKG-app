<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Notification;
use App\Models\TidalData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // 2. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@sipasut.id'],
            [
                'name' => 'Administrator BMKG',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );
        $admin->assignRole($adminRole);

        // 3. Normal User
        $user = User::firstOrCreate(
            ['email' => 'user@sipasut.id'],
            [
                'name' => 'Masyarakat & Nelayan',
                'password' => Hash::make('user123'),
                'role' => 'user',
            ]
        );
        $user->assignRole($userRole);

        // 4. 5 Titik Lokasi Pengamatan Pasang Surut Jawa Timur
        $locations = [
            [
                'name' => 'Surabaya Timur',
                'code' => 'SBYTIM',
                'latitude' => -7.2458000,
                'longitude' => 112.7988000,
                'description' => 'Titik pantau pasang surut wilayah perairan pesisir Surabaya Timur & Kenjeran.',
                'institution' => 'BMKG Stasiun Meteorologi Maritim Perak Surabaya',
            ],
            [
                'name' => 'Surabaya Barat',
                'code' => 'SBYBRT',
                'latitude' => -7.1929000,
                'longitude' => 112.6500000,
                'description' => 'Titik pantau alur pelayaran barat Surabaya & Teluk Lamong.',
                'institution' => 'BMKG Stasiun Meteorologi Maritim Perak Surabaya',
            ],
            [
                'name' => 'Surabaya Pelabuhan (Tanjung Perak)',
                'code' => 'SBYPLB',
                'latitude' => -7.2059000,
                'longitude' => 112.7343000,
                'description' => 'Stasiun utama pasang surut Pelabuhan Tanjung Perak Surabaya.',
                'institution' => 'BMKG Stasiun Meteorologi Maritim Perak Surabaya',
            ],
            [
                'name' => 'Kalianget, Sumenep',
                'code' => 'KAL',
                'latitude' => -7.0520000,
                'longitude' => 113.9490000,
                'description' => 'Titik pantau pasang surut Selat Madura bagian timur dan Kepulauan Sumenep.',
                'institution' => 'BMKG Stasiun Meteorologi Maritim Perak Surabaya',
            ],
            [
                'name' => 'Banyuwangi (Ketapang/Selat Bali)',
                'code' => 'BWI',
                'latitude' => -8.2191000,
                'longitude' => 114.3691000,
                'description' => 'Titik pantau pasang surut penyeberangan Selat Bali dan Pantai Ketapang Banyuwangi.',
                'institution' => 'BMKG Stasiun Meteorologi Maritim Perak Surabaya',
            ],
        ];

        foreach ($locations as $locData) {
            $location = Location::updateOrCreate(['code' => $locData['code']], $locData);

            // Generate realistis hourly tidal dataset untuk bulan Juli 2026 (atau bulan berjalan)
            // Sesuai pola semi-diurnal (pagi pasang, sore surut)
            $this->seedSampleTidalData($location);
        }

        // 5. Default Alert Notification
        Notification::updateOrCreate(
            ['title' => 'Peringatan Pasang Maksimum (Rob) Selat Madura'],
            [
                'message' => 'Waspada potensi banjir rob di pesisir Surabaya Timur dan Tanjung Perak akibat pasang maksimum mencapai ketinggian +140 cm.',
                'type' => 'warning',
                'is_active' => true,
                'threshold_value' => 120,
                'created_by' => $admin->id,
            ]
        );
    }

    private function seedSampleTidalData(Location $location): void
    {
        // Bulan Juli 2026 (31 hari)
        $year = 2026;
        $month = 7;
        $daysInMonth = 31;

        // Base offset per location agar ada variasi
        $offset = match ($location->code) {
            'SBYTIM' => 10,
            'SBYBRT' => -5,
            'SBYPLB' => 5,
            'KAL' => 15,
            'BWI' => -10,
            default => 0,
        };

        $records = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);

            for ($hour = 1; $hour <= 24; $hour++) {
                // Formula semi-diurnal pasang surut gelombang sinus ~12.4 jam periode
                // Puncak pasang siang hari (jam 9-11) dan tengah malam (jam 22-01)
                // Puncak surut sore hari (jam 16-18) dan subuh (jam 04-06)
                $radians = ($hour / 12.0) * 2 * M_PI;
                $level = (int) round(100 * sin($radians - 1.2) + 40 * sin(2 * $radians) + $offset + (($day % 7) * 5));

                $records[] = [
                    'location_id' => $location->id,
                    'record_date' => $dateStr,
                    'hour' => $hour,
                    'water_level' => $level,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert in chunks
        foreach (array_chunk($records, 100) as $chunk) {
            TidalData::upsert(
                $chunk,
                ['location_id', 'record_date', 'hour'],
                ['water_level', 'updated_at']
            );
        }
    }
}
