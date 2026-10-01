@extends('layouts.app')

@section('title', __('passwords.reset_title'))

@section('content')
<div class="mx-auto max-w-md clay-card p-6">
    <h1 class="mb-6 font-display text-xl font-bold">{{ __('passwords.reset_title') }}</h1>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="mb-1 block text-sm font-medium">{{ __('auth.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email', request()->query('email')) }}" required autofocus
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

        <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-medium">{{ __('passwords.password_confirmation') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        </div>

        <button type="submit" class="w-full rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('passwords.reset_button') }}
        </button>
    </form>
</div>
@endsection
