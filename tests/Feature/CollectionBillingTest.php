<?php

namespace Tests\Feature;

use App\Enums\CourseStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\GroupStatus;
use App\Enums\InvoiceStatus;
use App\Enums\Role;
use App\Enums\StudentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionBillingTest extends TestCase
{
    use RefreshDatabase;

    public function test_second_group_in_same_collection_creates_no_invoice(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $course = Course::factory()->create(['monthly_fee' => 350, 'status' => CourseStatus::Active]);
        $firstGroup = Group::factory()->create(['course_id' => $course->id, 'status' => GroupStatus::Active]);
        $secondGroup = Group::factory()->create(['course_id' => $course->id, 'status' => GroupStatus::Active]);
        $student = Student::factory()->create(['status' => StudentStatus::Active]);

        $payload = fn (Group $group): array => [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => today()->toDateString(),
            'start_date' => today()->toDateString(),
            'discount_amount' => 0,
        ];

        $first = $this->actingAs($manager)->post(route('enrollments.store'), $payload($firstGroup));
        $second = $this->actingAs($manager)->post(route('enrollments.store'), $payload($secondGroup));

        $first->assertRedirect();
        $second->assertRedirect();

        $this->assertSame(1, Invoice::whereHas(
            'enrollment',
            fn ($query) => $query->where('student_id', $student->id)
        )->count());

        $secondEnrollment = Enrollment::where('student_id', $student->id)->where('group_id', $secondGroup->id)->firstOrFail();
        $this->assertNotNull($secondEnrollment->covered_by_enrollment_id);
        $this->assertSame(0, $secondEnrollment->invoices()->count());
    }

    public function test_different_collections_bill_separately(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $foundation = Course::factory()->create(['monthly_fee' => 350, 'status' => CourseStatus::Active]);
        $nursery = Course::factory()->create(['monthly_fee' => 500, 'status' => CourseStatus::Active]);
        $foundationGroup = Group::factory()->create(['course_id' => $foundation->id, 'status' => GroupStatus::Active]);
        $nurseryGroup = Group::factory()->create(['course_id' => $nursery->id, 'status' => GroupStatus::Active]);
        $student = Student::factory()->create(['status' => StudentStatus::Active]);

        $payload = fn (Group $group): array => [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => today()->toDateString(),
            'start_date' => today()->toDateString(),
            'discount_amount' => 0,
        ];

        $this->actingAs($manager)->post(route('enrollments.store'), $payload($foundationGroup))->assertRedirect();
        $this->actingAs($manager)->post(route('enrollments.store'), $payload($nurseryGroup))->assertRedirect();

        $this->assertSame(2, Invoice::whereHas(
            'enrollment',
            fn ($query) => $query->where('student_id', $student->id)
        )->count());
    }

    public function test_cancelled_sibling_does_not_cover(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $course = Course::factory()->create(['monthly_fee' => 350, 'status' => CourseStatus::Active]);
        $firstGroup = Group::factory()->create(['course_id' => $course->id, 'status' => GroupStatus::Active]);
        $secondGroup = Group::factory()->create(['course_id' => $course->id, 'status' => GroupStatus::Active]);
        $student = Student::factory()->create(['status' => StudentStatus::Active]);

        $payload = fn (Group $group): array => [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => today()->toDateString(),
            'start_date' => today()->toDateString(),
            'discount_amount' => 0,
        ];

        $this->actingAs($manager)->post(route('enrollments.store'), $payload($firstGroup))->assertRedirect();

        $firstEnrollment = Enrollment::where('student_id', $student->id)->where('group_id', $firstGroup->id)->firstOrFail();

        $this->actingAs($manager)->put(route('enrollments.update', $firstEnrollment), [
            'status' => EnrollmentStatus::Cancelled->value,
        ])->assertRedirect();

        $this->actingAs($manager)->post(route('enrollments.store'), $payload($secondGroup))->assertRedirect();

        $secondEnrollment = Enrollment::where('student_id', $student->id)->where('group_id', $secondGroup->id)->firstOrFail();
        $this->assertNull($secondEnrollment->covered_by_enrollment_id);
        $this->assertSame(1, $secondEnrollment->invoices()->count());
    }

    public function test_covered_enrollment_shows_its_link(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $course = Course::factory()->create(['monthly_fee' => 350, 'status' => CourseStatus::Active]);
        $firstGroup = Group::factory()->create(['course_id' => $course->id, 'status' => GroupStatus::Active]);
        $secondGroup = Group::factory()->create(['course_id' => $course->id, 'status' => GroupStatus::Active]);
        $student = Student::factory()->create(['status' => StudentStatus::Active]);

        $payload = fn (Group $group): array => [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => today()->toDateString(),
            'start_date' => today()->toDateString(),
            'discount_amount' => 0,
        ];

        $this->actingAs($manager)->post(route('enrollments.store'), $payload($firstGroup))->assertRedirect();
        $this->actingAs($manager)->post(route('enrollments.store'), $payload($secondGroup))->assertRedirect();

        $secondEnrollment = Enrollment::where('student_id', $student->id)->where('group_id', $secondGroup->id)->firstOrFail();

        $response = $this->actingAs($manager)->get(route('enrollments.show', $secondEnrollment));

        $response->assertOk();
        $response->assertSee((string) $secondEnrollment->covered_by_enrollment_id);
    }

    public function test_fully_paid_sibling_still_covers(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $course = Course::factory()->create(['monthly_fee' => 350, 'status' => CourseStatus::Active]);
        $firstGroup = Group::factory()->create(['course_id' => $course->id, 'status' => GroupStatus::Active]);
        $secondGroup = Group::factory()->create(['course_id' => $course->id, 'status' => GroupStatus::Active]);
        $student = Student::factory()->create(['status' => StudentStatus::Active]);

        $payload = fn (Group $group): array => [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => today()->toDateString(),
            'start_date' => today()->toDateString(),
            'discount_amount' => 0,
        ];

        $this->actingAs($manager)->post(route('enrollments.store'), $payload($firstGroup))->assertRedirect();

        $firstEnrollment = Enrollment::where('student_id', $student->id)->where('group_id', $firstGroup->id)->firstOrFail();
        $firstEnrollment->invoices()->update(['status' => InvoiceStatus::Paid]);

        $this->actingAs($manager)->post(route('enrollments.store'), $payload($secondGroup))->assertRedirect();

        $secondEnrollment = Enrollment::where('student_id', $student->id)->where('group_id', $secondGroup->id)->firstOrFail();
        $this->assertNotNull($secondEnrollment->covered_by_enrollment_id);
        $this->assertSame(0, $secondEnrollment->invoices()->count());
    }
}
