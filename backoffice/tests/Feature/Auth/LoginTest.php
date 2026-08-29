<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('MALEGA');
        $response->assertSee('Backoffice Control Portal');
    }

    public function test_user_can_authenticate_using_login_form_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@malega.id',
            'password' => 'password',
            'role' => UserRole::Admin,
            'is_active' => true,
            'last_login_at' => null,
        ]);

        Livewire::test(Login::class)
            ->set('form.email', 'admin@malega.id')
            ->set('form.password', 'password')
            ->set('form.remember', true)
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_last_login_at_is_updated_on_successful_authentication(): void
    {
        $user = User::factory()->create([
            'email' => 'manager@malega.id',
            'password' => 'password',
            'role' => UserRole::Manager,
            'is_active' => true,
            'last_login_at' => null,
        ]);

        Livewire::test(Login::class)
            ->set('form.email', 'manager@malega.id')
            ->set('form.password', 'password')
            ->call('login')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertNotNull($user->last_login_at);
    }

    public function test_user_cannot_authenticate_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'admin@malega.id',
            'password' => 'password',
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        Livewire::test(Login::class)
            ->set('form.email', 'admin@malega.id')
            ->set('form.password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['form.email']);

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_authenticate(): void
    {
        User::factory()->inactive()->create([
            'email' => 'inactive@malega.id',
            'password' => 'password',
            'role' => UserRole::WarehouseStaff,
        ]);

        Livewire::test(Login::class)
            ->set('form.email', 'inactive@malega.id')
            ->set('form.password', 'password')
            ->call('login')
            ->assertHasErrors(['form.email']);

        $this->assertGuest();
    }

    public function test_unauthenticated_user_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_is_redirected_away_from_login_page(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/');
    }

    public function test_user_can_logout_and_session_is_cleared(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_validation_errors_are_triggered_for_empty_fields(): void
    {
        Livewire::test(Login::class)
            ->set('form.email', '')
            ->set('form.password', '')
            ->call('login')
            ->assertHasErrors(['form.email', 'form.password']);
    }
}
