<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\Role;
use App\Enums\SessionStatus;
use App\Enums\StudentStatus;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Session;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_record_attendance_for_own_session(): void
    {
        $teacher = Teacher::factory()->create();
        $group = Group::factory()->create(['teacher_id' => $teacher->id]);
        $session = Session::factory()->create([
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
            'status' => SessionStatus::Scheduled,
        ]);
        $first = Enrollment::factory()->create(['group_id' => $group->id, 'status' => EnrollmentStatus::Active]);
        $second = Enrollment::factory()->create(['group_id' => $group->id, 'status' => EnrollmentStatus::Active]);

        $response = $this->actingAs($teacher->user)->put(route('sessions.attendance.update', $session), [
            'records' => [
                ['enrollment_id' => $first->id, 'status' => AttendanceStatus::Present->value],
                ['enrollment_id' => $second->id, 'status' => AttendanceStatus::Absent->value],
            ],
        ]);

        $response->assertRedirect(route('sessions.show', $session));
        $this->assertDatabaseHas('attendances', [
            'session_id' => $session->id,
            'enrollment_id' => $first->id,
            'status' => AttendanceStatus::Present->value,
        ]);
        $this->assertSame(2, $session->attendances()->count());
    }

    public function test_resubmission_updates_instead_of_duplicating(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        $session = Session::factory()->create([
            'group_id' => $group->id,
            'teacher_id' => $group->teacher_id,
            'status' => SessionStatus::Scheduled,
        ]);
        $enrollment = Enrollment::factory()->create(['group_id' => $group->id, 'status' => EnrollmentStatus::Active]);

        $payload = fn (AttendanceStatus $status): array => [
            'records' => [
                ['enrollment_id' => $enrollment->id, 'status' => $status->value],
            ],
        ];

        $this->actingAs($manager)->put(route('sessions.attendance.update', $session), $payload(AttendanceStatus::Absent));
        $this->actingAs($manager)->put(route('sessions.attendance.update', $session), $payload(AttendanceStatus::Present));

        $this->assertSame(1, $session->attendances()->count());
        $this->assertSame(AttendanceStatus::Present, $session->attendances()->first()->status);
    }

    public function test_teachers_cannot_record_for_other_teachers_sessions(): void
    {
        $teacher = Teacher::factory()->create();
        $otherSession = Session::factory()->create(['status' => SessionStatus::Scheduled]);
        $enrollment = Enrollment::factory()->create([
            'group_id' => $otherSession->group_id,
            'status' => EnrollmentStatus::Active,
        ]);

        $this->actingAs($teacher->user)->get(route('sessions.attendance.edit', $otherSession))->assertForbidden();
        $this->actingAs($teacher->user)->put(route('sessions.attendance.update', $otherSession), [
            'records' => [
                ['enrollment_id' => $enrollment->id, 'status' => AttendanceStatus::Present->value],
            ],
        ])->assertForbidden();
    }

    public function test_cancelled_sessions_reject_attendance(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        $session = Session::factory()->create([
            'group_id' => $group->id,
            'teacher_id' => $group->teacher_id,
            'status' => SessionStatus::Cancelled,
        ]);
        $enrollment = Enrollment::factory()->create(['group_id' => $group->id, 'status' => EnrollmentStatus::Active]);

        $response = $this->actingAs($manager)->put(route('sessions.attendance.update', $session), [
            'records' => [
                ['enrollment_id' => $enrollment->id, 'status' => AttendanceStatus::Present->value],
            ],
        ]);

        $response->assertSessionHasErrors('session');
        $this->assertSame(0, $session->attendances()->count());
    }

    public function test_completed_sessions_lock_teachers_but_not_managers(): void
    {
        $teacher = Teacher::factory()->create();
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create(['teacher_id' => $teacher->id]);
        $session = Session::factory()->create([
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
            'status' => SessionStatus::Completed,
        ]);
        $enrollment = Enrollment::factory()->create(['group_id' => $group->id, 'status' => EnrollmentStatus::Active]);

        $payload = [
            'records' => [
                ['enrollment_id' => $enrollment->id, 'status' => AttendanceStatus::Present->value],
            ],
        ];

        $this->actingAs($teacher->user)->put(route('sessions.attendance.update', $session), $payload)
            ->assertSessionHasErrors('session');

        $this->actingAs($manager)->put(route('sessions.attendance.update', $session), $payload)
            ->assertRedirect(route('sessions.show', $session));

        $this->assertSame(1, $session->attendances()->count());
    }

    public function test_only_active_group_enrollments_are_accepted(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        $otherGroup = Group::factory()->create();
        $session = Session::factory()->create([
            'group_id' => $group->id,
            'teacher_id' => $group->teacher_id,
            'status' => SessionStatus::Scheduled,
        ]);
        $outsider = Enrollment::factory()->create(['group_id' => $otherGroup->id, 'status' => EnrollmentStatus::Active]);
        $cancelled = Enrollment::factory()->create(['group_id' => $group->id, 'status' => EnrollmentStatus::Cancelled]);

        $response = $this->actingAs($manager)->put(route('sessions.attendance.update', $session), [
            'records' => [
                ['enrollment_id' => $outsider->id, 'status' => AttendanceStatus::Present->value],
                ['enrollment_id' => $cancelled->id, 'status' => AttendanceStatus::Present->value],
            ],
        ]);

        $response->assertSessionHasErrors('records');
        $this->assertSame(0, $session->attendances()->count());
    }

    public function test_attendance_sheet_lists_only_active_enrollments(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        $session = Session::factory()->create([
            'group_id' => $group->id,
            'teacher_id' => $group->teacher_id,
            'status' => SessionStatus::Scheduled,
        ]);
        $active = Enrollment::factory()->create([
            'group_id' => $group->id,
            'student_id' => Student::factory()->create(['name' => 'Active Kid', 'status' => StudentStatus::Active])->id,
            'status' => EnrollmentStatus::Active,
        ]);
        $cancelled = Enrollment::factory()->create([
            'group_id' => $group->id,
            'student_id' => Student::factory()->create(['name' => 'Gone Kid', 'status' => StudentStatus::Active])->id,
            'status' => EnrollmentStatus::Cancelled,
        ]);

        $response = $this->actingAs($manager)->get(route('sessions.attendance.edit', $session));

        $response->assertOk();
        $response->assertSee('Active Kid');
        $response->assertDontSee('Gone Kid');
    }
}
