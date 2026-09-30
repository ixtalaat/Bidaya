<?php

namespace Tests\Feature;

use App\Enums\CourseStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\GroupStatus;
use App\Enums\InvoiceStatus;
use App\Enums\Role;
use App\Enums\StudentStatus;
use App\Enums\TeacherStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('enrollments.index'))->assertRedirect(route('login'));
    }

    public function test_successful_enrollment_creates_active_enrollment_and_first_invoice(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $course = Course::factory()->create(['monthly_fee' => 2000, 'status' => CourseStatus::Active]);
        $group = Group::factory()->create(['course_id' => $course->id, 'status' => GroupStatus::Active]);
        $student = Student::factory()->create(['status' => StudentStatus::Active]);

        $response = $this->actingAs($manager)->post(route('enrollments.store'), [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => '2026-10-01',
            'start_date' => '2026-10-01',
            'discount_amount' => 200,
        ]);

        $enrollment = Enrollment::where('student_id', $student->id)->where('group_id', $group->id)->firstOrFail();
        $response->assertRedirect(route('enrollments.show', $enrollment));

        $this->assertSame(EnrollmentStatus::Active, $enrollment->status);
        $this->assertSame('2000.00', $enrollment->monthly_fee);
        $this->assertSame('1800.00', $enrollment->final_monthly_fee);

        $invoice = $enrollment->invoices()->firstOrFail();
        $this->assertSame(InvoiceStatus::Pending, $invoice->status);
        $this->assertSame('1800.00', $invoice->total);
    }

    public function test_duplicate_active_enrollment_is_rejected(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create(['status' => GroupStatus::Active]);
        $student = Student::factory()->create(['status' => StudentStatus::Active]);
        Enrollment::factory()->create([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'status' => EnrollmentStatus::Active,
        ]);

        $response = $this->actingAs($manager)->post(route('enrollments.store'), [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => '2026-10-01',
            'start_date' => '2026-10-01',
            'discount_amount' => 0,
        ]);

        $response->assertSessionHasErrors('student_id');
        $this->assertSame(1, Enrollment::where('student_id', $student->id)->where('group_id', $group->id)->count());
    }

    public function test_full_groups_reject_new_enrollments(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create(['capacity' => 1, 'status' => GroupStatus::Active]);
        Enrollment::factory()->create(['group_id' => $group->id, 'status' => EnrollmentStatus::Active]);
        $student = Student::factory()->create(['status' => StudentStatus::Active]);

        $response = $this->actingAs($manager)->post(route('enrollments.store'), [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => '2026-10-01',
            'start_date' => '2026-10-01',
            'discount_amount' => 0,
        ]);

        $response->assertSessionHasErrors('group_id');
        $this->assertDatabaseMissing('enrollments', ['student_id' => $student->id]);
    }

    public function test_inactive_students_and_groups_are_rejected(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $activeGroup = Group::factory()->create(['status' => GroupStatus::Active]);
        $draftGroup = Group::factory()->create(['status' => GroupStatus::Draft]);
        $suspendedStudent = Student::factory()->create(['status' => StudentStatus::Suspended]);
        $activeStudent = Student::factory()->create(['status' => StudentStatus::Active]);

        $payload = fn (Student $student, Group $group): array => [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => '2026-10-01',
            'start_date' => '2026-10-01',
            'discount_amount' => 0,
        ];

        $this->actingAs($manager)->post(route('enrollments.store'), $payload($suspendedStudent, $activeGroup))
            ->assertSessionHasErrors('student_id');

        $this->actingAs($manager)->post(route('enrollments.store'), $payload($activeStudent, $draftGroup))
            ->assertSessionHasErrors('group_id');

        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_discount_cannot_exceed_monthly_fee(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $course = Course::factory()->create(['monthly_fee' => 500, 'status' => CourseStatus::Active]);
        $group = Group::factory()->create(['course_id' => $course->id, 'status' => GroupStatus::Active]);
        $student = Student::factory()->create(['status' => StudentStatus::Active]);

        $response = $this->actingAs($manager)->post(route('enrollments.store'), [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => '2026-10-01',
            'start_date' => '2026-10-01',
            'discount_amount' => 600,
        ]);

        $response->assertSessionHasErrors('discount_amount');
        $this->assertDatabaseMissing('enrollments', ['student_id' => $student->id]);
    }

    public function test_enrollment_status_follows_allowed_transitions(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $enrollment = Enrollment::factory()->create(['status' => EnrollmentStatus::Active]);

        $this->actingAs($manager)->put(route('enrollments.update', $enrollment), [
            'status' => EnrollmentStatus::Suspended->value,
        ])->assertRedirect(route('enrollments.show', $enrollment));

        $this->assertSame(EnrollmentStatus::Suspended, $enrollment->fresh()->status);

        $this->actingAs($manager)->put(route('enrollments.update', $enrollment), [
            'status' => EnrollmentStatus::Completed->value,
        ])->assertSessionHasErrors('status');

        $this->assertSame(EnrollmentStatus::Suspended, $enrollment->fresh()->status);
    }

    public function test_cancelling_an_enrollment_cancels_pending_invoices(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $enrollment = Enrollment::factory()->create(['status' => EnrollmentStatus::Active]);
        $invoice = $enrollment->invoices()->create([
            'period_start' => '2026-10-01',
            'period_end' => '2026-10-31',
            'amount' => 2000,
            'discount_amount' => 0,
            'total' => 2000,
            'status' => InvoiceStatus::Pending,
            'due_date' => '2026-10-01',
        ]);

        $this->actingAs($manager)->put(route('enrollments.update', $enrollment), [
            'status' => EnrollmentStatus::Cancelled->value,
        ])->assertRedirect(route('enrollments.show', $enrollment));

        $this->assertSame(InvoiceStatus::Cancelled, $invoice->fresh()->status);
    }

    public function test_students_can_reenroll_after_cancellation(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create(['status' => GroupStatus::Active]);
        $student = Student::factory()->create(['status' => StudentStatus::Active]);
        Enrollment::factory()->create([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'status' => EnrollmentStatus::Cancelled,
        ]);

        $response = $this->actingAs($manager)->post(route('enrollments.store'), [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => '2026-11-01',
            'start_date' => '2026-11-01',
            'discount_amount' => 0,
        ]);

        $enrollment = Enrollment::where('student_id', $student->id)->where('group_id', $group->id)
            ->where('status', EnrollmentStatus::Active)->firstOrFail();
        $response->assertRedirect(route('enrollments.show', $enrollment));
    }

    public function test_inactive_teachers_block_enrollment(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $teacher = Teacher::factory()->create(['status' => TeacherStatus::Inactive]);
        $group = Group::factory()->create(['teacher_id' => $teacher->id, 'status' => GroupStatus::Active]);
        $student = Student::factory()->create(['status' => StudentStatus::Active]);

        $response = $this->actingAs($manager)->post(route('enrollments.store'), [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => '2026-10-01',
            'start_date' => '2026-10-01',
            'discount_amount' => 0,
        ]);

        $response->assertSessionHasErrors('group_id');
        $this->assertDatabaseMissing('enrollments', ['student_id' => $student->id]);
    }
}
