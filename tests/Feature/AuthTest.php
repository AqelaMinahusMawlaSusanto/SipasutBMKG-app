<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Login Admin BMKG');
    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Buat Akun Baru Admin BMKG');
    }

    public function test_new_admin_can_register(): void
    {
        $this->seed();

        $response = $this->post('/register', [
            'name' => 'Petugas Baru BMKG',
            'email' => 'petugas.baru@sipasut.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));

        $user = User::where('email', 'petugas.baru@sipasut.id')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_admin_can_login_and_redirect_to_admin_dashboard(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'admin@sipasut.id',
            'password' => 'admin123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_non_admin_user_cannot_access_admin_dashboard_via_spatie(): void
    {
        $this->seed();
        $user = User::where('email', 'user@sipasut.id')->first();

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_user_can_access_admin_dashboard(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@sipasut.id')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Selamat datang, ' . $admin->name);
        $response->assertSee('Total Stasiun');
    }
}
