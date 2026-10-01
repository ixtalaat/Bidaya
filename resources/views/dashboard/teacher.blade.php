@extends('layouts.app')

@section('title', __('dashboard.teacher_title'))

@section('content')
<h1 class="mb-6 text-xl font-bold">{{ __('dashboard.teacher_title') }}</h1>

<div class="mb-4 rounded-lg bg-white p-4 shadow">
    <h2 class="mb-2 font-bold">{{ __('dashboard.today_sessions') }}</h2>
    <ul class="space-y-2 text-sm">
        @forelse ($todaySessions as $session)
            <li class="flex items-center justify-between gap-2">
                <span>
                    <a href="{{ route('sessions.show', $session) }}" class="text-indigo-700 hover:underline">{{ $session->group->name }}</a>
                    <span class="text-slate-500">{{ substr($session->start_time, 0, 5) }} – {{ substr($session->end_time, 0, 5) }} ({{ $session->status->label() }})</span>
                </span>
                <a href="{{ route('sessions.attendance.edit', $session) }}" class="shrink-0 rounded bg-indigo-700 px-3 py-1 text-sm font-semibold text-white hover:bg-indigo-800">
                    {{ __('attendance.title') }} ({{ $session->attendances->count() }})
                </a>
            </li>
        @empty
            <li class="text-slate-500">{{ __('dashboard.no_sessions_today') }}</li>
        @endforelse
    </ul>
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div class="rounded-lg bg-white p-4 shadow">
        <h2 class="mb-2 font-bold">{{ __('dashboard.upcoming_sessions') }}</h2>
        <ul class="space-y-1 text-sm">
            @forelse ($upcomingSessions as $session)
                <li>
                    <a href="{{ route('sessions.show', $session) }}" class="text-indigo-700 hover:underline">{{ $session->group->name }}</a>
                    <span class="text-slate-500">{{ $session->date->toDateString() }} {{ substr($session->start_time, 0, 5) }}</span>
                </li>
            @empty
                <li class="text-slate-500">{{ __('dashboard.no_upcoming') }}</li>
            @endforelse
        </ul>
    </div>

    <div class="rounded-lg bg-white p-4 shadow">
        <h2 class="mb-2 font-bold">{{ __('dashboard.my_groups') }}</h2>
        <ul class="space-y-1 text-sm">
            @forelse ($groups as $group)
                <li>
                    <a href="{{ route('groups.show', $group) }}" class="text-indigo-700 hover:underline">{{ $group->name }}</a>
                    <span class="text-slate-500">
                        {{ $group->course->name }} —
                        {{ __('dashboard.seats_filled', ['enrolled' => $group->active_enrollments_count, 'capacity' => $group->capacity]) }}
                    </span>
                </li>
            @empty
                <li class="text-slate-500">—</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
