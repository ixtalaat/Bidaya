@extends('layouts.app')

@section('title', $course->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('catalog') }}" class="text-sm font-semibold text-primary hover:underline">← {{ __('pages.back_to_catalog') }}</a>
</div>

<div class="overflow-hidden rounded-3xl border-[3px] border-slate-900 bg-white p-8 shadow-[8px_8px_0_#0f172a] md:p-10">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-xl">
            <p class="clay-chip inline-block bg-background px-3 py-0.5 text-xs font-bold text-slate-900">{{ $course->category ?? '—' }}</p>
            <h1 class="mt-3 font-display text-3xl font-bold text-slate-900 md:text-4xl">{{ $course->name }}</h1>
            @if ($course->description)
                <p class="mt-3 leading-relaxed text-slate-600">{{ $course->description }}</p>
            @endif
        </div>
        <div class="clay-card shrink-0 bg-background p-5 text-center">
            <p class="font-display text-3xl font-bold text-primary">{{ number_format($course->monthly_fee, 2) }}</p>
            <p class="text-xs font-bold text-slate-500">{{ __('pages.per_month') }}</p>
            <a href="{{ route('contact') }}" class="clay-btn mt-3 inline-block cursor-pointer bg-secondary px-5 py-2 text-sm font-extrabold text-slate-900">
                {{ __('pages.details_ask') }}
            </a>
        </div>
    </div>

    <dl class="mt-6 grid grid-cols-2 gap-3 text-sm md:grid-cols-4">
        <div class="rounded-2xl bg-muted p-3">
            <dt class="font-bold text-slate-500">{{ __('courses.level') }}</dt>
            <dd class="mt-1 font-semibold">{{ $course->level ?? '—' }}</dd>
        </div>
        <div class="rounded-2xl bg-muted p-3">
            <dt class="font-bold text-slate-500">{{ __('courses.min_age') }} / {{ __('courses.max_age') }}</dt>
            <dd class="mt-1 font-semibold">
                @if ($course->min_age !== null && $course->max_age !== null)
                    {{ __('courses.age_range', ['min' => $course->min_age, 'max' => $course->max_age]) }}
                @else
                    —
                @endif
            </dd>
        </div>
        <div class="rounded-2xl bg-muted p-3">
            <dt class="font-bold text-slate-500">{{ __('courses.duration_weeks') }}</dt>
            <dd class="mt-1 font-semibold">{{ $course->duration_weeks ?? '—' }}</dd>
        </div>
        <div class="rounded-2xl bg-muted p-3">
            <dt class="font-bold text-slate-500">{{ __('courses.sessions_count') }}</dt>
            <dd class="mt-1 font-semibold">{{ $course->sessions_count ?? '—' }}</dd>
        </div>
    </dl>

    @if ($course->prerequisites)
        <p class="mt-4 text-sm text-slate-600"><span class="font-bold text-slate-900">{{ __('courses.prerequisites') }}:</span> {{ $course->prerequisites }}</p>
    @endif
</div>

<div class="mt-8">
    <h2 class="font-display text-2xl font-bold text-slate-900">{{ __('pages.details_groups_title') }}</h2>
    <div class="mt-4 grid grid-cols-1 gap-5 md:grid-cols-2">
        @forelse ($course->groups as $group)
            <div class="clay-card p-5">
                <div class="flex items-start justify-between gap-2">
                    <h3 class="font-display text-lg font-bold text-slate-900">{{ $group->name }}</h3>
                    <span class="clay-chip bg-background px-3 py-0.5 text-xs font-bold text-slate-900">
                        {{ __('pages.details_seats', ['available' => $group->availableSeats(), 'capacity' => $group->capacity]) }}
                    </span>
                </div>
                <p class="mt-2 text-sm text-slate-600">
                    <span class="font-bold text-slate-900">{{ __('pages.details_teacher') }}:</span> {{ $group->teacher->user->name }}
                </p>
                <div class="mt-2 text-sm text-slate-600">
                    <p class="font-bold text-slate-900">{{ __('pages.details_schedule') }}:</p>
                    @forelse ($group->schedules as $schedule)
                        <p>{{ $schedule->day_of_week->label() }} — {{ substr($schedule->start_time, 0, 5) }} – {{ substr($schedule->end_time, 0, 5) }}{{ $schedule->classroom ? ' ('.$schedule->classroom.')' : '' }}</p>
                    @empty
                        <p>{{ __('pages.details_no_schedule') }}</p>
                    @endforelse
                </div>
            </div>
        @empty
            <p class="clay-card col-span-full p-6 text-center text-sm text-slate-500">{{ __('pages.details_no_groups') }}</p>
        @endforelse
    </div>
</div>

<div class="mt-8 rounded-3xl border-[3px] border-slate-900 bg-slate-900 p-8 text-center text-white shadow-[8px_8px_0_#f59e0b]">
    <h2 class="font-display text-2xl font-bold">{{ __('pages.cta_title') }}</h2>
    <p class="mt-2 text-sm text-slate-300">{{ __('pages.cta_subtitle') }}</p>
    <a href="{{ route('contact') }}" class="mt-5 inline-block cursor-pointer rounded-full border-[3px] border-slate-900 bg-secondary px-8 py-2.5 text-sm font-extrabold text-slate-900 transition-transform hover:-translate-y-0.5">
        {{ __('pages.cta_button') }}
    </a>
</div>
@endsection
