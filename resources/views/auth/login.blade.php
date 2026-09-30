@extends('layouts.app')

@section('title', __('auth.login_title'))

@section('content')
<div class="mx-auto max-w-md rounded-lg bg-white p-6 shadow">
    <h1 class="mb-6 text-xl font-bold">{{ __('auth.login_title') }}</h1>

    @if (session('status'))
        <p class="mb-4 rounded bg-green-50 px-3 py-2 text-sm text-green-700">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="mb-1 block text-sm font-medium">{{ __('auth.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-medium">{{ __('auth.password_label') }}</label>
            <input id="password" type="password" name="password" required
                   class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" class="rounded border-slate-300">
                {{ __('auth.remember') }}
            </label>
            <a href="{{ route('password.request') }}" class="text-indigo-700 hover:underline">{{ __('auth.forgot_password') }}</a>
        </div>

        <button type="submit" class="w-full rounded bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">
            {{ __('auth.login_button') }}
        </button>
    </form>
</div>
@endsection
