<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Enums\Role;
use App\Enums\SessionStatus;
use App\Models\Assessment;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Session;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_teachers_cannot_view_reports(): void
    {
        $teacher = User::factory()->withRole(Role::Teacher)->create();

        $this->actingAs($teacher)->get(route('reports.financial'))->assertForbidden();
        $this->actingAs($teacher)->get(route('reports.attendance'))->assertForbidden();
        $this->actingAs($teacher)->get(route('reports.progress'))->assertForbidden();
        $this->actingAs($teacher)->get(route('reports.courses'))->assertForbidden();
        $this->actingAs($teacher)->get(route('audit-logs.index'))->assertForbidden();
    }

    public function test_accountants_can_view_financial_reports_only(): void
    {
        $accountant = User::factory()->withRole(Role::Accountant)->create();

        $this->actingAs($accountant)->get(route('reports.financial'))->assertOk();
        $this->actingAs($accountant)->get(route('reports.attendance'))->assertForbidden();
        $this->actingAs($accountant)->get(route('audit-logs.index'))->assertForbidden();
    }

    public function test_financial_report_shows_collected_and_outstanding(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $invoice = Invoice::factory()->create(['total' => 1800, 'amount' => 2000, 'status' => InvoiceStatus::Pending]);
        Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'amount' => 1000,
            'payment_date' => today()->toDateString(),
            'status' => PaymentStatus::Completed,
        ]);

        $response = $this->actingAs($manager)->get(route('reports.financial', [
            'from' => today()->toDateString(),
            'to' => today()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertSee('1,000.00');
        $response->assertSee('800.00');
        $response->assertSee($invoice->enrollment->group->course->name);
    }

    public function test_attendance_report_counts_per_student(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'group_id' => $group->id,
            'student_id' => Student::factory()->create(['name' => 'Omar Ahmed'])->id,
            'status' => EnrollmentStatus::Active,
        ]);
        $session = Session::factory()->create([
            'group_id' => $group->id,
            'teacher_id' => $group->teacher_id,
            'date' => today()->toDateString(),
            'status' => SessionStatus::Completed,
        ]);
        $session->attendances()->create([
            'enrollment_id' => $enrollment->id,
            'status' => AttendanceStatus::Present,
        ]);

        $response = $this->actingAs($manager)->get(route('reports.attendance', [
            'group_id' => $group->id,
            'from' => today()->subDays(30)->toDateString(),
            'to' => today()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertSee('Omar Ahmed');
        $response->assertSee('100%');
    }

    public function test_progress_report_combines_attendance_grades_and_balance(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'group_id' => $group->id,
            'student_id' => Student::factory()->create(['name' => 'Jana Ali'])->id,
            'status' => EnrollmentStatus::Active,
        ]);
        $assessment = Assessment::factory()->create(['group_id' => $group->id, 'max_score' => 20]);
        $assessment->results()->create(['enrollment_id' => $enrollment->id, 'score' => 16]);

        $response = $this->actingAs($manager)->get(route('reports.progress', [
            'group_id' => $group->id,
        ]));

        $response->assertOk();
        $response->assertSee('Jana Ali');
        $response->assertSee('16.00');
    }

    public function test_courses_report_shows_enrollment_and_revenue(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $group = Group::factory()->create();
        Enrollment::factory()->create(['group_id' => $group->id, 'status' => EnrollmentStatus::Active]);
        Payment::factory()->create([
            'invoice_id' => Invoice::factory()->create([
                'enrollment_id' => Enrollment::factory()->create(['group_id' => $group->id])->id,
                'total' => 2000,
            ])->id,
            'amount' => 2000,
            'status' => PaymentStatus::Completed,
        ]);

        $response = $this->actingAs($manager)->get(route('reports.courses'));

        $response->assertOk();
        $response->assertSee($group->course->name);
        $response->assertSee('2,000.00');
    }

    public function test_audit_log_viewer_lists_entries(): void
    {
        $admin = User::factory()->withRole(Role::SuperAdmin)->create();
        $enrollment = Enrollment::factory()->create();

        $this->actingAs($admin)->put(route('enrollments.update', $enrollment), [
            'status' => EnrollmentStatus::Suspended->value,
        ]);

        $index = $this->actingAs($admin)->get(route('audit-logs.index'));
        $index->assertOk();
        $index->assertSee('enrollment.status_changed');

        $log = AuditLog::firstOrFail();
        $show = $this->actingAs($admin)->get(route('audit-logs.show', $log));
        $show->assertOk();
        $show->assertSee('suspended');
    }
}
