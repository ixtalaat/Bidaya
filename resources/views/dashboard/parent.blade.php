@extends('layouts.app')

@section('title', __('dashboard.parent_title'))

@section('content')
<h1 class="mb-6 text-xl font-bold">{{ __('dashboard.parent_title') }}</h1>

@forelse ($children as $child)
    <div class="mb-4 rounded-lg bg-white p-4 shadow">
        <h2 class="text-lg font-bold">{{ $child['student']->name }}</h2>
        <p class="mt-1 text-sm text-slate-500">
            {{ __('dashboard.active_courses_label') }}: {{ $child['enrollments']->count() }} —
            {{ __('dashboard.attendance_rate') }}: {{ $child['attendanceRate'] === null ? '—' : $child['attendanceRate'].'%' }} —
            {{ __('dashboard.outstanding') }}: {{ number_format($child['outstanding'], 2) }}
        </p>

        <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <h3 class="mb-1 text-sm font-bold">{{ __('dashboard.active_courses_label') }}</h3>
                <ul class="space-y-1 text-sm">
                    @forelse ($child['enrollments'] as $enrollment)
                        <li>{{ $enrollment->group->course->name }} ({{ $enrollment->group->name }})</li>
                    @empty
                        <li class="text-slate-500">—</li>
                    @endforelse
                </ul>
            </div>
            <div>
                <h3 class="mb-1 text-sm font-bold">{{ __('dashboard.upcoming_sessions') }}</h3>
                <ul class="space-y-1 text-sm">
                    @forelse ($child['upcomingSessions'] as $session)
                        <li>{{ $session->group->name }} — {{ $session->date->toDateString() }} {{ substr($session->start_time, 0, 5) }}</li>
                    @empty
                        <li class="text-slate-500">{{ __('dashboard.no_upcoming') }}</li>
                    @endforelse
                </ul>
            </div>
            <div>
                <h3 class="mb-1 text-sm font-bold">{{ __('dashboard.recent_grades') }}</h3>
                <ul class="space-y-1 text-sm">
                    @forelse ($child['recentGrades'] as $grade)
                        <li>{{ $grade->assessment->name }}: {{ number_format($grade->score, 2) }} / {{ number_format($grade->assessment->max_score, 2) }}</li>
                    @empty
                        <li class="text-slate-500">—</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@empty
    <p class="rounded-lg bg-white p-6 text-sm text-slate-500 shadow">{{ __('dashboard.no_children') }}</p>
@endforelse

<div class="rounded-lg bg-white p-4 shadow">
    <h2 class="mb-2 font-bold">{{ __('dashboard.notifications') }}</h2>
    <ul class="space-y-1 text-sm">
        @forelse ($unreadNotifications as $notification)
            <li>{{ $notification->data['message'] ?? $notification->type }}</li>
        @empty
            <li class="text-slate-500">{{ __('dashboard.no_notifications') }}</li>
        @endforelse
    </ul>
</div>
@endsection
