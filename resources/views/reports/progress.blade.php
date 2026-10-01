@extends('layouts.app')

@section('title', __('reports.progress_title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="font-display text-xl font-bold">{{ __('reports.progress_title') }}</h1>
    <button onclick="window.print()" class="clay-btn cursor-pointer bg-white px-3 py-1.5 text-sm font-extrabold text-slate-900 print:hidden">
        {{ __('reports.print_button') }}
    </button>
</div>

<form method="GET" action="{{ route('reports.progress') }}" class="mb-4 flex flex-wrap items-end gap-2 print:hidden">
    <div>
        <label for="group_id" class="mb-1 block text-sm font-medium">{{ __('reports.group') }}</label>
        <select id="group_id" name="group_id" required
                class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            @foreach ($allGroups as $group)
                <option value="{{ $group->id }}" @selected((string) $groupId === (string) $group->id)>
                    {{ $group->name }} ({{ $group->course->name }})
                </option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="rounded clay-btn cursor-pointer bg-white px-4 py-2 text-sm font-extrabold text-slate-900">
        {{ __('reports.filter_button') }}
    </button>
</form>

@if ($groupId)
    <div class="overflow-hidden clay-card">
        <table class="w-full text-sm">
            <thead class="bg-background">
                <tr>
                    <th class="px-4 py-2 text-start font-medium">{{ __('reports.student') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('reports.status') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('reports.attendance_rate') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('reports.average_score') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('reports.outstanding') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2">{{ $row['student'] }}</td>
                        <td class="px-4 py-2">{{ $row['status'] }}</td>
                        <td class="px-4 py-2">{{ $row['attendanceRate'] === null ? '—' : $row['attendanceRate'].'%' }}</td>
                        <td class="px-4 py-2">{{ $row['averageScore'] === null ? '—' : number_format($row['averageScore'], 2).' ('.$row['gradesCount'].')' }}</td>
                        <td class="px-4 py-2">{{ number_format($row['outstanding'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-slate-500">{{ __('reports.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@else
    <p class="clay-card p-6 text-sm text-slate-500">{{ __('reports.select_group') }}</p>
@endif
@endsection
