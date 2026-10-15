<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationTest extends TestCase
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

    public function test_admin_can_view_notifications()
    {
        Notification::create([
            'title' => 'Peringatan Gelombang Tinggi',
            'message' => 'Tinggi gelombang mencapai 2.5 meter di Selat Madura.',
            'type' => 'Peringatan',
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.notif'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Notifikasi');
        $response->assertSee('Peringatan Gelombang Tinggi');
    }

    public function test_admin_can_create_notification()
    {
        $payload = [
            'title' => 'Informasi Pasang Normal',
            'type' => 'Informasi',
            'message' => 'Kondisi pasang surut air laut terpantau normal.',
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.notif.store'), $payload);

        $response->assertRedirect(route('admin.notif'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('notifications', [
            'title' => 'Informasi Pasang Normal',
            'type' => 'Informasi',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_update_notification()
    {
        $notif = Notification::create([
            'title' => 'Judul Awal',
            'type' => 'Informasi',
            'message' => 'Pesan awal',
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $updatePayload = [
            'title' => 'Judul Diperbarui',
            'type' => 'Peringatan',
            'message' => 'Pesan telah diperbarui oleh admin.',
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.notif.update', $notif->id), $updatePayload);

        $response->assertRedirect(route('admin.notif'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('notifications', [
            'id' => $notif->id,
            'title' => 'Judul Diperbarui',
            'type' => 'Peringatan',
        ]);
    }

    public function test_admin_can_toggle_notification_status()
    {
        $notif = Notification::create([
            'title' => 'Status Toggle Test',
            'type' => 'Informasi',
            'message' => 'Test toggle',
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.notif.toggle', $notif->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('notifications', [
            'id' => $notif->id,
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_notification()
    {
        $notif = Notification::create([
            'title' => 'Notif Hapus Test',
            'type' => 'Informasi',
            'message' => 'Test hapus',
            'is_active' => false,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.notif.delete', $notif->id));

        $response->assertRedirect(route('admin.notif'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('notifications', [
            'id' => $notif->id,
        ]);
    }

    public function test_admin_can_filter_notifications_by_tab_and_search()
    {
        Notification::create([
            'title' => 'Info Pemeliharaan Server',
            'message' => 'Server maintenance berkala',
            'type' => 'Informasi',
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        Notification::create([
            'title' => 'Peringatan Pasang Rob',
            'message' => 'Banjir rob di pesisir utara',
            'type' => 'Peringatan',
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        // Filter tab Peringatan
        $respPeringatan = $this->actingAs($this->admin)->get(route('admin.notif', ['tab' => 'peringatan']));
        $respPeringatan->assertSee('Peringatan Pasang Rob');
        $respPeringatan->assertDontSee('Info Pemeliharaan Server');

        // Filter search
        $respSearch = $this->actingAs($this->admin)->get(route('admin.notif', ['search' => 'maintenance']));
        $respSearch->assertSee('Info Pemeliharaan Server');
        $respSearch->assertDontSee('Peringatan Pasang Rob');
    }
}
