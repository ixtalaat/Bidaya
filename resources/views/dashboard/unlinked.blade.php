@extends('layouts.app')

@section('title', __('dashboard.unlinked_title'))

@section('content')
<div class="mx-auto max-w-xl clay-card p-6">
    <h1 class="font-display text-xl font-bold">{{ __('dashboard.unlinked_title') }}</h1>
    <p class="mt-2 text-sm text-slate-600">{{ __('dashboard.unlinked_body') }}</p>
</div>
@endsection
