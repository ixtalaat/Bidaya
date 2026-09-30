@extends('layouts.app')

@section('title', __('guardians.create_title'))

@section('content')
<div class="mx-auto max-w-xl rounded-lg bg-white p-6 shadow">
    <h1 class="mb-6 text-xl font-bold">{{ __('guardians.create_title') }}</h1>

    <form method="POST" action="{{ route('parents.store') }}">
        @csrf
        @include('parents._form', ['guardian' => new \App\Models\Guardian()])

        <button type="submit" class="mt-6 w-full rounded bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">
            {{ __('guardians.save_button') }}
        </button>
    </form>
</div>
@endsection
