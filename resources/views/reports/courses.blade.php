@extends('layouts.app')

@section('title', __('reports.courses_title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="font-display text-xl font-bold">{{ __('reports.courses_title') }}</h1>
    <button onclick="window.print()" class="clay-btn cursor-pointer bg-white px-3 py-1.5 text-sm font-extrabold text-slate-900 print:hidden">
        {{ __('reports.print_button') }}
    </button>
</div>

<div class="overflow-hidden clay-card">
    <table class="w-full text-sm">
        <thead class="bg-background">
            <tr>
                <th class="px-4 py-2 text-start font-medium">{{ __('reports.course') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('reports.groups_count') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('reports.enrolled') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('reports.attendance_rate') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('reports.revenue') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2">{{ $row['course'] }}</td>
                    <td class="px-4 py-2">{{ $row['groups'] }}</td>
                    <td class="px-4 py-2">{{ $row['enrolled'] }}</td>
                    <td class="px-4 py-2">{{ $row['attendanceRate'] === null ? '—' : $row['attendanceRate'].'%' }}</td>
                    <td class="px-4 py-2">{{ number_format($row['revenue'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-slate-500">{{ __('reports.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
