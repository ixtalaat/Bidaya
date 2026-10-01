@extends('layouts.app')

@section('title', __('enrollments.show_title'))

@section('content')
<div class="mx-auto max-w-2xl clay-card p-6">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h1 class="font-display text-xl font-bold">{{ $enrollment->student->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $enrollment->group->name }} ({{ $enrollment->group->course->name }}) — {{ $enrollment->status->label() }}
            </p>
        </div>
        <a href="{{ route('enrollments.edit', $enrollment) }}" class="rounded clay-btn cursor-pointer bg-white px-3 py-1.5 text-sm font-extrabold text-slate-900">
            {{ __('students.edit_button') }}
        </a>
    </div>

    <dl class="grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
        <div>
            <dt class="font-medium text-slate-500">{{ __('enrollments.monthly_fee') }}</dt>
            <dd>{{ number_format($enrollment->monthly_fee, 2) }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('enrollments.discount_amount') }}</dt>
            <dd>{{ number_format($enrollment->discount_amount, 2) }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('enrollments.final_monthly_fee') }}</dt>
            <dd class="font-bold">{{ number_format($enrollment->final_monthly_fee, 2) }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('enrollments.enrollment_date') }}</dt>
            <dd>{{ $enrollment->enrollment_date->toDateString() }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('enrollments.start_date') }} / {{ __('enrollments.end_date') }}</dt>
            <dd>{{ $enrollment->start_date->toDateString() }} / {{ $enrollment->end_date?->toDateString() ?? '—' }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('groups.teacher') }}</dt>
            <dd>{{ $enrollment->group->teacher->user->name }}</dd>
        </div>
    </dl>

    <h2 class="mb-2 mt-6 font-bold">{{ __('enrollments.invoices') }}</h2>
    <div class="overflow-hidden rounded border border-slate-200">
        <table class="w-full text-sm">
            <thead class="bg-background">
                <tr>
                    <th class="px-4 py-2 text-start font-medium">{{ __('enrollments.period') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('enrollments.total') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('payments.remaining') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('enrollments.invoice_status') }}</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($enrollment->invoices as $invoice)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2">{{ $invoice->period_start->toDateString() }} → {{ $invoice->period_end->toDateString() }}</td>
                        <td class="px-4 py-2">{{ number_format($invoice->total, 2) }}</td>
                        <td class="px-4 py-2">{{ number_format($invoice->remaining(), 2) }}</td>
                        <td class="px-4 py-2">{{ $invoice->status->label() }}</td>
                        <td class="px-4 py-2 text-end">
                            @can('record-payments')
                                @if ($invoice->remaining() > 0 && $invoice->status !== \App\Enums\InvoiceStatus::Cancelled)
                                    <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" class="text-primary font-semibold hover:underline">{{ __('payments.create_button') }}</a>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-slate-500">—</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
