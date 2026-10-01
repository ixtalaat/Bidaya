@extends('layouts.app')

@section('title', __('teachers.create_title'))

@section('content')
<div class="mx-auto max-w-2xl clay-card p-6">
    <h1 class="mb-6 font-display text-xl font-bold">{{ __('teachers.create_title') }}</h1>

    <form method="POST" action="{{ route('teachers.store') }}">
        @csrf
        @include('teachers._form', ['teacher' => new \App\Models\Teacher()])

        <button type="submit" class="mt-6 w-full rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('teachers.save_button') }}
        </button>
    </form>
</div>
@endsection
