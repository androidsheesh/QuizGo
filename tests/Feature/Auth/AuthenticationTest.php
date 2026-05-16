<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_second_device_login_requires_confirmation(): void
    {
        $user = User::factory()->create([
            'session_token' => 'active-session-token',
        ]);

        $this->post('/signin', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertSessionHas('confirm_other_device', true);

        $this->assertGuest();
        $this->assertSame('active-session-token', $user->fresh()->session_token);
    }

    public function test_confirmed_second_device_login_replaces_the_active_session(): void
    {
        $user = User::factory()->create([
            'session_token' => 'active-session-token',
        ]);

        $this->post('/signin', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->post('/signin', [
            'email' => $user->email,
            'confirm_other_device' => '1',
        ])->assertRedirect('/home');

        $this->assertAuthenticatedAs($user);
        $this->assertNotSame('active-session-token', $user->fresh()->session_token);
    }

    public function test_logout_from_an_old_session_does_not_clear_the_current_session_token(): void
    {
        $user = User::factory()->create([
            'session_token' => 'current-session-token',
        ]);

        $this->actingAs($user)
            ->withSession(['session_token' => 'old-session-token'])
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertSame('current-session-token', $user->fresh()->session_token);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
