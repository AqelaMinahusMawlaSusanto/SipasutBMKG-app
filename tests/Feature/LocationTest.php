<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

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
    }

    public function test_admin_can_view_location_management_page()
    {
        Location::create([
            'name' => 'Surabaya Barat',
            'code' => 'SBYBRT',
            'latitude' => -7.1950,
            'longitude' => 112.7390,
            'institution' => 'BMKG Maritim Perak',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.lokasi'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Lokasi Monitoring');
        $response->assertSee('Surabaya Barat');
        $response->assertSee('SBYBRT');
    }

    public function test_admin_can_create_new_location()
    {
        $payload = [
            'name' => 'Pelabuhan Tanjung Perak',
            'code' => 'SBYPLB',
            'latitude' => -7.1985,
            'longitude' => 112.7312,
            'institution' => 'BMKG Stasiun Meteorologi Maritim Perak Surabaya',
            'description' => 'Titik pantau utama perairan Tanjung Perak',
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.lokasi.store'), $payload);

        $response->assertRedirect(route('admin.lokasi'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('locations', [
            'code' => 'SBYPLB',
            'name' => 'Pelabuhan Tanjung Perak',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_update_location()
    {
        $location = Location::create([
            'name' => 'Banyuwangi Lama',
            'code' => 'BWI',
            'latitude' => -8.2192,
            'longitude' => 114.3692,
            'is_active' => true,
        ]);

        $updatePayload = [
            'name' => 'Pelabuhan Ketapang Banyuwangi',
            'code' => 'BWI',
            'latitude' => -8.1450,
            'longitude' => 114.3980,
            'institution' => 'BMKG Banyuwangi',
            'description' => 'Pembaruan deskripsi lokasi',
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.lokasi.update', $location->id), $updatePayload);

        $response->assertRedirect(route('admin.lokasi'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'name' => 'Pelabuhan Ketapang Banyuwangi',
        ]);
    }

    public function test_admin_can_delete_location()
    {
        $location = Location::create([
            'name' => 'Lokasi Dihapus',
            'code' => 'DEL01',
            'latitude' => -7.0,
            'longitude' => 112.0,
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.lokasi.delete', $location->id));

        $response->assertRedirect(route('admin.lokasi'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('locations', [
            'id' => $location->id,
        ]);
    }
}
