@extends('layouts.app')

@section('title', __('reports.attendance_title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-xl font-bold">{{ __('reports.attendance_title') }}</h1>
    <button onclick="window.print()" class="rounded bg-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-800 hover:bg-slate-300 print:hidden">
        {{ __('reports.print_button') }}
    </button>
</div>

<form method="GET" action="{{ route('reports.attendance') }}" class="mb-4 flex flex-wrap items-end gap-2 print:hidden">
    <div>
        <label for="group_id" class="mb-1 block text-sm font-medium">{{ __('reports.group') }}</label>
        <select id="group_id" name="group_id" required
                class="rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            @foreach ($allGroups as $group)
                <option value="{{ $group->id }}" @selected((string) $groupId === (string) $group->id)>
                    {{ $group->name }} ({{ $group->course->name }})
                </option>
            @endforeach
        </select>
    </div>
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

@if ($groupId)
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <table class="w-full text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-2 text-start font-medium">{{ __('reports.student') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('reports.present') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('reports.total_sessions') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('reports.attendance_rate') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2">{{ $row['student'] }}</td>
                        <td class="px-4 py-2">{{ $row['attended'] }}</td>
                        <td class="px-4 py-2">{{ $row['total'] }}</td>
                        <td class="px-4 py-2">{{ $row['rate'] === null ? '—' : $row['rate'].'%' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-500">{{ __('reports.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@else
    <p class="rounded-lg bg-white p-6 text-sm text-slate-500 shadow">{{ __('reports.select_group') }}</p>
@endif
@endsection
