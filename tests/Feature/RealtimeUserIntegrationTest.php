<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Notification;
use App\Models\Prediction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RealtimeUserIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Location $location;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@sipasut.id'],
            ['name' => 'Admin SIPASUT', 'password' => bcrypt('admin123')]
        );

        $role = Role::firstOrCreate(['name' => 'admin']);
        if (!$this->admin->hasRole('admin')) {
            $this->admin->assignRole($role);
        }

        $this->location = Location::create([
            'name' => 'Surabaya Timur',
            'code' => 'SBYTIM',
            'latitude' => -7.2458,
            'longitude' => 112.7988,
            'institution' => 'BMKG Maritim Perak',
            'is_active' => true,
        ]);
    }

    public function test_user_dashboard_reflects_admin_prediction_data()
    {
        // 1. Admin input prediksi baru
        $predictionData = [
            'location_id' => $this->location->id,
            'record_date' => now()->toDateString(),
            'high_tide_time' => '17.30 WIB',
            'high_tide_level' => 3.45,
            'low_tide_time' => '11.00 WIB',
            'low_tide_level' => 0.50,
            'status' => 'Bahaya',
        ];

        $this->actingAs($this->admin)->post(route('admin.data.store'), $predictionData);

        // 2. User membuka dashboard
        $response = $this->get(route('dashboard.index', ['lokasi' => 'surabaya-timur']));

        $response->assertStatus(200);
        $response->assertSee('Surabaya Timur');
        $response->assertSee('BAHAYA');
        $response->assertSee('3.5 m'); // number_format 3.45 -> 3.5 m
    }

    public function test_realtime_api_returns_live_updated_prediction_and_notification()
    {
        // Buat notifikasi aktif oleh admin
        Notification::create([
            'title' => 'Peringatan Rob Ekstrem',
            'message' => 'Air pasang naik 180cm di pesisir.',
            'type' => 'danger',
            'location_id' => $this->location->id,
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        // Buat prediksi
        Prediction::create([
            'location_id' => $this->location->id,
            'record_date' => now()->toDateString(),
            'high_tide_time' => '18.00 WIB',
            'high_tide_level' => 2.9,
            'low_tide_time' => '12.00 WIB',
            'low_tide_level' => 0.4,
            'status' => 'Waspada',
        ]);

        // Request real-time endpoint
        $response = $this->getJson(route('dashboard.realtime', ['lokasi' => 'surabaya-timur']));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'selected' => 'surabaya-timur',
            'active' => [
                'label' => 'Surabaya Timur',
                'status' => 'waspada',
                'status_label' => 'WASPADA',
                'current_tide' => 2.9,
            ],
            'notification' => [
                'title' => 'Peringatan Rob Ekstrem',
                'type' => 'danger',
            ],
        ]);
    }

    public function test_user_lokasi_and_download_pages_load_dynamic_locations()
    {
        $responseLokasi = $this->get(route('lokasi.index'));
        $responseLokasi->assertStatus(200);
        $responseLokasi->assertSee('Surabaya Timur');

        $responseDownload = $this->get(route('download.index'));
        $responseDownload->assertStatus(200);
        $responseDownload->assertSee('Surabaya Timur');
    }

    public function test_admin_manual_prediction_syncs_chart_and_tidal_data()
    {
        $today = now()->toDateString();
        $predictionData = [
            'location_id' => $this->location->id,
            'record_date' => $today,
            'high_tide_time' => '14.00 WIB',
            'high_tide_level' => 2.10,
            'low_tide_time' => '20.00 WIB',
            'low_tide_level' => -0.60,
            'status' => 'Waspada',
        ];

        // 1. Admin simpan data manual
        $this->actingAs($this->admin)->post(route('admin.data.store'), $predictionData);

        // 2. Cek apakah 24 jam tersinkron ke tabel tidal_data
        $this->assertDatabaseCount('tidal_data', 24);
        $this->assertDatabaseHas('tidal_data', [
            'location_id' => $this->location->id,
            'record_date' => $today,
            'hour' => 14,
            'water_level' => 210, // 2.10 m = 210 cm
        ]);

        // 3. User dashboard menampilkan data chart yang akurat
        $response = $this->getJson(route('dashboard.realtime', ['lokasi' => 'surabaya-timur']));
        $response->assertStatus(200);
        $chart = $response->json('active.chart');
        $this->assertEquals('hourly', $chart['source']);
        $this->assertCount(24, $chart['points']);
        $this->assertEquals('14.00 WIB', $chart['high']['time']);
        $this->assertEquals(2.1, $chart['high']['v']);

        // 4. Admin update data prediksi
        $prediction = Prediction::where('location_id', $this->location->id)->first();
        $updateData = array_merge($predictionData, [
            'high_tide_level' => 3.20,
            'status' => 'Bahaya',
        ]);
        $this->actingAs($this->admin)->put(route('admin.data.update', $prediction->id), $updateData);

        // Verifikasi tidal_data dan realtime API terupdate
        $this->assertDatabaseHas('tidal_data', [
            'location_id' => $this->location->id,
            'record_date' => $today,
            'hour' => 14,
            'water_level' => 320, // 3.20 m = 320 cm
        ]);

        $responseUpdated = $this->getJson(route('dashboard.realtime', ['lokasi' => 'surabaya-timur']));
        $this->assertEquals('bahaya', $responseUpdated->json('active.status'));
        $this->assertEquals(3.2, $responseUpdated->json('active.chart.high.v'));
    }
}
