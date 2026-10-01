@extends('layouts.app')

@section('title', __('payments.show_title'))

@section('content')
<div class="mx-auto max-w-xl rounded-lg bg-white p-6 shadow print:shadow-none">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h1 class="text-xl font-bold">{{ config('app.name', 'Bidaya') }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ __('payments.receipt_no', ['id' => $payment->id]) }}</p>
        </div>
        <button onclick="window.print()" class="rounded bg-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-800 hover:bg-slate-300 print:hidden">
            {{ __('payments.print_button') }}
        </button>
    </div>

    <dl class="grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
        <div>
            <dt class="font-medium text-slate-500">{{ __('payments.received_from') }}</dt>
            <dd>{{ $payment->invoice->enrollment->student->name }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('payments.group') }}</dt>
            <dd>{{ $payment->invoice->enrollment->group->name }} ({{ $payment->invoice->enrollment->group->course->name }})</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('payments.period') }}</dt>
            <dd>{{ $payment->invoice->period_start->toDateString() }} → {{ $payment->invoice->period_end->toDateString() }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('payments.payment_date') }}</dt>
            <dd>{{ $payment->payment_date->toDateString() }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('payments.amount') }}</dt>
            <dd class="text-lg font-bold">{{ number_format($payment->amount, 2) }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('payments.method') }}</dt>
            <dd>{{ $payment->method->label() }}{{ $payment->reference ? ' — '.$payment->reference : '' }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('payments.total_due') }}</dt>
            <dd>{{ number_format($payment->invoice->total, 2) }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('payments.remaining') }}</dt>
            <dd>{{ number_format($payment->invoice->remaining(), 2) }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('payments.received_by') }}</dt>
            <dd>{{ $payment->receiver->name }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('payments.status') }}</dt>
            <dd>{{ $payment->status->label() }}</dd>
        </div>
    </dl>

    @if ($payment->notes)
        <div class="mt-3 text-sm">
            <p class="font-medium text-slate-500">{{ __('payments.notes') }}</p>
            <p>{{ $payment->notes }}</p>
        </div>
    @endif

    @if ($payment->status === \App\Enums\PaymentStatus::Completed)
        <form method="POST" action="{{ route('payments.refund', $payment) }}" class="mt-6 print:hidden"
              onsubmit="return confirm({{ json_encode(__('payments.refund_confirm')) }})">
            @csrf
            <button type="submit" class="rounded bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">
                {{ __('payments.refund_button') }}
            </button>
        </form>
    @endif
</div>
@endsection
