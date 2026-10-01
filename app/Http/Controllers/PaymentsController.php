<?php

namespace App\Http\Controllers;

use App\Actions\Payments\RecordPaymentAction;
use App\Actions\Payments\RefundPaymentAction;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $method = PaymentMethod::tryFrom($request->string('method')->toString());
        $status = PaymentStatus::tryFrom($request->string('status')->toString());

        $payments = Payment::query()
            ->when(
                $request->string('search')->toString(),
                fn (Builder $query, string $term) => $query->whereHas(
                    'invoice.enrollment.student',
                    fn (Builder $students) => $students->where('name', 'like', "%{$term}%")
                )
            )
            ->when(
                $method,
                fn (Builder $query, PaymentMethod $method) => $query->where('method', $method)
            )
            ->when(
                $status,
                fn (Builder $query, PaymentStatus $status) => $query->where('status', $status)
            )
            ->when(
                $request->string('date_from')->toString(),
                fn (Builder $query, string $from) => $query->whereDate('payment_date', '>=', $from)
            )
            ->when(
                $request->string('date_to')->toString(),
                fn (Builder $query, string $to) => $query->whereDate('payment_date', '<=', $to)
            )
            ->with(['invoice.enrollment.student', 'invoice.enrollment.group.course', 'receiver'])
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('payments.index', ['payments' => $payments]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $openInvoices = Invoice::query()
            ->whereIn('status', [InvoiceStatus::Pending, InvoiceStatus::PartiallyPaid])
            ->with(['enrollment.student', 'enrollment.group.course'])
            ->orderBy('due_date')
            ->orderBy('id')
            ->get()
            ->filter(fn (Invoice $invoice): bool => $invoice->remaining() > 0);

        return view('payments.create', [
            'openInvoices' => $openInvoices,
            'preselectedInvoiceId' => $request->integer('invoice_id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request, RecordPaymentAction $record): RedirectResponse
    {
        $payment = $record->handle($request->validated(), $request->user());

        return redirect()->route('payments.show', $payment)->with('status', __('payments.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment): View
    {
        $payment->load(['invoice.enrollment.student', 'invoice.enrollment.group.course', 'receiver']);

        return view('payments.show', ['payment' => $payment]);
    }

    /**
     * Refund a completed payment. Payments are never deleted.
     */
    public function refund(Request $request, Payment $payment, RefundPaymentAction $refund): RedirectResponse
    {
        $payment = $refund->handle($payment, $request->user());

        return redirect()->route('payments.show', $payment)->with('status', __('payments.refunded'));
    }
}
