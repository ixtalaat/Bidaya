@extends('layouts.app')

@section('title', __('assessments.edit_title'))

@section('content')
<div class="mx-auto max-w-xl clay-card p-6">
    <h1 class="mb-6 font-display text-xl font-bold">{{ __('assessments.edit_title') }}</h1>

    <form method="POST" action="{{ route('groups.assessments.update', [$group, $assessment]) }}">
        @csrf
        @method('PUT')
        @include('assessments._form')

        <button type="submit" class="mt-6 w-full rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('assessments.update_button') }}
        </button>
    </form>
</div>
@endsection
