<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Bidaya') }} — @yield('title')</title>
    <meta name="theme-color" content="#2563eb">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Marhey:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-background text-slate-900">
<header class="border-b-[3px] border-slate-900 bg-white">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-3">
        <x-logo :tagline="true" />
        <nav class="flex flex-wrap items-center gap-3 text-sm">
            <a href="{{ route('home') }}" class="text-slate-600 hover:text-indigo-700">{{ __('layout.home') }}</a>
            <a href="{{ route('about') }}" class="text-slate-600 hover:text-indigo-700">{{ __('pages.about_nav') }}</a>
            <a href="{{ route('contact') }}" class="text-slate-600 hover:text-indigo-700">{{ __('pages.contact_nav') }}</a>
            <a href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="text-slate-600 hover:text-indigo-700">
                {{ __('layout.switch_language') }}
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="text-slate-600 hover:text-indigo-700">{{ __('layout.dashboard') }}</a>
                <a href="{{ route('sessions.index') }}" class="text-slate-600 hover:text-indigo-700">{{ __('sessions.title') }}</a>
                <a href="{{ route('notifications.index') }}" class="text-slate-600 hover:text-indigo-700">
                    {{ __('notifications.title') }}
                    @if (($unreadNotificationsCount ?? 0) > 0)
                        <span class="rounded-full bg-red-600 px-1.5 text-xs font-bold text-white">{{ $unreadNotificationsCount }}</span>
                    @endif
                </a>
                @can('manage-center')
                    <a href="{{ route('courses.index') }}" class="text-slate-600 hover:text-indigo-700">{{ __('courses.title') }}</a>
                    <a href="{{ route('groups.index') }}" class="text-slate-600 hover:text-indigo-700">{{ __('groups.title') }}</a>
                    <a href="{{ route('enrollments.index') }}" class="text-slate-600 hover:text-indigo-700">{{ __('enrollments.title') }}</a>
                    <a href="{{ route('teachers.index') }}" class="text-slate-600 hover:text-indigo-700">{{ __('teachers.title') }}</a>
                    <a href="{{ route('students.index') }}" class="text-slate-600 hover:text-indigo-700">{{ __('students.title') }}</a>
                    <a href="{{ route('parents.index') }}" class="text-slate-600 hover:text-indigo-700">{{ __('guardians.title') }}</a>
                    <a href="{{ route('reports.attendance') }}" class="text-slate-600 hover:text-indigo-700">{{ __('reports.attendance_title') }}</a>
                    <a href="{{ route('reports.progress') }}" class="text-slate-600 hover:text-indigo-700">{{ __('reports.progress_title') }}</a>
                    <a href="{{ route('reports.courses') }}" class="text-slate-600 hover:text-indigo-700">{{ __('reports.courses_title') }}</a>
                    <a href="{{ route('audit-logs.index') }}" class="text-slate-600 hover:text-indigo-700">{{ __('reports.audit_title') }}</a>
                @endcan
                @can('record-payments')
                    <a href="{{ route('payments.index') }}" class="text-slate-600 hover:text-indigo-700">{{ __('payments.title') }}</a>
                    <a href="{{ route('reports.financial') }}" class="text-slate-600 hover:text-indigo-700">{{ __('reports.financial_title') }}</a>
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
<main class="mx-auto w-full max-w-6xl grow px-4 py-8">
    <div id="flash-data" class="hidden" data-success="{{ session('status') }}" data-errors='@json($errors->all())' data-confirm-button="{{ __('layout.confirm_button') }}" data-cancel-button="{{ __('layout.cancel_button') }}"></div>
    @yield('content')
</main>
<footer class="border-t-[3px] border-slate-900 bg-white">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-6">
        <x-logo :tagline="true" markSize="h-8 w-8" textSize="text-lg" />
        <nav class="flex flex-wrap items-center gap-4 text-sm text-slate-600">
            <a href="{{ route('home') }}" class="hover:text-indigo-700">{{ __('layout.home') }}</a>
            <a href="{{ route('about') }}" class="hover:text-indigo-700">{{ __('pages.about_nav') }}</a>
            <a href="{{ route('contact') }}" class="hover:text-indigo-700">{{ __('pages.contact_nav') }}</a>
            <a href="{{ route('login') }}" class="hover:text-indigo-700">{{ __('auth.login_button') }}</a>
        </nav>
        <p class="w-full text-center text-xs text-slate-400">{{ __('layout.footer_rights', ['year' => date('Y')]) }}</p>
    </div>
</footer>
</body>
</html>
