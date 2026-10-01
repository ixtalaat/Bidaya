@extends('layouts.app')

@section('title', __('pages.contact_title'))

@section('content')
<div class="mx-auto max-w-3xl text-center">
    <h1 class="font-display text-3xl font-extrabold">{{ __('pages.contact_title') }}</h1>
    <p class="mt-2 text-slate-600">{{ __('pages.contact_subtitle') }}</p>
</div>

<div class="mx-auto mt-8 grid max-w-4xl grid-cols-1 gap-4 md:grid-cols-2">
    <div class="clay-card p-6">
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

    <div class="clay-card p-6">
        <h2 class="mb-4 font-extrabold">{{ __('pages.form_title') }}</h2>
        <form method="POST" action="{{ route('contact.send') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('pages.form_name') }}" required
                           class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="{{ __('pages.form_phone') }}" required
                           class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('pages.form_email') }}"
                       class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <input type="text" name="subject" value="{{ old('subject') }}" placeholder="{{ __('pages.form_subject') }}"
                       class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            </div>
            <div>
                <textarea name="message" rows="4" placeholder="{{ __('pages.form_message') }}" required
                          class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="clay-btn w-full cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
                {{ __('pages.form_send') }}
            </button>
        </form>
    </div>
</div>

<div class="mx-auto mt-6 max-w-4xl">
    <h2 class="mb-3 text-center font-display text-xl font-bold text-slate-900">{{ __('pages.map_title') }}</h2>
    <div class="overflow-hidden clay-card p-2">
        <iframe title="{{ __('pages.map_title') }}"
                src="https://www.google.com/maps?q=Mit%20Mohsen%2C%20Mit%20Ghamr%2C%20Dakahlia%2C%20Egypt&output=embed"
                class="h-80 w-full rounded-3xl border-0"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen></iframe>
    </div>
</div>
@endsection
