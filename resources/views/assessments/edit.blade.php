@extends('layouts.app')

@section('title', __('assessments.edit_title'))

@section('content')
<div class="mx-auto max-w-xl rounded-lg bg-white p-6 shadow">
    <h1 class="mb-6 text-xl font-bold">{{ __('assessments.edit_title') }}</h1>

    <form method="POST" action="{{ route('groups.assessments.update', [$group, $assessment]) }}">
        @csrf
        @method('PUT')
        @include('assessments._form')

        <button type="submit" class="mt-6 w-full rounded bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">
            {{ __('assessments.update_button') }}
        </button>
    </form>
</div>
@endsection
