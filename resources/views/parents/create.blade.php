@extends('layouts.app')

@section('title', __('guardians.create_title'))

@section('content')
<div class="mx-auto max-w-xl clay-card p-6">
    <h1 class="mb-6 font-display text-xl font-bold">{{ __('guardians.create_title') }}</h1>

    <form method="POST" action="{{ route('parents.store') }}">
        @csrf
        @include('parents._form', ['guardian' => new \App\Models\Guardian()])

        <button type="submit" class="mt-6 w-full rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('guardians.save_button') }}
        </button>
    </form>
</div>
@endsection
