@extends('layouts.app')

@section('title', __('reports.financial_title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-xl font-bold">{{ __('reports.financial_title') }}</h1>
    <button onclick="window.print()" class="rounded bg-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-800 hover:bg-slate-300 print:hidden">
        {{ __('reports.print_button') }}
    </button>
</div>

<form method="GET" action="{{ route('reports.financial') }}" class="mb-4 flex flex-wrap items-end gap-2 print:hidden">
    <div>
        <label for="from" class="mb-1 block text-sm font-medium">{{ __('reports.from') }}</label>
        <input id="from" type="date" name="from" value="{{ $from }}"
               class="rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
    </div>
    <div>
        <label for="to" class="mb-1 block text-sm font-medium">{{ __('reports.to') }}</label>
        <input id="to" type="date" name="to" value="{{ $to }}"
               class="rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
    </div>
    <button type="submit" class="rounded bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
        {{ __('reports.filter_button') }}
    </button>
</form>

<div class="mb-4 grid grid-cols-2 gap-4">
    <div class="rounded-lg bg-white p-4 shadow">
        <p class="text-sm text-slate-500">{{ __('reports.collected') }} ({{ $from }} → {{ $to }})</p>
        <p class="text-2xl font-bold">{{ number_format($collected, 2) }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 shadow">
        <p class="text-sm text-slate-500">{{ __('reports.outstanding') }}</p>
        <p class="text-2xl font-bold">{{ number_format($outstanding, 2) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <table class="w-full text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-2 text-start font-medium">{{ __('reports.course') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('reports.collected') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('reports.payments_count') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($byCourse as $row)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2">{{ $row->course }}</td>
                        <td class="px-4 py-2">{{ number_format($row->collected, 2) }}</td>
                        <td class="px-4 py-2">{{ $row->payments_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-slate-500">{{ __('reports.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow">
        <table class="w-full text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-2 text-start font-medium">{{ __('reports.day') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('reports.collected') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daily as $row)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2">{{ $row->day }}</td>
                        <td class="px-4 py-2">{{ number_format($row->collected, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-6 text-center text-slate-500">{{ __('reports.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
