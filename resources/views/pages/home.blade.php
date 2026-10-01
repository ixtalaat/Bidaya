@extends('layouts.app')

@section('title', __('layout.home'))

@section('content')
<div class="relative overflow-hidden rounded-3xl border-[3px] border-slate-900 bg-background p-8 shadow-[8px_8px_0_#0f172a] md:p-12">
    <div class="pointer-events-none absolute -top-10 -end-10 h-40 w-40 rounded-full bg-secondary/30" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -bottom-12 -start-8 h-48 w-48 rounded-full bg-accent/20" aria-hidden="true"></div>
    <div class="relative flex flex-col items-start gap-8 md:flex-row md:items-center md:justify-between">
        <div class="max-w-xl">
            <p class="clay-chip mb-4 inline-block bg-white px-4 py-1 text-xs font-bold text-slate-900">{{ __('pages.hero_badge') }}</p>
            <h1 class="font-display text-4xl font-bold leading-tight text-slate-900 md:text-5xl">{{ __('pages.hero_title') }}</h1>
            <p class="mt-4 leading-relaxed text-slate-600">{{ __('pages.hero_subtitle') }}</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('login') }}" class="clay-chip cursor-pointer bg-secondary px-6 py-2.5 text-sm font-extrabold text-slate-900 transition-transform hover:-translate-y-0.5">
                    {{ __('pages.hero_cta_login') }}
                </a>
                <a href="{{ route('contact') }}" class="clay-chip cursor-pointer bg-white px-6 py-2.5 text-sm font-extrabold text-slate-900 transition-transform hover:-translate-y-0.5">
                    {{ __('pages.hero_cta_contact') }}
                </a>
            </div>
            <div class="mt-6 flex flex-wrap gap-2 text-xs font-bold text-slate-700">
                <span class="clay-chip bg-white px-3 py-1">{{ __('pages.chip_small_groups') }}</span>
                <span class="clay-chip bg-white px-3 py-1">{{ __('pages.chip_parent_updates') }}</span>
                <span class="clay-chip bg-white px-3 py-1">{{ __('pages.chip_teachers') }}</span>
            </div>
        </div>
        <div class="animate-float mx-auto shrink-0">
            <x-logo :tagline="true" markSize="h-28 w-28 md:h-36 md:w-36" textSize="text-4xl md:text-5xl" />
        </div>
    </div>
</div>

<div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4">
    @foreach ([['courses', $stats['courses'], __('pages.stat_courses')], ['groups', $stats['groups'], __('pages.stat_groups')], ['teachers', $stats['teachers'], __('pages.stat_teachers')], ['students', $stats['students'], __('pages.stat_students')]] as $stat)
        <div class="reveal clay-card p-4 text-center">
            <p class="font-display text-3xl font-bold text-primary">{{ $stat[1] }}</p>
            <p class="mt-1 text-sm font-semibold text-slate-600">{{ $stat[2] }}</p>
        </div>
    @endforeach
</div>

<div class="mt-12">
    <h2 class="text-center font-display text-2xl font-bold text-slate-900 md:text-3xl">{{ __('pages.features_title') }}</h2>
    <p class="mt-2 text-center text-sm text-slate-500">{{ __('pages.features_subtitle') }}</p>
    <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-3">
        @foreach ([['enrollment', 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z M22 21v-2a4 4 0 0 0-3-3.87 M16 3.13a4 4 0 0 1 0 7.75'], ['sessions', 'M19 4H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z M16 2v4 M8 2v4 M3 10h18'], ['attendance', 'M9 11l3 3L22 4 M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11'], ['grades', 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z'], ['payments', 'M1 4h22v16H1z M1 10h22'], ['parent', 'M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z']] as $feature)
            <div class="reveal clay-card p-5 transition-transform hover:-translate-y-1">
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border-2 border-slate-900 bg-background">
                    <svg viewBox="0 0 24 24" class="h-6 w-6 text-primary" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="{{ $feature[1] }}" />
                    </svg>
                </span>
                <h3 class="mt-3 font-display font-bold text-slate-900">{{ __("pages.feature_{$feature[0]}_t") }}</h3>
                <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ __("pages.feature_{$feature[0]}_d") }}</p>
            </div>
        @endforeach
    </div>
</div>

<div class="mt-12 rounded-3xl border-[3px] border-slate-900 bg-white p-8 shadow-[8px_8px_0_#0f172a]">
    <h2 class="text-center font-display text-2xl font-bold text-slate-900 md:text-3xl">{{ __('pages.how_title') }}</h2>
    <p class="mt-2 text-center text-sm text-slate-500">{{ __('pages.how_subtitle') }}</p>
    <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-3">
        @foreach ([['1', 'step_visit_t', 'step_visit_d', 'bg-secondary'], ['2', 'step_enroll_t', 'step_enroll_d', 'bg-primary'], ['3', 'step_track_t', 'step_track_d', 'bg-accent']] as $step)
            <div class="reveal rounded-2xl bg-muted p-5 text-center">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-full border-[3px] border-slate-900 {{ $step[3] }} font-display text-xl font-bold {{ $step[3] === 'bg-secondary' ? 'text-slate-900' : 'text-white' }}">{{ $step[0] }}</span>
                <h3 class="mt-3 font-bold text-slate-900">{{ __("pages.{$step[1]}") }}</h3>
                <p class="mt-1 text-sm text-slate-600">{{ __("pages.{$step[2]}") }}</p>
            </div>
        @endforeach
    </div>
</div>

<div class="mt-12">
    <h2 class="text-center font-display text-2xl font-bold text-slate-900 md:text-3xl">{{ __('pages.courses_title') }}</h2>
    <p class="mt-2 text-center text-sm text-slate-500">{{ __('pages.courses_subtitle') }}</p>
    <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-3">
        @forelse ($featuredCourses as $course)
            <div class="reveal clay-card flex flex-col p-5">
                <p class="clay-chip self-start bg-background px-3 py-0.5 text-xs font-bold text-slate-900">{{ $course->category ?? '—' }}</p>
                <h3 class="mt-3 font-display text-lg font-bold text-slate-900">{{ $course->name }}</h3>
                <p class="mt-2 text-sm font-extrabold text-primary">{{ number_format($course->monthly_fee, 2) }} {{ __('pages.per_month') }}</p>
                <p class="mt-1 text-xs font-semibold text-slate-500">{{ __('pages.groups_open', ['count' => $course->groups_count]) }}</p>
                <a href="{{ route('catalog.show', $course) }}" class="mt-4 inline-block cursor-pointer rounded-xl border-2 border-slate-900 bg-white px-4 py-2 text-center text-sm font-bold text-slate-900 transition-colors hover:bg-slate-900 hover:text-white">
                    {{ __('pages.view_details') }}
                </a>
            </div>
        @empty
            <p class="clay-card col-span-full p-6 text-center text-sm text-slate-500">{{ __('pages.courses_empty') }}</p>
        @endforelse
    </div>
</div>

<div class="reveal mt-12 rounded-3xl border-[3px] border-slate-900 bg-slate-900 p-8 text-center text-white shadow-[8px_8px_0_#f59e0b]">
    <h2 class="font-display text-2xl font-bold md:text-3xl">{{ __('pages.cta_title') }}</h2>
    <p class="mt-2 text-sm text-slate-300">{{ __('pages.cta_subtitle') }}</p>
    <a href="{{ route('contact') }}" class="mt-5 inline-block cursor-pointer rounded-full border-[3px] border-slate-900 bg-secondary px-8 py-2.5 text-sm font-extrabold text-slate-900 transition-transform hover:-translate-y-0.5">
        {{ __('pages.cta_button') }}
    </a>
</div>
@endsection
