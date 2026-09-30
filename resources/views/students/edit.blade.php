@extends('layouts.app')

@section('title', __('students.edit_title'))

@section('content')
<div class="mx-auto max-w-2xl rounded-lg bg-white p-6 shadow">
    <h1 class="mb-6 text-xl font-bold">{{ __('students.edit_title') }}</h1>

    <form method="POST" action="{{ route('students.update', $student) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('students._form')

        <button type="submit" class="mt-6 w-full rounded bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">
            {{ __('students.update_button') }}
        </button>
    </form>
</div>
@endsection
