<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Enums\Role;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_managers_can_issue_certificates_for_completed_enrollments(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $enrollment = Enrollment::factory()->create(['status' => EnrollmentStatus::Completed]);

        $response = $this->actingAs($manager)->post(route('enrollments.certificates.store', $enrollment));

        $certificate = Certificate::where('enrollment_id', $enrollment->id)->firstOrFail();
        $response->assertRedirect(route('certificates.show', $certificate));
        $this->assertMatchesRegularExpression('/^BID-\d{4}-\d{4}$/', $certificate->number);
        $this->assertSame($manager->id, $certificate->issued_by);
    }

    public function test_active_enrollments_cannot_receive_certificates(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $enrollment = Enrollment::factory()->create(['status' => EnrollmentStatus::Active]);

        $response = $this->actingAs($manager)->post(route('enrollments.certificates.store', $enrollment));

        $response->assertSessionHasErrors('enrollment');
        $this->assertSame(0, Certificate::count());
    }

    public function test_duplicate_certificates_are_rejected(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $enrollment = Enrollment::factory()->create(['status' => EnrollmentStatus::Completed]);
        Certificate::create([
            'enrollment_id' => $enrollment->id,
            'number' => 'BID-2026-0001',
            'issued_by' => $manager->id,
            'issued_at' => now(),
        ]);

        $response = $this->actingAs($manager)->post(route('enrollments.certificates.store', $enrollment));

        $response->assertSessionHasErrors('enrollment');
        $this->assertSame(1, Certificate::count());
    }

    public function test_teachers_can_issue_for_their_own_groups_only(): void
    {
        $teacher = Teacher::factory()->create();
        $ownEnrollment = Enrollment::factory()->create([
            'group_id' => Group::factory()->create(['teacher_id' => $teacher->id])->id,
            'status' => EnrollmentStatus::Completed,
        ]);
        $otherEnrollment = Enrollment::factory()->create(['status' => EnrollmentStatus::Completed]);

        $this->actingAs($teacher->user)->post(route('enrollments.certificates.store', $ownEnrollment))
            ->assertRedirect();

        $this->assertSame(1, Certificate::count());

        $this->actingAs($teacher->user)->post(route('enrollments.certificates.store', $otherEnrollment))
            ->assertForbidden();
    }

    public function test_linked_parents_can_view_but_not_issue(): void
    {
        $parentUser = User::factory()->withRole(Role::Parent)->create();
        $guardian = Guardian::factory()->create(['user_id' => $parentUser->id]);
        $student = Student::factory()->create();
        $guardian->students()->attach($student, ['relationship' => 'father']);
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => EnrollmentStatus::Completed,
        ]);
        $certificate = Certificate::create([
            'enrollment_id' => $enrollment->id,
            'number' => 'BID-2026-0001',
            'issued_by' => User::factory()->withRole(Role::Manager)->create()->id,
            'issued_at' => now(),
        ]);

        $this->actingAs($parentUser)->get(route('certificates.show', $certificate))
            ->assertOk()
            ->assertSee($student->name)
            ->assertSee($certificate->number);

        $this->actingAs($parentUser)->post(route('enrollments.certificates.store', $enrollment))
            ->assertForbidden();
    }

    public function test_unrelated_users_cannot_view_certificates(): void
    {
        $parentUser = User::factory()->withRole(Role::Parent)->create();
        Guardian::factory()->create(['user_id' => $parentUser->id]);
        $enrollment = Enrollment::factory()->create(['status' => EnrollmentStatus::Completed]);
        $certificate = Certificate::create([
            'enrollment_id' => $enrollment->id,
            'number' => 'BID-2026-0001',
            'issued_by' => User::factory()->withRole(Role::Manager)->create()->id,
            'issued_at' => now(),
        ]);

        $this->actingAs($parentUser)->get(route('certificates.show', $certificate))->assertForbidden();
    }
}
