@extends('layouts.app')

@section('title', __('pages.contact_title'))

@section('content')
<div class="mx-auto max-w-3xl text-center">
    <h1 class="text-3xl font-extrabold">{{ __('pages.contact_title') }}</h1>
    <p class="mt-2 text-slate-600">{{ __('pages.contact_subtitle') }}</p>
</div>

<div class="mx-auto mt-8 grid max-w-4xl grid-cols-1 gap-4 md:grid-cols-2">
    <div class="rounded-lg bg-white p-6 shadow">
        <h2 class="font-extrabold">{{ __('pages.info_title') }}</h2>
        <dl class="mt-4 space-y-3 text-sm">
            <div>
                <dt class="font-medium text-slate-500">📍</dt>
                <dd>{{ __('pages.contact_address') }}</dd>
            </div>
            <div>
                <dt class="font-medium text-slate-500">📞</dt>
                <dd dir="ltr" class="text-end">{{ __('pages.contact_phone') }}</dd>
            </div>
            <div>
                <dt class="font-medium text-slate-500">✉️</dt>
                <dd>{{ __('pages.contact_email') }}</dd>
            </div>
            <div>
                <dt class="font-medium text-slate-500">🕙</dt>
                <dd>{{ __('pages.contact_hours') }}</dd>
            </div>
        </dl>
    </div>

    <div class="rounded-lg bg-white p-6 shadow">
        <h2 class="mb-4 font-extrabold">{{ __('pages.form_title') }}</h2>
        <form method="POST" action="{{ route('contact.send') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('pages.form_name') }}" required
                           class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="{{ __('pages.form_phone') }}" required
                           class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('pages.form_email') }}"
                       class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <input type="text" name="subject" value="{{ old('subject') }}" placeholder="{{ __('pages.form_subject') }}"
                       class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            </div>
            <div>
                <textarea name="message" rows="4" placeholder="{{ __('pages.form_message') }}" required
                          class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="w-full rounded bg-indigo-700 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-800">
                {{ __('pages.form_send') }}
            </button>
        </form>
    </div>
</div>
@endsection
