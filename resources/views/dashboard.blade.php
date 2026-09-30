@extends('layouts.app')

@section('title', __('layout.dashboard'))

@section('content')
<div class="rounded-lg bg-white p-6 shadow">
    <h1 class="text-xl font-bold">{{ __('layout.dashboard') }}</h1>
    <p class="mt-2 text-sm text-slate-600">{{ auth()->user()->name }} — {{ auth()->user()->role->label() }}</p>

    @can('manage-center')
        <p class="mt-4 rounded bg-indigo-50 px-3 py-2 text-sm text-indigo-800">manage-center</p>
    @endcan
</div>
@endsection
