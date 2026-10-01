@extends('layouts.app')

@section('title', __('payments.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-xl font-bold">{{ __('payments.title') }}</h1>
    <a href="{{ route('payments.create') }}" class="rounded bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">
        {{ __('payments.create_button') }}
    </a>
</div>

<form method="GET" action="{{ route('payments.index') }}" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('payments.search_placeholder') }}"
           class="w-full max-w-xs rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
    <select name="method" class="rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        <option value="">{{ __('payments.all_methods') }}</option>
        @foreach (\App\Enums\PaymentMethod::cases() as $option)
            <option value="{{ $option->value }}" @selected(request('method') === $option->value)>{{ $option->label() }}</option>
        @endforeach
    </select>
    <select name="status" class="rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        <option value="">{{ __('payments.all_statuses') }}</option>
        @foreach (\App\Enums\PaymentStatus::cases() as $option)
            <option value="{{ $option->value }}" @selected(request('status') === $option->value)>{{ $option->label() }}</option>
        @endforeach
    </select>
    <input type="date" name="date_from" value="{{ request('date_from') }}" title="{{ __('payments.date_from') }}"
           class="rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
    <input type="date" name="date_to" value="{{ request('date_to') }}" title="{{ __('payments.date_to') }}"
           class="rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
    <button type="submit" class="rounded bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
        {{ __('payments.search_button') }}
    </button>
</form>

<div class="overflow-hidden rounded-lg bg-white shadow">
    <table class="w-full text-sm">
        <thead class="bg-slate-100">
            <tr>
                <th class="px-4 py-2 text-start font-medium">#</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('payments.student') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('payments.amount') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('payments.method') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('payments.status') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('payments.payment_date') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('payments.received_by') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2">
                        <a href="{{ route('payments.show', $payment) }}" class="text-indigo-700 hover:underline">{{ $payment->id }}</a>
                    </td>
                    <td class="px-4 py-2">{{ $payment->invoice->enrollment->student->name }}</td>
                    <td class="px-4 py-2">{{ number_format($payment->amount, 2) }}</td>
                    <td class="px-4 py-2">{{ $payment->method->label() }}</td>
                    <td class="px-4 py-2">{{ $payment->status->label() }}</td>
                    <td class="px-4 py-2">{{ $payment->payment_date->toDateString() }}</td>
                    <td class="px-4 py-2">{{ $payment->receiver->name }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-slate-500">{{ __('payments.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $payments->links() }}</div>
@endsection
