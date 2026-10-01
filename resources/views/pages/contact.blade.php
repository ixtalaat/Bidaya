@extends('layouts.app')

@section('title', __('pages.contact_title'))

@section('content')
<div class="overflow-hidden rounded-3xl border-[3px] border-slate-900 bg-background p-8 text-center shadow-[8px_8px_0_#0f172a] md:p-10">
    <h1 class="font-display text-3xl font-bold text-slate-900 md:text-4xl">{{ __('pages.contact_title') }}</h1>
    <p class="mx-auto mt-2 max-w-xl text-slate-600">{{ __('pages.contact_subtitle') }}</p>
</div>

<div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="reveal clay-card p-4 text-center">
        <span class="mx-auto inline-flex h-11 w-11 items-center justify-center rounded-2xl border-2 border-slate-900 bg-background">
            <svg viewBox="0 0 24 24" class="h-6 w-6 text-primary" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" /><circle cx="12" cy="10" r="3" /></svg>
        </span>
        <p class="mt-2 text-sm font-semibold text-slate-700">{{ __('pages.contact_address') }}</p>
    </div>
    <div class="reveal clay-card p-4 text-center">
        <span class="mx-auto inline-flex h-11 w-11 items-center justify-center rounded-2xl border-2 border-slate-900 bg-background">
            <svg viewBox="0 0 24 24" class="h-6 w-6 text-primary" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z" /></svg>
        </span>
        <p class="mt-2 text-sm font-semibold text-slate-700" dir="ltr">{{ __('pages.contact_phone') }}</p>
    </div>
    <div class="reveal clay-card p-4 text-center">
        <span class="mx-auto inline-flex h-11 w-11 items-center justify-center rounded-2xl border-2 border-slate-900 bg-background">
            <svg viewBox="0 0 24 24" class="h-6 w-6 text-primary" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" /><path d="M22 6l-10 7L2 6" /></svg>
        </span>
        <p class="mt-2 text-sm font-semibold text-slate-700">{{ __('pages.contact_email') }}</p>
    </div>
    <div class="reveal clay-card p-4 text-center">
        <span class="mx-auto inline-flex h-11 w-11 items-center justify-center rounded-2xl border-2 border-slate-900 bg-background">
            <svg viewBox="0 0 24 24" class="h-6 w-6 text-primary" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10" /><path d="M12 6v6l4 2" /></svg>
        </span>
        <p class="mt-2 text-sm font-semibold text-slate-700">{{ __('pages.contact_hours') }}</p>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
    <div class="clay-card p-6">
        <h2 class="mb-4 font-display text-xl font-bold text-slate-900">{{ __('pages.form_title') }}</h2>
        <form method="POST" action="{{ route('contact.send') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('pages.form_name') }}" required
                           class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="{{ __('pages.form_phone') }}" required
                           class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('pages.form_email') }}"
                       class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <input type="text" name="subject" value="{{ old('subject') }}" placeholder="{{ __('pages.form_subject') }}"
                       class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            </div>
            <div>
                <textarea name="message" rows="4" placeholder="{{ __('pages.form_message') }}" required
                          class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="clay-btn w-full cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
                {{ __('pages.form_send') }}
            </button>
        </form>
    </div>

    <div class="flex flex-col gap-5">
        <div class="overflow-hidden clay-card flex-1 p-2">
            <iframe title="{{ __('pages.map_title') }}"
                    src="https://www.google.com/maps?q=Mit%20Mohsen%2C%20Mit%20Ghamr%2C%20Dakahlia%2C%20Egypt&output=embed"
                    class="h-80 w-full rounded-3xl border-0 lg:h-full lg:min-h-80"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    allowfullscreen></iframe>
        </div>
        <a href="https://www.google.com/maps/search/?api=1&query=Mit+Mohsen+Mit+Ghamr+Dakahlia+Egypt" target="_blank" rel="noopener"
           class="clay-btn inline-block cursor-pointer bg-white px-4 py-2 text-center text-sm font-extrabold text-slate-900">
            {{ __('pages.open_in_maps') }}
        </a>
    </div>
</div>

<div class="mx-auto mt-8 max-w-3xl">
    <h2 class="text-center font-display text-2xl font-bold text-slate-900">{{ __('pages.faq_title') }}</h2>
    <div class="mt-4 space-y-3">
        @foreach ([['faq_q1', 'faq_a1'], ['faq_q2', 'faq_a2'], ['faq_q3', 'faq_a3']] as $faq)
            <details class="reveal clay-card group p-4">
                <summary class="cursor-pointer list-none font-bold text-slate-900">
                    <span class="me-2 inline-flex h-7 w-7 items-center justify-center rounded-full border-2 border-slate-900 bg-secondary text-sm font-extrabold">?</span>
                    {{ __("pages.{$faq[0]}") }}
                </summary>
                <p class="mt-2 ps-9 text-sm leading-relaxed text-slate-600">{{ __("pages.{$faq[1]}") }}</p>
            </details>
        @endforeach
    </div>
</div>
@endsection
