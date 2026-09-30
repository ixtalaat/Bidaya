@extends('layouts.app')

@section('title', __('teachers.create_title'))

@section('content')
<div class="mx-auto max-w-2xl rounded-lg bg-white p-6 shadow">
    <h1 class="mb-6 text-xl font-bold">{{ __('teachers.create_title') }}</h1>

    <form method="POST" action="{{ route('teachers.store') }}">
        @csrf
        @include('teachers._form', ['teacher' => new \App\Models\Teacher()])

        <button type="submit" class="mt-6 w-full rounded bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">
            {{ __('teachers.save_button') }}
        </button>
    </form>
</div>
@endsection
