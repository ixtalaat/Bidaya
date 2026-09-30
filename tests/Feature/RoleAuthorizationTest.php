<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'role:manager'])->get('/_test/manager-area', fn () => 'ok');
        Route::middleware(['web', 'role:manager,super_admin'])->get('/_test/shared-area', fn () => 'ok');
        Route::middleware(['web', 'role:unknown_role'])->get('/_test/unknown-area', fn () => 'ok');
    }

    public function test_guests_are_redirected_to_login_on_role_protected_routes(): void
    {
        $response = $this->get('/_test/manager-area');

        $response->assertRedirect(route('login'));
    }

    public function test_users_with_the_required_role_can_access_the_route(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();

        $response = $this->actingAs($manager)->get('/_test/manager-area');

        $response->assertOk();
    }

    public function test_users_without_the_required_role_receive_forbidden(): void
    {
        $teacher = User::factory()->withRole(Role::Teacher)->create();

        $response = $this->actingAs($teacher)->get('/_test/manager-area');

        $response->assertForbidden();
    }

    public function test_any_listed_role_can_access_shared_routes(): void
    {
        $superAdmin = User::factory()->withRole(Role::SuperAdmin)->create();
        $teacher = User::factory()->withRole(Role::Teacher)->create();

        $this->actingAs($superAdmin)->get('/_test/shared-area')->assertOk();
        $this->actingAs($teacher)->get('/_test/shared-area')->assertForbidden();
    }

    public function test_unknown_role_slugs_deny_access(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();

        $response = $this->actingAs($manager)->get('/_test/unknown-area');

        $response->assertForbidden();
    }

    public function test_manage_center_gate_matches_managers_but_not_teachers(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $teacher = User::factory()->withRole(Role::Teacher)->create();

        $this->assertTrue(Gate::forUser($manager)->allows('manage-center'));
        $this->assertTrue(Gate::forUser($teacher)->denies('manage-center'));
    }

    public function test_new_users_default_to_the_parent_role(): void
    {
        $user = User::create([
            'name' => 'New User',
            'email' => 'new-user@example.com',
            'password' => 'password',
        ]);

        $this->assertSame(Role::Parent, $user->fresh()->role);
    }
}
