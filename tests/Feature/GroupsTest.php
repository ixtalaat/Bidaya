<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Enums\GroupStatus;
use App\Enums\Role;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('groups.index'))->assertRedirect(route('login'));
    }

    public function test_managers_can_create_a_group(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $course = Course::factory()->create();
        $teacher = Teacher::factory()->create();

        $response = $this->actingAs($manager)->post(route('groups.store'), [
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'name' => 'English Group A',
            'capacity' => 15,
            'start_date' => '2026-10-01',
            'status' => GroupStatus::Active->value,
        ]);

        $group = Group::where('name', 'English Group A')->firstOrFail();
        $response->assertRedirect(route('groups.show', $group));
    }

    public function test_capacity_cannot_drop_below_active_enrollments(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create(['capacity' => 15]);
        Enrollment::factory()->count(3)->create([
            'group_id' => $group->id,
            'status' => EnrollmentStatus::Active,
        ]);

        $response = $this->actingAs($manager)->put(route('groups.update', $group), [
            'course_id' => $group->course_id,
            'teacher_id' => $group->teacher_id,
            'name' => $group->name,
            'capacity' => 2,
            'start_date' => $group->start_date->toDateString(),
            'status' => $group->status->value,
        ]);

        $response->assertSessionHasErrors('capacity');
        $this->assertSame(15, $group->fresh()->capacity);
    }

    public function test_groups_with_active_enrollments_cannot_be_deleted(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        Enrollment::factory()->create([
            'group_id' => $group->id,
            'status' => EnrollmentStatus::Active,
        ]);

        $response = $this->actingAs($manager)->delete(route('groups.destroy', $group));

        $response->assertSessionHasErrors('group');
        $this->assertDatabaseHas('groups', ['id' => $group->id]);
    }

    public function test_group_show_lists_enrolled_students(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        $student = Student::factory()->create(['name' => 'Omar Ahmed']);
        Enrollment::factory()->create([
            'group_id' => $group->id,
            'student_id' => $student->id,
        ]);

        $response = $this->actingAs($manager)->get(route('groups.show', $group));

        $response->assertOk();
        $response->assertSee('Omar Ahmed');
    }
}
