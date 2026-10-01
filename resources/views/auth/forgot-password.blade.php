@extends('layouts.app')

@section('title', __('passwords.forgot_title'))

@section('content')
<div class="mx-auto max-w-md clay-card p-6">
    <h1 class="mb-2 font-display text-xl font-bold">{{ __('passwords.forgot_title') }}</h1>
    <p class="mb-6 text-sm text-slate-500">{{ __('passwords.forgot_hint') }}</p>

    @if (session('status'))
        <p class="mb-4 rounded bg-green-50 px-3 py-2 text-sm text-green-700">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="mb-1 block text-sm font-medium">{{ __('auth.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('passwords.send_link_button') }}
        </button>
    </form>

    <p class="mt-4 text-center text-sm">
        <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline">{{ __('auth.back_to_login') }}</a>
    </p>
</div>
@endsection
