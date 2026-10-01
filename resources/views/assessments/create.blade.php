@extends('layouts.app')

@section('title', __('assessments.create_title'))

@section('content')
<div class="mx-auto max-w-xl clay-card p-6">
    <h1 class="mb-2 font-display text-xl font-bold">{{ __('assessments.create_title') }}</h1>
    <p class="mb-6 text-sm text-slate-500">{{ $group->name }}</p>

    <form method="POST" action="{{ route('groups.assessments.store', $group) }}">
        @csrf
        @include('assessments._form', ['assessment' => new \App\Models\Assessment()])

        <button type="submit" class="mt-6 w-full rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('assessments.save_button') }}
        </button>
    </form>
</div>
@endsection
