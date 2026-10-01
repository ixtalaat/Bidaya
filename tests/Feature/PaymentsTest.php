<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\Role;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('payments.index'))->assertRedirect(route('login'));
    }

    public function test_teachers_cannot_access_payments(): void
    {
        $teacher = User::factory()->withRole(Role::Teacher)->create();

        $this->actingAs($teacher)->get(route('payments.index'))->assertForbidden();
        $this->actingAs($teacher)->post(route('payments.store'), [])->assertForbidden();
    }

    public function test_accountants_can_record_payments(): void
    {
        $accountant = User::factory()->withRole(Role::Accountant)->create();
        $invoice = Invoice::factory()->create(['total' => 1800, 'amount' => 2000, 'status' => InvoiceStatus::Pending]);

        $response = $this->actingAs($accountant)->post(route('payments.store'), [
            'invoice_id' => $invoice->id,
            'amount' => 1800,
            'payment_date' => now()->toDateString(),
            'method' => PaymentMethod::Cash->value,
        ]);

        $payment = Payment::where('invoice_id', $invoice->id)->firstOrFail();
        $response->assertRedirect(route('payments.show', $payment));

        $this->assertSame(PaymentStatus::Completed, $payment->status);
        $this->assertSame($accountant->id, $payment->received_by);
        $this->assertSame(InvoiceStatus::Paid, $invoice->fresh()->status);
    }

    public function test_partial_payments_mark_invoices_partially_paid(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $invoice = Invoice::factory()->create(['total' => 1800, 'amount' => 2000, 'status' => InvoiceStatus::Pending]);

        $this->actingAs($manager)->post(route('payments.store'), [
            'invoice_id' => $invoice->id,
            'amount' => 1000,
            'payment_date' => now()->toDateString(),
            'method' => PaymentMethod::BankTransfer->value,
            'reference' => 'TRX-001',
        ])->assertRedirect(route('payments.show', Payment::firstOrFail()));

        $fresh = $invoice->fresh();
        $this->assertSame(InvoiceStatus::PartiallyPaid, $fresh->status);
        $this->assertSame(800.0, $fresh->remaining());
    }

    public function test_overpayments_are_rejected(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $invoice = Invoice::factory()->create(['total' => 1800, 'amount' => 2000, 'status' => InvoiceStatus::Pending]);

        $response = $this->actingAs($manager)->post(route('payments.store'), [
            'invoice_id' => $invoice->id,
            'amount' => 1800.01,
            'payment_date' => now()->toDateString(),
            'method' => PaymentMethod::Cash->value,
        ]);

        $response->assertSessionHasErrors('amount');
        $this->assertSame(0, $invoice->payments()->count());
        $this->assertSame(InvoiceStatus::Pending, $invoice->fresh()->status);
    }

    public function test_zero_and_future_payments_are_rejected(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $invoice = Invoice::factory()->create(['total' => 1800, 'status' => InvoiceStatus::Pending]);

        $this->actingAs($manager)->post(route('payments.store'), [
            'invoice_id' => $invoice->id,
            'amount' => 0,
            'payment_date' => now()->toDateString(),
            'method' => PaymentMethod::Cash->value,
        ])->assertSessionHasErrors('amount');

        $this->actingAs($manager)->post(route('payments.store'), [
            'invoice_id' => $invoice->id,
            'amount' => 100,
            'payment_date' => now()->addDay()->toDateString(),
            'method' => PaymentMethod::Cash->value,
        ])->assertSessionHasErrors('payment_date');

        $this->assertSame(0, $invoice->payments()->count());
    }

    public function test_cancelled_invoices_reject_payments(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $invoice = Invoice::factory()->create(['total' => 1800, 'status' => InvoiceStatus::Cancelled]);

        $response = $this->actingAs($manager)->post(route('payments.store'), [
            'invoice_id' => $invoice->id,
            'amount' => 100,
            'payment_date' => now()->toDateString(),
            'method' => PaymentMethod::Cash->value,
        ]);

        $response->assertSessionHasErrors('invoice_id');
        $this->assertSame(0, $invoice->payments()->count());
    }

    public function test_completed_payments_can_be_refunded_once(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $invoice = Invoice::factory()->create(['total' => 1800, 'amount' => 2000, 'status' => InvoiceStatus::Paid]);
        $payment = Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'amount' => 1800,
            'status' => PaymentStatus::Completed,
        ]);

        $this->actingAs($manager)->post(route('payments.refund', $payment))
            ->assertRedirect(route('payments.show', $payment));

        $this->assertSame(PaymentStatus::Refunded, $payment->fresh()->status);
        $this->assertSame(InvoiceStatus::Pending, $invoice->fresh()->status);

        $this->actingAs($manager)->post(route('payments.refund', $payment))
            ->assertSessionHasErrors('payment');
    }

    public function test_financial_writes_create_audit_entries(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $invoice = Invoice::factory()->create(['total' => 1800, 'amount' => 2000, 'status' => InvoiceStatus::Pending]);

        $this->actingAs($manager)->post(route('payments.store'), [
            'invoice_id' => $invoice->id,
            'amount' => 1800,
            'payment_date' => now()->toDateString(),
            'method' => PaymentMethod::Card->value,
        ]);

        $payment = Payment::firstOrFail();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $manager->id,
            'action' => 'payment.recorded',
            'entity_type' => Payment::class,
            'entity_id' => $payment->id,
        ]);

        $this->actingAs($manager)->post(route('payments.refund', $payment));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $manager->id,
            'action' => 'payment.refunded',
            'entity_id' => $payment->id,
        ]);
    }

    public function test_enrollment_changes_are_audited(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $enrollment = Enrollment::factory()->create();

        $this->actingAs($manager)->put(route('enrollments.update', $enrollment), [
            'status' => EnrollmentStatus::Suspended->value,
        ])->assertRedirect(route('enrollments.show', $enrollment));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $manager->id,
            'action' => 'enrollment.status_changed',
            'entity_type' => Enrollment::class,
            'entity_id' => $enrollment->id,
        ]);
    }

    public function test_receipt_page_shows_payment_details(): void
    {
        $manager = User::factory()->withRole(Role::Manager)->create();
        $payment = Payment::factory()->create([
            'amount' => 1800,
            'method' => PaymentMethod::Cash,
            'status' => PaymentStatus::Completed,
        ]);

        $response = $this->actingAs($manager)->get(route('payments.show', $payment));

        $response->assertOk();
        $response->assertSee('1,800.00');
        $response->assertSee($payment->invoice->enrollment->student->name);
    }
}
