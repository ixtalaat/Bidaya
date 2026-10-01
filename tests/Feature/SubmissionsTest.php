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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_linked_parents_can_submit_for_their_children(): void
    {
        $parentUser = User::factory()->withRole(Role::Parent)->create();
        $guardian = Guardian::factory()->create(['user_id' => $parentUser->id]);
        $student = Student::factory()->create();
        $guardian->students()->attach($student, ['relationship' => 'father']);
        $group = Group::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'status' => EnrollmentStatus::Active,
        ]);
        $assignment = Assignment::factory()->create([
            'group_id' => $group->id,
            'status' => AssignmentStatus::Published,
            'due_date' => today()->addWeek()->toDateString(),
        ]);

        $response = $this->actingAs($parentUser)->post(
            route('groups.assignments.submissions.store', [$group, $assignment]),
            ['enrollment_id' => $enrollment->id, 'answer' => 'Page 12 done.']
        );

        $response->assertRedirect(route('groups.assignments.show', [$group, $assignment]));
        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $assignment->id,
            'enrollment_id' => $enrollment->id,
            'status' => SubmissionStatus::Submitted->value,
            'submitted_by' => $parentUser->id,
        ]);
    }

    public function test_students_can_submit_for_themselves(): void
    {
        $studentUser = User::factory()->withRole(Role::Student)->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id]);
        $group = Group::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'status' => EnrollmentStatus::Active,
        ]);
        $assignment = Assignment::factory()->create([
            'group_id' => $group->id,
            'status' => AssignmentStatus::Published,
            'due_date' => today()->addWeek()->toDateString(),
        ]);

        $this->actingAs($studentUser)->post(
            route('groups.assignments.submissions.store', [$group, $assignment]),
            ['enrollment_id' => $enrollment->id, 'answer' => 'My answer.']
        )->assertRedirect(route('groups.assignments.show', [$group, $assignment]));

        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $assignment->id,
            'enrollment_id' => $enrollment->id,
        ]);
    }

    public function test_unrelated_parents_cannot_submit(): void
    {
        $parentUser = User::factory()->withRole(Role::Parent)->create();
        Guardian::factory()->create(['user_id' => $parentUser->id]);
        $group = Group::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'group_id' => $group->id,
            'status' => EnrollmentStatus::Active,
        ]);
        $assignment = Assignment::factory()->create([
            'group_id' => $group->id,
            'status' => AssignmentStatus::Published,
        ]);

        $this->actingAs($parentUser)->post(
            route('groups.assignments.submissions.store', [$group, $assignment]),
            ['enrollment_id' => $enrollment->id, 'answer' => 'Sneaky.']
        )->assertForbidden();

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_closed_assignments_reject_submissions(): void
    {
        $parentUser = User::factory()->withRole(Role::Parent)->create();
        $guardian = Guardian::factory()->create(['user_id' => $parentUser->id]);
        $student = Student::factory()->create();
        $guardian->students()->attach($student, ['relationship' => 'father']);
        $group = Group::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'status' => EnrollmentStatus::Active,
        ]);
        $assignment = Assignment::factory()->create([
            'group_id' => $group->id,
            'status' => AssignmentStatus::Closed,
        ]);

        $this->actingAs($parentUser)->post(
            route('groups.assignments.submissions.store', [$group, $assignment]),
            ['enrollment_id' => $enrollment->id, 'answer' => 'Too late.']
        )->assertSessionHasErrors('assignment');

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_late_submissions_are_flagged(): void
    {
        $parentUser = User::factory()->withRole(Role::Parent)->create();
        $guardian = Guardian::factory()->create(['user_id' => $parentUser->id]);
        $student = Student::factory()->create();
        $guardian->students()->attach($student, ['relationship' => 'father']);
        $group = Group::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'status' => EnrollmentStatus::Active,
        ]);
        $assignment = Assignment::factory()->create([
            'group_id' => $group->id,
            'status' => AssignmentStatus::Published,
            'due_date' => today()->subDay()->toDateString(),
        ]);

        $this->actingAs($parentUser)->post(
            route('groups.assignments.submissions.store', [$group, $assignment]),
            ['enrollment_id' => $enrollment->id, 'answer' => 'Late work.']
        )->assertRedirect(route('groups.assignments.show', [$group, $assignment]));

        $this->assertSame(
            SubmissionStatus::Late,
            $assignment->submissions()->firstOrFail()->status
        );
    }

    public function test_resubmission_updates_and_resets_the_grade(): void
    {
        Storage::fake('public');
        $parentUser = User::factory()->withRole(Role::Parent)->create();
        $guardian = Guardian::factory()->create(['user_id' => $parentUser->id]);
        $student = Student::factory()->create();
        $guardian->students()->attach($student, ['relationship' => 'father']);
        $group = Group::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'status' => EnrollmentStatus::Active,
        ]);
        $assignment = Assignment::factory()->create([
            'group_id' => $group->id,
            'status' => AssignmentStatus::Published,
            'due_date' => today()->addWeek()->toDateString(),
        ]);
        $submission = $assignment->submissions()->create([
            'enrollment_id' => $enrollment->id,
            'answer' => 'First try.',
            'submitted_by' => $parentUser->id,
            'submitted_at' => now(),
            'score' => 8,
            'status' => SubmissionStatus::Reviewed,
        ]);

        $this->actingAs($parentUser)->put(
            route('groups.assignments.submissions.update', [$group, $assignment, $submission]),
            [
                'answer' => 'Improved try.',
                'file' => UploadedFile::fake()->create('homework.pdf', 100, 'application/pdf'),
            ]
        )->assertRedirect(route('groups.assignments.show', [$group, $assignment]));

        $fresh = $submission->fresh();
        $this->assertSame(1, $assignment->submissions()->count());
        $this->assertSame('Improved try.', $fresh->answer);
        $this->assertNull($fresh->score);
        $this->assertSame(SubmissionStatus::Submitted, $fresh->status);
        Storage::disk('public')->assertExists($fresh->file_path);
    }

    public function test_teachers_can_grade_with_max_score_enforced(): void
    {
        $teacher = Teacher::factory()->create();
        $group = Group::factory()->create(['teacher_id' => $teacher->id]);
        $assignment = Assignment::factory()->create(['group_id' => $group->id, 'max_score' => 10]);
        $enrollment = Enrollment::factory()->create([
            'group_id' => $group->id,
            'status' => EnrollmentStatus::Active,
        ]);

        $this->actingAs($teacher->user)->put(
            route('groups.assignments.grades.update', [$group, $assignment]),
            ['records' => [['enrollment_id' => $enrollment->id, 'score' => 15]]]
        )->assertSessionHasErrors('records.0.score');

        $this->actingAs($teacher->user)->put(
            route('groups.assignments.grades.update', [$group, $assignment]),
            ['records' => [['enrollment_id' => $enrollment->id, 'score' => 9, 'feedback' => 'Good.']]]
        )->assertRedirect(route('groups.assignments.show', [$group, $assignment]));

        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $assignment->id,
            'enrollment_id' => $enrollment->id,
            'score' => 9,
            'status' => SubmissionStatus::Reviewed->value,
        ]);
    }
}
