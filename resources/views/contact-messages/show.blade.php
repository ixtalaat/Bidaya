@extends('layouts.app')

@section('title', __('pages.inbox_title'))

@section('content')
<div class="mx-auto max-w-2xl clay-card p-6">
    <p class="text-sm text-slate-500">{{ $message->created_at->toDateTimeString() }}</p>
    <h1 class="mt-1 font-display text-xl font-bold">{{ $message->name }}</h1>

    <dl class="mt-4 grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
        <div>
            <dt class="font-medium text-slate-500">{{ __('pages.form_phone') }}</dt>
            <dd dir="ltr" class="text-start">{{ $message->phone }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('pages.form_email') }}</dt>
            <dd>{{ $message->email ?? '—' }}</dd>
        </div>
    </dl>

    @if ($message->subject)
        <p class="mt-3 text-sm"><span class="font-bold">{{ __('pages.form_subject') }}:</span> {{ $message->subject }}</p>
    @endif

    <div class="mt-3 rounded-xl border-2 border-slate-900 bg-muted p-4 text-sm">
        {{ $message->message }}
    </div>

    <form method="POST" action="{{ route('contact-messages.destroy', $message) }}" class="mt-6" data-confirm="{{ __('pages.delete_confirm') }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="clay-btn cursor-pointer bg-red-600 px-4 py-2 text-sm font-extrabold text-white">
            {{ __('pages.delete_button') }}
        </button>
    </form>
</div>
@endsection
