@extends('layouts.app')

@section('title', __('dashboard.admin_title'))

@section('content')
<h1 class="mb-6 text-xl font-bold">{{ __('dashboard.admin_title') }}</h1>

<div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-5">
    <div class="rounded-lg bg-white p-4 shadow">
        <p class="text-sm text-slate-500">{{ __('dashboard.active_students') }}</p>
        <p class="text-2xl font-bold">{{ $activeStudents }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 shadow">
        <p class="text-sm text-slate-500">{{ __('dashboard.active_courses') }}</p>
        <p class="text-2xl font-bold">{{ $activeCourses }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 shadow">
        <p class="text-sm text-slate-500">{{ __('dashboard.active_groups') }}</p>
        <p class="text-2xl font-bold">{{ $activeGroups }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 shadow">
        <p class="text-sm text-slate-500">{{ __('dashboard.active_teachers') }}</p>
        <p class="text-2xl font-bold">{{ $activeTeachers }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 shadow">
        <p class="text-sm text-slate-500">{{ __('dashboard.outstanding') }}</p>
        <p class="text-2xl font-bold">{{ number_format($outstanding, 2) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div class="rounded-lg bg-white p-4 shadow">
        <h2 class="mb-2 font-bold">{{ __('dashboard.today_sessions') }} ({{ $todaySessions->count() }})</h2>
        <p class="mb-2 text-sm text-slate-500">
            {{ __('dashboard.attendance_today') }}:
            {{ $attendanceToday['rate'] === null ? '—' : $attendanceToday['rate'].'% ('.$attendanceToday['attended'].'/'.$attendanceToday['total'].')' }}
        </p>
        <ul class="space-y-1 text-sm">
            @forelse ($todaySessions as $session)
                <li>
                    <a href="{{ route('sessions.show', $session) }}" class="text-indigo-700 hover:underline">{{ $session->group->name }}</a>
                    <span class="text-slate-500">{{ substr($session->start_time, 0, 5) }} — {{ $session->teacher->user->name }} ({{ $session->status->label() }})</span>
                </li>
            @empty
                <li class="text-slate-500">{{ __('dashboard.no_sessions_today') }}</li>
            @endforelse
        </ul>
    </div>

    <div class="rounded-lg bg-white p-4 shadow">
        <h2 class="mb-2 font-bold">{{ __('dashboard.recent_enrollments') }}</h2>
        <ul class="space-y-1 text-sm">
            @forelse ($recentEnrollments as $enrollment)
                <li>
                    <a href="{{ route('enrollments.show', $enrollment) }}" class="text-indigo-700 hover:underline">{{ $enrollment->student->name }}</a>
                    <span class="text-slate-500">{{ $enrollment->group->name }} ({{ $enrollment->status->label() }})</span>
                </li>
            @empty
                <li class="text-slate-500">—</li>
            @endforelse
        </ul>

        <h2 class="mb-2 mt-4 font-bold">{{ __('dashboard.recent_payments') }}</h2>
        <ul class="space-y-1 text-sm">
            @forelse ($recentPayments as $payment)
                <li>
                    <a href="{{ route('payments.show', $payment) }}" class="text-indigo-700 hover:underline">#{{ $payment->id }}</a>
                    <span class="text-slate-500">{{ $payment->invoice->enrollment->student->name }} — {{ number_format($payment->amount, 2) }}</span>
                </li>
            @empty
                <li class="text-slate-500">—</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
