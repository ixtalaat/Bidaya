<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\GroupStatus;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Enums\Role;
use App\Enums\SessionStatus;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Session;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\PaymentRecorded;
use App\Notifications\SessionReminder;
use App\Notifications\StudentAbsent;
use App\Notifications\StudentEnrolled;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrollment_notifies_linked_parent_users(): void
    {
        Notification::fake();

        $manager = User::factory()->withRole(Role::Manager)->create();
        $parentUser = User::factory()->withRole(Role::Parent)->create();
        $guardian = Guardian::factory()->create(['user_id' => $parentUser->id]);
        $student = Student::factory()->create();
        $guardian->students()->attach($student, ['relationship' => 'father']);
        $group = Group::factory()->create(['status' => GroupStatus::Active]);

        $this->actingAs($manager)->post(route('enrollments.store'), [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => today()->toDateString(),
            'start_date' => today()->toDateString(),
            'discount_amount' => 0,
        ]);

        Notification::assertSentTo($parentUser, StudentEnrolled::class);
    }

    public function test_enrollment_without_linked_users_sends_nothing(): void
    {
        Notification::fake();

        $manager = User::factory()->withRole(Role::Manager)->create();
        $student = Student::factory()->create();
        $group = Group::factory()->create(['status' => GroupStatus::Active]);

        $response = $this->actingAs($manager)->post(route('enrollments.store'), [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => today()->toDateString(),
            'start_date' => today()->toDateString(),
            'discount_amount' => 0,
        ]);

        $response->assertRedirect();
        Notification::assertNothingSent();
    }

    public function test_payment_notifies_linked_parent_users(): void
    {
        Notification::fake();

        $manager = User::factory()->withRole(Role::Manager)->create();
        $parentUser = User::factory()->withRole(Role::Parent)->create();
        $guardian = Guardian::factory()->create(['user_id' => $parentUser->id]);
        $student = Student::factory()->create();
        $guardian->students()->attach($student, ['relationship' => 'mother']);
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => EnrollmentStatus::Active,
        ]);
        $invoice = Invoice::factory()->create([
            'enrollment_id' => $enrollment->id,
            'total' => 1800,
            'amount' => 2000,
            'status' => InvoiceStatus::Pending,
        ]);

        $this->actingAs($manager)->post(route('payments.store'), [
            'invoice_id' => $invoice->id,
            'amount' => 1800,
            'payment_date' => now()->toDateString(),
            'method' => PaymentMethod::Cash->value,
        ]);

        Notification::assertSentTo($parentUser, PaymentRecorded::class);
    }

    public function test_absences_notify_parents_but_presences_do_not(): void
    {
        Notification::fake();

        $teacher = Teacher::factory()->create();
        $parentUser = User::factory()->withRole(Role::Parent)->create();
        $group = Group::factory()->create(['teacher_id' => $teacher->id]);
        $session = Session::factory()->create([
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
            'status' => SessionStatus::Scheduled,
        ]);

        $absentStudent = Student::factory()->create();
        $presentStudent = Student::factory()->create();
        $guardian = Guardian::factory()->create(['user_id' => $parentUser->id]);
        $guardian->students()->attach([$absentStudent->id, $presentStudent->id], ['relationship' => 'father']);

        $absentEnrollment = Enrollment::factory()->create([
            'student_id' => $absentStudent->id,
            'group_id' => $group->id,
            'status' => EnrollmentStatus::Active,
        ]);
        $presentEnrollment = Enrollment::factory()->create([
            'student_id' => $presentStudent->id,
            'group_id' => $group->id,
            'status' => EnrollmentStatus::Active,
        ]);

        $this->actingAs($teacher->user)->put(route('sessions.attendance.update', $session), [
            'records' => [
                ['enrollment_id' => $absentEnrollment->id, 'status' => AttendanceStatus::Absent->value],
                ['enrollment_id' => $presentEnrollment->id, 'status' => AttendanceStatus::Present->value],
            ],
        ]);

        Notification::assertSentTo($parentUser, StudentAbsent::class);
        Notification::assertSentToTimes($parentUser, StudentAbsent::class, 1);
    }

    public function test_users_see_only_their_own_notifications(): void
    {
        $parentUser = User::factory()->withRole(Role::Parent)->create();
        $otherUser = User::factory()->withRole(Role::Parent)->create();
        $otherUser->notify(new SessionReminder([], today()->toDateString()));
        $parentUser->notify(new SessionReminder([], today()->toDateString()));

        $response = $this->actingAs($parentUser)->get(route('notifications.index'));

        $response->assertOk();
        $this->assertSame(1, substr_count($response->getContent(), today()->toDateString()));
    }

    public function test_notifications_can_be_marked_as_read(): void
    {
        $parentUser = User::factory()->withRole(Role::Parent)->create();
        $parentUser->notify(new SessionReminder([], today()->toDateString()));
        $notification = $parentUser->notifications()->firstOrFail();

        $this->actingAs($parentUser)->post(route('notifications.read', $notification->id))
            ->assertRedirect(route('sessions.index', ['date' => today()->toDateString()]));

        $this->assertNotNull($notification->fresh()->read_at);

        $this->actingAs($parentUser)->post(route('notifications.read-all'))->assertRedirect();

        $this->assertSame(0, $parentUser->unreadNotifications()->count());
    }

    public function test_reminder_command_notifies_teachers_with_today_sessions(): void
    {
        Notification::fake();

        $teacher = Teacher::factory()->create();
        $otherTeacher = Teacher::factory()->create();
        Session::factory()->create([
            'group_id' => Group::factory()->create(['teacher_id' => $teacher->id])->id,
            'teacher_id' => $teacher->id,
            'date' => today()->toDateString(),
            'status' => SessionStatus::Scheduled,
        ]);
        Session::factory()->create([
            'group_id' => Group::factory()->create(['teacher_id' => $otherTeacher->id])->id,
            'teacher_id' => $otherTeacher->id,
            'date' => today()->addDay()->toDateString(),
            'status' => SessionStatus::Scheduled,
        ]);

        $this->artisan('app:send-session-reminders')->assertSuccessful();

        Notification::assertSentTo($teacher->user, SessionReminder::class);
        Notification::assertNotSentTo($otherTeacher->user, SessionReminder::class);
    }
}
