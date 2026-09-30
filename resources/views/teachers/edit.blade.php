@extends('layouts.app')

@section('title', __('teachers.edit_title'))

@section('content')
<div class="mx-auto max-w-2xl rounded-lg bg-white p-6 shadow">
    <h1 class="mb-6 text-xl font-bold">{{ __('teachers.edit_title') }}</h1>

    <form method="POST" action="{{ route('teachers.update', $teacher) }}">
        @csrf
        @method('PUT')
        @include('teachers._form')

        <button type="submit" class="mt-6 w-full rounded bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">
            {{ __('teachers.update_button') }}
        </button>
    </form>
</div>
@endsection
