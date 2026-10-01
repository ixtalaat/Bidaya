@extends('layouts.app')

@section('title', __('courses.create_title'))

@section('content')
<div class="mx-auto max-w-2xl clay-card p-6">
    <h1 class="mb-6 font-display text-xl font-bold">{{ __('courses.create_title') }}</h1>

    <form method="POST" action="{{ route('courses.store') }}">
        @csrf
        @include('courses._form', ['course' => new \App\Models\Course()])

        <button type="submit" class="mt-6 w-full rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('courses.save_button') }}
        </button>
    </form>
</div>
@endsection
