@extends('layouts.app')

@section('title', __('dashboard.accountant_title'))

@section('content')
<h1 class="mb-6 text-xl font-bold">{{ __('dashboard.accountant_title') }}</h1>

<div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
    <div class="rounded-lg bg-white p-4 shadow">
        <p class="text-sm text-slate-500">{{ __('dashboard.collected_today') }}</p>
        <p class="text-2xl font-bold">{{ number_format($collectedToday, 2) }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 shadow">
        <p class="text-sm text-slate-500">{{ __('dashboard.collected_month') }}</p>
        <p class="text-2xl font-bold">{{ number_format($collectedMonth, 2) }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 shadow">
        <p class="text-sm text-slate-500">{{ __('dashboard.outstanding') }}</p>
        <p class="text-2xl font-bold">{{ number_format($outstanding, 2) }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 shadow">
        <p class="text-sm text-slate-500">{{ __('dashboard.pending_invoices') }}</p>
        <p class="text-2xl font-bold">{{ $pendingInvoices }}</p>
    </div>
</div>

<div class="rounded-lg bg-white p-4 shadow">
    <h2 class="mb-2 font-bold">{{ __('dashboard.recent_payments') }}</h2>
    <ul class="space-y-1 text-sm">
        @forelse ($recentPayments as $payment)
            <li>
                <a href="{{ route('payments.show', $payment) }}" class="text-indigo-700 hover:underline">#{{ $payment->id }}</a>
                <span class="text-slate-500">
                    {{ $payment->invoice->enrollment->student->name }} —
                    {{ number_format($payment->amount, 2) }} —
                    {{ $payment->receiver->name }}
                </span>
            </li>
        @empty
            <li class="text-slate-500">—</li>
        @endforelse
    </ul>
</div>
@endsection
