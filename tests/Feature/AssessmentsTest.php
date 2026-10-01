<?php

namespace Tests\Feature;

use App\Enums\AssessmentType;
use App\Enums\EnrollmentStatus;
use App\Enums\Role;
use App\Models\Assessment;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $group = Group::factory()->create();

        $this->get(route('groups.assessments.index', $group))->assertRedirect(route('login'));
    }

    public function test_teachers_can_create_assessments_for_their_own_groups(): void
    {
        $teacher = Teacher::factory()->create();
        $group = Group::factory()->create(['teacher_id' => $teacher->id]);

        $response = $this->actingAs($teacher->user)->post(route('groups.assessments.store', $group), [
            'name' => 'Quiz 1',
            'type' => AssessmentType::Quiz->value,
            'max_score' => 20,
            'assessment_date' => '2026-10-10',
        ]);

        $assessment = Assessment::where('group_id', $group->id)->firstOrFail();
        $response->assertRedirect(route('groups.assessments.show', [$group, $assessment]));
    }

    public function test_teachers_cannot_touch_other_groups_assessments(): void
    {
        $teacher = Teacher::factory()->create();
        $otherGroup = Group::factory()->create();
        $otherAssessment = Assessment::factory()->create(['group_id' => $otherGroup->id]);

        $this->actingAs($teacher->user)->get(route('groups.assessments.index', $otherGroup))->assertForbidden();
        $this->actingAs($teacher->user)->post(route('groups.assessments.store', $otherGroup), [
            'name' => 'Sneaky Quiz',
            'type' => AssessmentType::Quiz->value,
            'max_score' => 20,
            'assessment_date' => '2026-10-10',
        ])->assertForbidden();
        $this->actingAs($teacher->user)->get(route('groups.assessments.show', [$otherGroup, $otherAssessment]))->assertForbidden();
    }

    public function test_teacher_can_record_grades_for_own_group(): void
    {
        $teacher = Teacher::factory()->create();
        $group = Group::factory()->create(['teacher_id' => $teacher->id]);
        $assessment = Assessment::factory()->create(['group_id' => $group->id, 'max_score' => 20]);
        $first = Enrollment::factory()->create(['group_id' => $group->id, 'status' => EnrollmentStatus::Active]);
        $second = Enrollment::factory()->create(['group_id' => $group->id, 'status' => EnrollmentStatus::Active]);

        $response = $this->actingAs($teacher->user)->put(route('groups.assessments.results.update', [$group, $assessment]), [
            'records' => [
                ['enrollment_id' => $first->id, 'score' => 18, 'feedback' => 'Great work.'],
                ['enrollment_id' => $second->id, 'score' => 15],
            ],
        ]);

        $response->assertRedirect(route('groups.assessments.show', [$group, $assessment]));
        $this->assertDatabaseHas('assessment_results', [
            'assessment_id' => $assessment->id,
            'enrollment_id' => $first->id,
            'score' => 18,
        ]);
        $this->assertSame(2, $assessment->results()->count());
    }

    public function test_resubmission_updates_grades_without_duplicating(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        $assessment = Assessment::factory()->create(['group_id' => $group->id, 'max_score' => 20]);
        $enrollment = Enrollment::factory()->create(['group_id' => $group->id, 'status' => EnrollmentStatus::Active]);

        $payload = fn (int $score): array => [
            'records' => [
                ['enrollment_id' => $enrollment->id, 'score' => $score],
            ],
        ];

        $this->actingAs($manager)->put(route('groups.assessments.results.update', [$group, $assessment]), $payload(12));
        $this->actingAs($manager)->put(route('groups.assessments.results.update', [$group, $assessment]), $payload(17));

        $this->assertSame(1, $assessment->results()->count());
        $this->assertSame('17.00', $assessment->results()->first()->score);
    }

    public function test_scores_above_maximum_are_rejected(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        $assessment = Assessment::factory()->create(['group_id' => $group->id, 'max_score' => 20]);
        $enrollment = Enrollment::factory()->create(['group_id' => $group->id, 'status' => EnrollmentStatus::Active]);

        $response = $this->actingAs($manager)->put(route('groups.assessments.results.update', [$group, $assessment]), [
            'records' => [
                ['enrollment_id' => $enrollment->id, 'score' => 25],
            ],
        ]);

        $response->assertSessionHasErrors('records.0.score');
        $this->assertSame(0, $assessment->results()->count());
    }

    public function test_outsider_enrollments_are_rejected(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        $otherGroup = Group::factory()->create();
        $assessment = Assessment::factory()->create(['group_id' => $group->id, 'max_score' => 20]);
        $outsider = Enrollment::factory()->create(['group_id' => $otherGroup->id, 'status' => EnrollmentStatus::Active]);
        $cancelled = Enrollment::factory()->create(['group_id' => $group->id, 'status' => EnrollmentStatus::Cancelled]);

        $response = $this->actingAs($manager)->put(route('groups.assessments.results.update', [$group, $assessment]), [
            'records' => [
                ['enrollment_id' => $outsider->id, 'score' => 10],
                ['enrollment_id' => $cancelled->id, 'score' => 10],
            ],
        ]);

        $response->assertSessionHasErrors('records.0.enrollment_id');
        $this->assertSame(0, $assessment->results()->count());
    }

    public function test_assessment_show_displays_average_and_results(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        $assessment = Assessment::factory()->create(['group_id' => $group->id, 'max_score' => 20]);
        $first = Enrollment::factory()->create(['group_id' => $group->id, 'status' => EnrollmentStatus::Active]);
        $second = Enrollment::factory()->create(['group_id' => $group->id, 'status' => EnrollmentStatus::Active]);
        $assessment->results()->create(['enrollment_id' => $first->id, 'score' => 18]);
        $assessment->results()->create(['enrollment_id' => $second->id, 'score' => 14]);

        $response = $this->actingAs($manager)->get(route('groups.assessments.show', [$group, $assessment]));

        $response->assertOk();
        $response->assertSee((string) $assessment->fresh()->averageScore());
        $response->assertSee($first->student->name);
    }

    public function test_deleting_an_assessment_removes_its_results(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        $assessment = Assessment::factory()->create(['group_id' => $group->id]);
        $enrollment = Enrollment::factory()->create(['group_id' => $group->id, 'status' => EnrollmentStatus::Active]);
        $assessment->results()->create(['enrollment_id' => $enrollment->id, 'score' => 10]);

        $this->actingAs($manager)->delete(route('groups.assessments.destroy', [$group, $assessment]))
            ->assertRedirect(route('groups.assessments.index', $group));

        $this->assertDatabaseMissing('assessments', ['id' => $assessment->id]);
        $this->assertDatabaseMissing('assessment_results', ['assessment_id' => $assessment->id]);
    }
}
