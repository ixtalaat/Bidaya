@extends('layouts.app')

@section('title', __('assignments.edit_title'))

@section('content')
<div class="mx-auto max-w-2xl clay-card p-6">
    <h1 class="mb-6 font-display text-xl font-bold">{{ __('assignments.edit_title') }}</h1>

    <form method="POST" action="{{ route('groups.assignments.update', [$group, $assignment]) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('assignments._form')

        <button type="submit" class="clay-btn mt-6 w-full cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('assignments.update_button') }}
        </button>
    </form>
</div>
@endsection
