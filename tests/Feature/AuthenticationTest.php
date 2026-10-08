<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_renders(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee(__('auth.login_title'));
        $response->assertSee(__('auth.parent_hint'));
    }

    public function test_users_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->withRole(Role::Teacher)->create();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_login_requires_email_and_password(): void
    {
        $response = $this->post(route('login'), []);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_five_attempts(): void
    {
        $user = User::factory()->create();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $this->assertNotSame(__('auth.failed'), session('errors')->first('email'));
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect(route('home'));
    }

    public function test_logout_keeps_the_active_locale(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession(['locale' => 'ar'])
            ->actingAs($user)
            ->post(route('logout'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('locale', 'ar');

        $home = $this->get(route('home'));
        $home->assertOk();
        $home->assertSee('<title>بداية', false);
    }

    public function test_guests_cannot_visit_the_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_password_reset_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post(route('password.email'), ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
        $response->assertSessionHas('status');
    }

    public function test_password_can_be_reset_with_a_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
    }

    public function test_locale_switch_persists_and_renders_rtl_layout(): void
    {
        $this->get(route('locale.switch', 'ar'))->assertSessionHas('locale', 'ar');

        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('dir="rtl"', false);
        $response->assertSee(__('auth.login_title'));
    }
}
