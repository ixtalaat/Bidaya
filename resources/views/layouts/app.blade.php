<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Bidaya') }} — @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3">
        <a href="{{ route('home') }}" class="text-lg font-bold text-indigo-700">
            {{ config('app.name', 'Bidaya') }}
            <span class="ms-2 text-sm font-normal text-slate-500">{{ __('layout.tagline') }}</span>
        </a>
        <nav class="flex items-center gap-3 text-sm">
            <a href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="text-slate-600 hover:text-indigo-700">
                {{ __('layout.switch_language') }}
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="text-slate-600 hover:text-indigo-700">{{ __('layout.dashboard') }}</a>
                <a href="{{ route('sessions.index') }}" class="text-slate-600 hover:text-indigo-700">{{ __('sessions.title') }}</a>
                @can('manage-center')
                    <a href="{{ route('courses.index') }}" class="text-slate-600 hover:text-indigo-700">{{ __('courses.title') }}</a>
                    <a href="{{ route('groups.index') }}" class="text-slate-600 hover:text-indigo-700">{{ __('groups.title') }}</a>
                    <a href="{{ route('enrollments.index') }}" class="text-slate-600 hover:text-indigo-700">{{ __('enrollments.title') }}</a>
                    <a href="{{ route('teachers.index') }}" class="text-slate-600 hover:text-indigo-700">{{ __('teachers.title') }}</a>
                    <a href="{{ route('students.index') }}" class="text-slate-600 hover:text-indigo-700">{{ __('students.title') }}</a>
                    <a href="{{ route('parents.index') }}" class="text-slate-600 hover:text-indigo-700">{{ __('guardians.title') }}</a>
                @endcan
                <span class="text-slate-400">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-slate-600 hover:text-indigo-700">{{ __('layout.logout') }}</button>
                </form>
            @endauth
        </nav>
    </div>
</header>
<main class="mx-auto max-w-5xl px-4 py-8">
    @if (session('status'))
        <p class="mb-4 rounded bg-green-50 px-3 py-2 text-sm text-green-700">{{ session('status') }}</p>
    @endif
    @yield('content')
</main>
</body>
</html>
