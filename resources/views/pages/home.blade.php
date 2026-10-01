@extends('layouts.app')

@section('title', __('layout.home'))

@section('content')
<div class="overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-600 p-8 text-white shadow md:p-12">
    <div class="flex flex-col items-start gap-6 md:flex-row md:items-center md:justify-between">
        <div class="max-w-xl">
            <p class="mb-3 inline-block rounded-full bg-white/20 px-3 py-1 text-xs font-semibold">{{ __('pages.hero_badge') }}</p>
            <h1 class="text-3xl font-extrabold md:text-4xl">{{ __('pages.hero_title') }}</h1>
            <p class="mt-3 text-indigo-100">{{ __('pages.hero_subtitle') }}</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('login') }}" class="rounded bg-white px-5 py-2.5 text-sm font-bold text-indigo-700 hover:bg-indigo-50">
                    {{ __('pages.hero_cta_login') }}
                </a>
                <a href="{{ route('contact') }}" class="rounded border border-white/60 px-5 py-2.5 text-sm font-bold text-white hover:bg-white/10">
                    {{ __('pages.hero_cta_contact') }}
                </a>
            </div>
        </div>
        <x-logo :tagline="true" markSize="h-24 w-24" textSize="text-3xl" />
    </div>
</div>

<div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4">
    <div class="rounded-lg bg-white p-4 text-center shadow">
        <p class="text-2xl font-extrabold text-indigo-700">{{ $stats['courses'] }}</p>
        <p class="text-sm text-slate-500">{{ __('pages.stat_courses') }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 text-center shadow">
        <p class="text-2xl font-extrabold text-indigo-700">{{ $stats['groups'] }}</p>
        <p class="text-sm text-slate-500">{{ __('pages.stat_groups') }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 text-center shadow">
        <p class="text-2xl font-extrabold text-indigo-700">{{ $stats['teachers'] }}</p>
        <p class="text-sm text-slate-500">{{ __('pages.stat_teachers') }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 text-center shadow">
        <p class="text-2xl font-extrabold text-indigo-700">{{ $stats['students'] }}</p>
        <p class="text-sm text-slate-500">{{ __('pages.stat_students') }}</p>
    </div>
</div>

<div class="mt-10">
    <h2 class="text-center text-xl font-extrabold">{{ __('pages.features_title') }}</h2>
    <p class="mt-1 text-center text-sm text-slate-500">{{ __('pages.features_subtitle') }}</p>
    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
        @foreach (['enrollment', 'sessions', 'attendance', 'grades', 'payments', 'parent'] as $feature)
            <div class="rounded-lg bg-white p-5 shadow">
                <h3 class="font-bold text-indigo-700">{{ __("pages.feature_{$feature}_t") }}</h3>
                <p class="mt-1 text-sm text-slate-600">{{ __("pages.feature_{$feature}_d") }}</p>
            </div>
        @endforeach
    </div>
</div>

<div class="mt-10">
    <h2 class="text-center text-xl font-extrabold">{{ __('pages.courses_title') }}</h2>
    <p class="mt-1 text-center text-sm text-slate-500">{{ __('pages.courses_subtitle') }}</p>
    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
        @forelse ($featuredCourses as $course)
            <div class="rounded-lg bg-white p-5 shadow">
                <p class="text-xs font-semibold text-indigo-600">{{ $course->category ?? '—' }}</p>
                <h3 class="mt-1 font-bold">{{ $course->name }}</h3>
                <p class="mt-2 text-sm text-slate-600">{{ number_format($course->monthly_fee, 2) }} {{ __('pages.per_month') }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ __('pages.groups_open', ['count' => $course->groups_count]) }}</p>
            </div>
        @empty
            <p class="col-span-full rounded-lg bg-white p-6 text-center text-sm text-slate-500 shadow">{{ __('pages.courses_empty') }}</p>
        @endforelse
    </div>
</div>

<div class="mt-10 rounded-2xl bg-slate-900 p-8 text-center text-white shadow">
    <h2 class="text-xl font-extrabold">{{ __('pages.cta_title') }}</h2>
    <p class="mt-2 text-sm text-slate-300">{{ __('pages.cta_subtitle') }}</p>
    <a href="{{ route('contact') }}" class="mt-4 inline-block rounded bg-amber-400 px-6 py-2.5 text-sm font-bold text-slate-900 hover:bg-amber-300">
        {{ __('pages.cta_button') }}
    </a>
</div>
@endsection
