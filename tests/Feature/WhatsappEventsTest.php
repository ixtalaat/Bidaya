<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\GroupStatus;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Enums\Role;
use App\Enums\SessionStatus;
use App\Enums\WhatsAppMessageStatus;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Session;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class WhatsappEventsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.whatsapp.enabled', true);
        config()->set('services.whatsapp.phone_number_id', 'test-id');
        config()->set('services.whatsapp.token', 'test-token');
    }

    protected function fakeWhatsAppSuccess(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.test']]], 200),
        ]);
    }

    public function test_enrollment_sends_whatsapp_to_guardian_phones(): void
    {
        $this->fakeWhatsAppSuccess();
        Notification::fake();

        $manager = User::factory()->withRole(Role::Manager)->create();
        $parentUser = User::factory()->withRole(Role::Parent)->create();
        $guardian = Guardian::factory()->create(['user_id' => $parentUser->id, 'phone' => '01001234567']);
        $student = Student::factory()->create();
        $guardian->students()->attach($student, ['relationship' => 'father']);
        $group = Group::factory()->create(['status' => GroupStatus::Active]);

        $this->actingAs($manager)->post(route('enrollments.store'), [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => today()->toDateString(),
            'start_date' => today()->toDateString(),
            'discount_amount' => 0,
        ])->assertRedirect();

        $this->assertDatabaseHas('whatsapp_messages', [
            'recipient' => '201001234567',
            'event' => 'enrollment.created',
            'status' => WhatsAppMessageStatus::Sent->value,
        ]);
        Http::assertSentCount(1);
    }

    public function test_payment_and_absence_send_whatsapp(): void
    {
        $this->fakeWhatsAppSuccess();
        Notification::fake();

        $manager = User::factory()->withRole(Role::Manager)->create();
        $parentUser = User::factory()->withRole(Role::Parent)->create();
        $guardian = Guardian::factory()->create(['user_id' => $parentUser->id, 'phone' => '+201001234567']);
        $student = Student::factory()->create();
        $guardian->students()->attach($student, ['relationship' => 'mother']);
        $group = Group::factory()->create(['status' => GroupStatus::Active]);
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'group_id' => $group->id,
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
        ])->assertRedirect();

        $session = Session::factory()->create([
            'group_id' => $group->id,
            'teacher_id' => $group->teacher_id,
            'status' => SessionStatus::Scheduled,
        ]);

        $this->actingAs($manager)->put(route('sessions.attendance.update', $session), [
            'records' => [
                ['enrollment_id' => $enrollment->id, 'status' => AttendanceStatus::Absent->value],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('whatsapp_messages', [
            'recipient' => '201001234567',
            'event' => 'payment.recorded',
            'status' => WhatsAppMessageStatus::Sent->value,
        ]);
        $this->assertDatabaseHas('whatsapp_messages', [
            'recipient' => '201001234567',
            'event' => 'attendance.absent',
            'status' => WhatsAppMessageStatus::Sent->value,
        ]);
    }

    public function test_invalid_phones_are_skipped_without_breaking_the_flow(): void
    {
        $this->fakeWhatsAppSuccess();
        Notification::fake();

        $manager = User::factory()->withRole(Role::Manager)->create();
        $guardian = Guardian::factory()->create(['phone' => 'not-a-number']);
        $student = Student::factory()->create();
        $guardian->students()->attach($student, ['relationship' => 'father']);
        $group = Group::factory()->create(['status' => GroupStatus::Active]);

        $this->actingAs($manager)->post(route('enrollments.store'), [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => today()->toDateString(),
            'start_date' => today()->toDateString(),
            'discount_amount' => 0,
        ])->assertRedirect();

        $this->assertDatabaseHas('whatsapp_messages', [
            'event' => 'enrollment.created',
            'status' => WhatsAppMessageStatus::Skipped->value,
        ]);
        Http::assertNothingSent();
    }

    public function test_api_failures_are_logged_without_rolling_back(): void
    {
        Notification::fake();

        Http::fake([
            'graph.facebook.com/*' => Http::response(['error' => ['message' => 'Bad token.']], 401),
        ]);

        $manager = User::factory()->withRole(Role::Manager)->create();
        $guardian = Guardian::factory()->create(['phone' => '01001234567']);
        $student = Student::factory()->create();
        $guardian->students()->attach($student, ['relationship' => 'father']);
        $group = Group::factory()->create(['status' => GroupStatus::Active]);

        $this->actingAs($manager)->post(route('enrollments.store'), [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'enrollment_date' => today()->toDateString(),
            'start_date' => today()->toDateString(),
            'discount_amount' => 0,
        ])->assertRedirect();

        $this->assertDatabaseHas('enrollments', ['student_id' => $student->id]);
        $this->assertDatabaseHas('whatsapp_messages', [
            'event' => 'enrollment.created',
            'status' => WhatsAppMessageStatus::Failed->value,
        ]);
    }

    public function test_reminder_command_messages_teachers_on_whatsapp(): void
    {
        $this->fakeWhatsAppSuccess();
        Notification::fake();

        $teacher = Teacher::factory()->create(['phone' => '01112223333']);
        Session::factory()->create([
            'group_id' => Group::factory()->create(['teacher_id' => $teacher->id])->id,
            'teacher_id' => $teacher->id,
            'date' => today()->toDateString(),
            'status' => SessionStatus::Scheduled,
        ]);

        $this->artisan('app:send-session-reminders')->assertSuccessful();

        $this->assertDatabaseHas('whatsapp_messages', [
            'recipient' => '201112223333',
            'event' => 'session.reminder',
            'status' => WhatsAppMessageStatus::Sent->value,
        ]);
    }
}
