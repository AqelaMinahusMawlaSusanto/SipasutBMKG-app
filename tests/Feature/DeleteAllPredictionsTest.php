<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Location;
use App\Models\Prediction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DeleteAllPredictionsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Location $loc1;
    private Location $loc2;

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

        $this->loc1 = Location::create([
            'name' => 'Surabaya Timur',
            'code' => 'SBYTIM',
            'latitude' => -7.24,
            'longitude' => 112.75,
            'is_active' => true,
        ]);

        $this->loc2 = Location::create([
            'name' => 'Kalianget',
            'code' => 'KAL',
            'latitude' => -7.05,
            'longitude' => 113.92,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_delete_all_predictions()
    {
        // Buat 5 data prediksi
        for ($i = 1; $i <= 3; $i++) {
            Prediction::create([
                'location_id' => $this->loc1->id,
                'record_date' => "2026-07-0{$i}",
                'high_tide_time' => '10.00 WIB',
                'high_tide_level' => 2.5,
                'low_tide_time' => '16.00 WIB',
                'low_tide_level' => 0.5,
                'status' => 'Aman',
            ]);
        }
        for ($i = 1; $i <= 2; $i++) {
            Prediction::create([
                'location_id' => $this->loc2->id,
                'record_date' => "2026-07-0{$i}",
                'high_tide_time' => '11.00 WIB',
                'high_tide_level' => 2.8,
                'low_tide_time' => '17.00 WIB',
                'low_tide_level' => 0.4,
                'status' => 'Waspada',
            ]);
        }

        $this->assertEquals(5, Prediction::count());

        $response = $this->actingAs($this->admin)->delete(route('admin.data.destroyAll'));

        $response->assertRedirect(route('admin.data'));
        $response->assertSessionHas('success');
        $this->assertEquals(0, Prediction::count());

        // Verifikasi log aktivitas tercatat
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'delete_all_predictions',
        ]);
    }

    public function test_admin_can_delete_predictions_filtered_by_location()
    {
        Prediction::create([
            'location_id' => $this->loc1->id,
            'record_date' => '2026-07-01',
            'high_tide_time' => '10.00 WIB',
            'high_tide_level' => 2.5,
            'low_tide_time' => '16.00 WIB',
            'low_tide_level' => 0.5,
            'status' => 'Aman',
        ]);
        Prediction::create([
            'location_id' => $this->loc2->id,
            'record_date' => '2026-07-01',
            'high_tide_time' => '11.00 WIB',
            'high_tide_level' => 2.8,
            'low_tide_time' => '17.00 WIB',
            'low_tide_level' => 0.4,
            'status' => 'Waspada',
        ]);

        $this->assertEquals(2, Prediction::count());

        // Hapus hanya loc1
        $response = $this->actingAs($this->admin)->delete(route('admin.data.destroyAll'), [
            'location_id' => $this->loc1->id,
        ]);

        $response->assertRedirect(route('admin.data'));
        $response->assertSessionHas('success');
        $this->assertEquals(1, Prediction::count());
        $this->assertDatabaseMissing('predictions', ['location_id' => $this->loc1->id]);
        $this->assertDatabaseHas('predictions', ['location_id' => $this->loc2->id]);
    }
}
