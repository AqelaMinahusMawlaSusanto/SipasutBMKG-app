<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $this->seed();

        $response = $this->post('/register', [
            'name' => 'Pengguna Baru BMKG',
            'email' => 'pengguna.baru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('user.dashboard'));
    }

    public function test_user_can_login_and_redirect_to_dashboard(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'user@sipasut.id',
            'password' => 'user123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('user.dashboard'));
    }

    public function test_admin_can_login_and_redirect_to_admin_homepage(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'admin@sipasut.id',
            'password' => 'admin123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.data'));
    }
}
