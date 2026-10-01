<?php

namespace Tests\Feature;

use App\Enums\AssignmentStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\Role;
use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $group = Group::factory()->create();

        $this->get(route('groups.assignments.index', $group))->assertRedirect(route('login'));
    }

    public function test_teachers_can_create_assignments_for_their_own_groups(): void
    {
        $teacher = Teacher::factory()->create();
        $group = Group::factory()->create(['teacher_id' => $teacher->id]);

        $response = $this->actingAs($teacher->user)->post(route('groups.assignments.store', $group), [
            'title' => 'Homework 1',
            'due_date' => today()->addWeek()->toDateString(),
            'max_score' => 10,
            'status' => AssignmentStatus::Draft->value,
        ]);

        $assignment = Assignment::where('group_id', $group->id)->firstOrFail();
        $response->assertRedirect(route('groups.assignments.show', [$group, $assignment]));
    }

    public function test_teachers_cannot_touch_other_groups_assignments(): void
    {
        $teacher = Teacher::factory()->create();
        $otherGroup = Group::factory()->create();
        $otherAssignment = Assignment::factory()->create(['group_id' => $otherGroup->id]);

        $this->actingAs($teacher->user)->get(route('groups.assignments.index', $otherGroup))->assertForbidden();
        $this->actingAs($teacher->user)->post(route('groups.assignments.store', $otherGroup), [
            'title' => 'Sneaky',
            'due_date' => today()->addWeek()->toDateString(),
            'max_score' => 10,
            'status' => AssignmentStatus::Draft->value,
        ])->assertForbidden();
        $this->actingAs($teacher->user)->get(route('groups.assignments.show', [$otherGroup, $otherAssignment]))->assertForbidden();
    }

    public function test_draft_assignments_are_hidden_from_parents(): void
    {
        $parentUser = User::factory()->withRole(Role::Parent)->create();
        $group = Group::factory()->create();
        $student = Student::factory()->create();
        $guardian = Guardian::factory()->create(['user_id' => $parentUser->id]);
        $guardian->students()->attach($student, ['relationship' => 'father']);
        Enrollment::factory()->create([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'status' => EnrollmentStatus::Active,
        ]);
        $draft = Assignment::factory()->create(['group_id' => $group->id, 'status' => AssignmentStatus::Draft]);
        $published = Assignment::factory()->create(['group_id' => $group->id, 'status' => AssignmentStatus::Published]);

        $index = $this->actingAs($parentUser)->get(route('groups.assignments.index', $group));
        $index->assertOk();
        $index->assertSee($published->title);
        $index->assertDontSee($draft->title);

        $this->actingAs($parentUser)->get(route('groups.assignments.show', [$group, $draft]))->assertForbidden();
        $this->actingAs($parentUser)->get(route('groups.assignments.show', [$group, $published]))->assertOk();
    }

    public function test_assignment_status_follows_allowed_transitions(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        $assignment = Assignment::factory()->create(['group_id' => $group->id, 'status' => AssignmentStatus::Draft]);

        $this->actingAs($manager)->put(route('groups.assignments.update', [$group, $assignment]), [
            'title' => $assignment->title,
            'due_date' => $assignment->due_date->toDateString(),
            'max_score' => $assignment->max_score,
            'status' => AssignmentStatus::Published->value,
        ])->assertRedirect(route('groups.assignments.show', [$group, $assignment]));

        $this->assertSame(AssignmentStatus::Published, $assignment->fresh()->status);

        $this->actingAs($manager)->put(route('groups.assignments.update', [$group, $assignment]), [
            'title' => $assignment->title,
            'due_date' => $assignment->due_date->toDateString(),
            'max_score' => $assignment->max_score,
            'status' => AssignmentStatus::Draft->value,
        ])->assertSessionHasErrors('status');
    }

    public function test_deleting_an_assignment_removes_its_submissions(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        $assignment = Assignment::factory()->create(['group_id' => $group->id]);
        $enrollment = Enrollment::factory()->create([
            'group_id' => $group->id,
            'status' => EnrollmentStatus::Active,
        ]);
        $assignment->submissions()->create([
            'enrollment_id' => $enrollment->id,
            'answer' => 'Done.',
            'status' => SubmissionStatus::Submitted,
        ]);

        $this->actingAs($manager)->delete(route('groups.assignments.destroy', [$group, $assignment]))
            ->assertRedirect(route('groups.assignments.index', $group));

        $this->assertDatabaseMissing('assignments', ['id' => $assignment->id]);
        $this->assertDatabaseMissing('submissions', ['assignment_id' => $assignment->id]);
    }
}
