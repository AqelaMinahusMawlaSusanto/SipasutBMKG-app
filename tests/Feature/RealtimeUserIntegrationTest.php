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
}
