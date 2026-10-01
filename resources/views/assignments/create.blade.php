@extends('layouts.app')

@section('title', __('assignments.create_title'))

@section('content')
<div class="mx-auto max-w-2xl clay-card p-6">
    <h1 class="mb-2 font-display text-xl font-bold">{{ __('assignments.create_title') }}</h1>
    <p class="mb-6 text-sm text-slate-500">{{ $group->name }}</p>

    <form method="POST" action="{{ route('groups.assignments.store', $group) }}" enctype="multipart/form-data">
        @csrf
        @include('assignments._form', ['assignment' => new \App\Models\Assignment()])

        <button type="submit" class="clay-btn mt-6 w-full cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('assignments.save_button') }}
        </button>
    </form>
</div>
@endsection
