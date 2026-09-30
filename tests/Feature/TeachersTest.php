<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\TeacherStatus;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeachersTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('teachers.index'))->assertRedirect(route('login'));
    }

    public function test_managers_can_create_a_teacher_with_login_account(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();

        $response = $this->actingAs($manager)->post(route('teachers.store'), [
            'name' => 'Sara Ahmed',
            'email' => 'sara@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
            'specialization' => 'English',
            'status' => TeacherStatus::Active->value,
        ]);

        $teacher = Teacher::whereHas('user', fn ($query) => $query->where('email', 'sara@example.com'))->firstOrFail();
        $response->assertRedirect(route('teachers.show', $teacher));

        $this->assertSame(Role::Teacher, $teacher->user->role);
        $this->assertTrue($teacher->isActive());
    }

    public function test_teacher_email_must_be_unique(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->actingAs($manager)->post(route('teachers.store'), [
            'name' => 'Duplicate',
            'email' => 'taken@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
            'status' => TeacherStatus::Active->value,
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('teachers', 0);
    }

    public function test_managers_can_update_a_teacher_without_changing_password(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $teacher = Teacher::factory()->create();
        $originalHash = $teacher->user->password;

        $response = $this->actingAs($manager)->put(route('teachers.update', $teacher), [
            'name' => 'Renamed Teacher',
            'email' => $teacher->user->email,
            'specialization' => 'Math',
            'status' => TeacherStatus::Inactive->value,
        ]);

        $response->assertRedirect(route('teachers.show', $teacher));

        $fresh = $teacher->fresh();
        $this->assertSame('Renamed Teacher', $fresh->user->name);
        $this->assertSame($originalHash, $fresh->user->password);
        $this->assertFalse($fresh->isActive());
    }

    public function test_deleting_a_teacher_removes_the_login_account(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $teacher = Teacher::factory()->create();
        $userId = $teacher->user_id;

        $this->actingAs($manager)->delete(route('teachers.destroy', $teacher))
            ->assertRedirect(route('teachers.index'));

        $this->assertDatabaseMissing('teachers', ['id' => $teacher->id]);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }
}
