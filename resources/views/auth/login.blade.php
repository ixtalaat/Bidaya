@extends('layouts.app')

@section('title', __('auth.login_title'))

@section('content')
<div class="mx-auto max-w-md clay-card p-6">
    <h1 class="mb-6 font-display text-xl font-bold">{{ __('auth.login_title') }}</h1>

    @if (session('status'))
        <p class="mb-4 rounded bg-green-50 px-3 py-2 text-sm text-green-700">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="mb-1 block text-sm font-medium">{{ __('auth.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-medium">{{ __('auth.password_label') }}</label>
            <input id="password" type="password" name="password" required
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" class="rounded border-slate-300">
                {{ __('auth.remember') }}
            </label>
            <a href="{{ route('password.request') }}" class="text-primary font-semibold hover:underline">{{ __('auth.forgot_password') }}</a>
        </div>

        <button type="submit" class="w-full rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('auth.login_button') }}
        </button>
    </form>

    <p class="mt-4 rounded-xl border-2 border-slate-900 bg-muted px-3 py-2 text-center text-xs text-slate-600">
        {{ __('auth.parent_hint') }}
        <a href="{{ route('contact') }}" class="font-bold text-primary hover:underline">{{ __('pages.contact_nav') }}</a>
    </p>
</div>
@endsection
