@extends('layouts.app')

@section('title', __('pages.about_title'))

@section('content')
<div class="mx-auto max-w-3xl text-center">
    <x-logo :tagline="true" markSize="h-20 w-20" textSize="text-3xl" />
    <h1 class="mt-4 text-3xl font-extrabold">{{ __('pages.about_title') }}</h1>
    <p class="mt-2 text-slate-600">{{ __('pages.about_subtitle') }}</p>
</div>

<div class="mx-auto mt-8 max-w-3xl rounded-lg bg-white p-6 shadow">
    <h2 class="font-extrabold">{{ __('pages.story_title') }}</h2>
    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ __('pages.story_p1') }}</p>
    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ __('pages.story_p2') }}</p>
</div>

<div class="mx-auto mt-4 grid max-w-3xl grid-cols-1 gap-4 md:grid-cols-2">
    <div class="rounded-lg bg-white p-6 shadow">
        <h2 class="font-extrabold text-indigo-700">{{ __('pages.mission_title') }}</h2>
        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ __('pages.mission_text') }}</p>
    </div>
    <div class="rounded-lg bg-white p-6 shadow">
        <h2 class="font-extrabold text-indigo-700">{{ __('pages.vision_title') }}</h2>
        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ __('pages.vision_text') }}</p>
    </div>
</div>

<div class="mx-auto mt-4 max-w-3xl rounded-lg bg-white p-6 shadow">
    <h2 class="font-extrabold">{{ __('pages.values_title') }}</h2>
    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
        @foreach (['care', 'clarity', 'growth'] as $value)
            <div class="rounded bg-slate-50 p-4">
                <h3 class="font-bold">{{ __("pages.value_{$value}_t") }}</h3>
                <p class="mt-1 text-sm text-slate-600">{{ __("pages.value_{$value}_d") }}</p>
            </div>
        @endforeach
    </div>
</div>

<div class="mx-auto mt-6 max-w-3xl text-center">
    <a href="{{ route('contact') }}" class="inline-block rounded bg-indigo-700 px-6 py-2.5 text-sm font-bold text-white hover:bg-indigo-800">
        {{ __('pages.cta_button') }}
    </a>
</div>
@endsection
