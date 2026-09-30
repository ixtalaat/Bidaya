<?php

namespace Tests\Feature;

use App\Enums\RelationshipType;
use App\Enums\Role;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardiansTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('parents.index'))->assertRedirect(route('login'));
    }

    public function test_teachers_cannot_manage_guardians(): void
    {
        $teacher = User::factory()->withRole(Role::Teacher)->create();

        $this->actingAs($teacher)->get(route('parents.index'))->assertForbidden();
        $this->actingAs($teacher)->post(route('parents.store'), [])->assertForbidden();
    }

    public function test_managers_can_list_guardians(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $guardian = Guardian::factory()->create(['name' => 'Ahmed Mohamed']);

        $response = $this->actingAs($manager)->get(route('parents.index'));

        $response->assertOk();
        $response->assertSee('Ahmed Mohamed');
    }

    public function test_managers_can_create_a_guardian(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();

        $response = $this->actingAs($manager)->post(route('parents.store'), [
            'name' => 'Sara Ali',
            'phone' => '01001234567',
            'email' => 'sara@example.com',
        ]);

        $guardian = Guardian::where('phone', '01001234567')->firstOrFail();
        $response->assertRedirect(route('parents.show', $guardian));
        $this->assertDatabaseHas('parents', ['name' => 'Sara Ali']);
    }

    public function test_guardian_phone_must_be_unique(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        Guardian::factory()->create(['phone' => '01001234567']);

        $response = $this->actingAs($manager)->post(route('parents.store'), [
            'name' => 'Duplicate Phone',
            'phone' => '01001234567',
        ]);

        $response->assertSessionHasErrors('phone');
        $this->assertDatabaseCount('parents', 1);
    }

    public function test_guardian_show_displays_linked_children(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $guardian = Guardian::factory()->create();
        $student = Student::factory()->create(['name' => 'Omar Ahmed']);
        $guardian->students()->attach($student, ['relationship' => RelationshipType::Father->value]);

        $response = $this->actingAs($manager)->get(route('parents.show', $guardian));

        $response->assertOk();
        $response->assertSee('Omar Ahmed');
    }

    public function test_managers_can_update_and_delete_a_guardian(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $guardian = Guardian::factory()->create();

        $this->actingAs($manager)->put(route('parents.update', $guardian), [
            'name' => 'Updated Name',
            'phone' => $guardian->phone,
        ])->assertRedirect(route('parents.show', $guardian));

        $this->assertSame('Updated Name', $guardian->fresh()->name);

        $this->actingAs($manager)->delete(route('parents.destroy', $guardian))
            ->assertRedirect(route('parents.index'));

        $this->assertDatabaseMissing('parents', ['id' => $guardian->id]);
    }
}
