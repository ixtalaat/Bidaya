@extends('layouts.app')

@section('title', __('pages.catalog_title'))

@section('content')
<div class="mx-auto max-w-3xl text-center">
    <h1 class="font-display text-3xl font-bold text-slate-900">{{ __('pages.catalog_title') }}</h1>
    <p class="mt-2 text-slate-600">{{ __('pages.catalog_subtitle') }}</p>
</div>

<div class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-3">
    @forelse ($courses as $course)
        <div class="reveal clay-card flex flex-col p-5">
            <p class="clay-chip self-start bg-background px-3 py-0.5 text-xs font-bold text-slate-900">{{ $course->category ?? '—' }}</p>
            <h2 class="mt-3 font-display text-lg font-bold text-slate-900">{{ $course->name }}</h2>
            @if ($course->description)
                <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $course->description }}</p>
            @endif
            <p class="mt-2 text-sm font-extrabold text-primary">{{ number_format($course->monthly_fee, 2) }} {{ __('pages.per_month') }}</p>
            <p class="mt-1 text-xs font-semibold text-slate-500">{{ __('pages.groups_open', ['count' => $course->groups_count]) }}</p>
            <a href="{{ route('catalog.show', $course) }}" class="mt-4 inline-block cursor-pointer rounded-xl border-2 border-slate-900 bg-white px-4 py-2 text-center text-sm font-bold text-slate-900 transition-colors hover:bg-slate-900 hover:text-white">
                {{ __('pages.view_details') }}
            </a>
        </div>
    @empty
        <p class="clay-card col-span-full p-6 text-center text-sm text-slate-500">{{ __('pages.catalog_empty') }}</p>
    @endforelse
</div>
@endsection
