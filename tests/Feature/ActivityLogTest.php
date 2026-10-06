<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityLogTest extends TestCase
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

    public function test_admin_can_view_activity_logs()
    {
        ActivityLog::create([
            'user_id' => $this->admin->id,
            'user_name' => $this->admin->name,
            'user_email' => $this->admin->email,
            'action' => 'login',
            'ip_address' => '127.0.0.1',
            'description' => 'User admin berhasil login ke sistem.',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.aktivitas'));

        $response->assertStatus(200);
        $response->assertSee('Log Aktivitas');
        $response->assertSee('Login Masuk');
        $response->assertSee('127.0.0.1');
    }

    public function test_admin_can_filter_activity_logs_by_action_and_status()
    {
        ActivityLog::create([
            'user_name' => 'Admin SIPASUT',
            'action' => 'create_prediction',
            'description' => 'Menambahkan data prediksi baru',
            'created_at' => now(),
        ]);

        ActivityLog::create([
            'user_name' => 'Admin SIPASUT',
            'action' => 'failed_login',
            'description' => 'Percobaan login gagal dengan password salah',
            'created_at' => now(),
        ]);

        // Filter Sukses
        $responseSuccess = $this->actingAs($this->admin)->get(route('admin.aktivitas', [
            'status' => 'Sukses'
        ]));
        $responseSuccess->assertSee('Menambahkan data prediksi baru');
        $responseSuccess->assertDontSee('Percobaan login gagal');

        // Filter Gagal
        $responseFailed = $this->actingAs($this->admin)->get(route('admin.aktivitas', [
            'status' => 'Gagal'
        ]));
        $responseFailed->assertSee('Percobaan login gagal');
        $responseFailed->assertDontSee('Menambahkan data prediksi baru');
    }

    public function test_admin_can_filter_activity_logs_by_date()
    {
        ActivityLog::create([
            'user_name' => 'Admin SIPASUT',
            'action' => 'login',
            'description' => 'Aktivitas tanggal kemarin',
            'created_at' => now()->subDay(),
        ]);

        ActivityLog::create([
            'user_name' => 'Admin SIPASUT',
            'action' => 'login',
            'description' => 'Aktivitas tanggal hari ini',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.aktivitas', [
            'tanggal' => now()->toDateString()
        ]));

        $response->assertSee('Aktivitas tanggal hari ini');
        $response->assertDontSee('Aktivitas tanggal kemarin');
    }
}
