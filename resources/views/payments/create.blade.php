@extends('layouts.app')

@section('title', __('payments.create_title'))

@section('content')
<div class="mx-auto max-w-xl clay-card p-6">
    <h1 class="mb-6 font-display text-xl font-bold">{{ __('payments.create_title') }}</h1>

    <form method="POST" action="{{ route('payments.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="invoice_id" class="mb-1 block text-sm font-medium">{{ __('payments.invoice') }}</label>
            <select id="invoice_id" name="invoice_id" required
                    class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                <option value="">{{ __('payments.select_invoice') }}</option>
                @foreach ($openInvoices as $invoice)
                    <option value="{{ $invoice->id }}" @selected((string) old('invoice_id', $preselectedInvoiceId) === (string) $invoice->id)>
                        #{{ $invoice->id }} — {{ $invoice->enrollment->student->name }}
                        ({{ $invoice->enrollment->group->course->name }} —
                        {{ __('payments.remaining') }}: {{ number_format($invoice->remaining(), 2) }})
                    </option>
                @endforeach
            </select>
            @error('invoice_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="amount" class="mb-1 block text-sm font-medium">{{ __('payments.amount') }}</label>
                <input id="amount" type="number" name="amount" value="{{ old('amount') }}" min="0.01" step="0.01" required
                       class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="payment_date" class="mb-1 block text-sm font-medium">{{ __('payments.payment_date') }}</label>
                <input id="payment_date" type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required
                       class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                @error('payment_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="method" class="mb-1 block text-sm font-medium">{{ __('payments.method') }}</label>
                <select id="method" name="method" required
                        class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                    @foreach (\App\Enums\PaymentMethod::cases() as $option)
                        <option value="{{ $option->value }}" @selected(old('method') === $option->value)>{{ $option->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="reference" class="mb-1 block text-sm font-medium">{{ __('payments.reference') }}</label>
                <input id="reference" type="text" name="reference" value="{{ old('reference') }}"
                       class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            </div>
        </div>

        <div>
            <label for="notes" class="mb-1 block text-sm font-medium">{{ __('payments.notes') }}</label>
            <textarea id="notes" name="notes" rows="2"
                      class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="w-full rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('payments.save_button') }}
        </button>
    </form>
</div>
@endsection
