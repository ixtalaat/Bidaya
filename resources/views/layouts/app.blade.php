<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('layout.app_name') }} — @yield('title')</title>
    <meta name="theme-color" content="#2563eb">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Marhey:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-background text-slate-900">
<header class="sticky top-0 z-40 border-b-[3px] border-slate-900 bg-white/95 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3">
        <x-logo />
        <nav class="hidden items-center gap-1 text-sm lg:flex" aria-label="Primary">
            @guest
                <x-nav-link :href="route('home')" :active="request()->routeIs('home')">{{ __('layout.home') }}</x-nav-link>
                <x-nav-link :href="route('catalog')" :active="request()->routeIs('catalog*')">{{ __('pages.courses_nav') }}</x-nav-link>
                <x-nav-link :href="route('gallery')" :active="request()->routeIs('gallery')">{{ __('gallery.title') }}</x-nav-link>
                <x-nav-link :href="route('about')" :active="request()->routeIs('about')">{{ __('pages.about_nav') }}</x-nav-link>
                <x-nav-link :href="route('contact')" :active="request()->routeIs('contact')">{{ __('pages.contact_nav') }}</x-nav-link>
            @endguest
            @auth
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('layout.dashboard') }}</x-nav-link>
                @if (auth()->user()->hasRole(\App\Enums\Role::SuperAdmin, \App\Enums\Role::Manager, \App\Enums\Role::Teacher))
                    <x-nav-link :href="route('sessions.index')" :active="request()->routeIs('sessions.*')">{{ __('sessions.title') }}</x-nav-link>
                @endif
                @can('manage-center')
                    <div class="relative">
                        <button type="button" data-dropdown-toggle="menu-quick-add" aria-expanded="false" aria-haspopup="true"
                                class="clay-btn flex cursor-pointer items-center gap-1 bg-secondary px-3 py-1.5 text-sm font-extrabold text-slate-900">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14 M5 12h14" /></svg>
                            {{ __('layout.quick_create') }}
                        </button>
                        <div id="menu-quick-add" data-dropdown-menu class="clay-card absolute start-0 top-full z-50 mt-2 hidden w-56 p-2">
                            <x-nav-link dropdownItem :href="route('students.create')">{{ __('students.create_title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('parents.create')">{{ __('guardians.create_title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('courses.create')">{{ __('courses.create_title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('groups.create')">{{ __('groups.create_title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('teachers.create')">{{ __('teachers.create_title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('enrollments.create')">{{ __('enrollments.create_title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('sessions.create')">{{ __('sessions.create_title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('payments.create')">{{ __('payments.create_title') }}</x-nav-link>
                        </div>
                    </div>
                    <div class="relative">
                        <button type="button" data-dropdown-toggle="menu-manage" aria-expanded="false" aria-haspopup="true"
                                class="flex cursor-pointer items-center gap-1 rounded-lg px-2.5 py-2 font-semibold text-slate-600 transition-colors hover:bg-muted hover:text-primary {{ request()->routeIs('courses.*', 'groups.*', 'enrollments.*', 'teachers.*', 'students.*', 'parents.*') ? 'bg-muted font-extrabold text-primary' : '' }}">
                            {{ __('layout.manage') }}
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6" /></svg>
                        </button>
                        <div id="menu-manage" data-dropdown-menu class="clay-card absolute start-0 top-full z-50 mt-2 hidden w-56 p-2">
                            <x-nav-link dropdownItem :href="route('courses.index')" :active="request()->routeIs('courses.*')">{{ __('courses.title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('groups.index')" :active="request()->routeIs('groups.*')">{{ __('groups.title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('enrollments.index')" :active="request()->routeIs('enrollments.*')">{{ __('enrollments.title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('teachers.index')" :active="request()->routeIs('teachers.*')">{{ __('teachers.title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('students.index')" :active="request()->routeIs('students.*')">{{ __('students.title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('parents.index')" :active="request()->routeIs('parents.*')">{{ __('guardians.title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('galleries.index')" :active="request()->routeIs('galleries.*')">{{ __('gallery.manage_title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('contact-messages.index')" :active="request()->routeIs('contact-messages.*')">{{ __('pages.inbox_title') }}</x-nav-link>
                        </div>
                    </div>
                    <div class="relative">
                        <button type="button" data-dropdown-toggle="menu-reports" aria-expanded="false" aria-haspopup="true"
                                class="flex cursor-pointer items-center gap-1 rounded-lg px-2.5 py-2 font-semibold text-slate-600 transition-colors hover:bg-muted hover:text-primary {{ request()->routeIs('reports.attendance', 'reports.progress', 'reports.courses', 'audit-logs.*') ? 'bg-muted font-extrabold text-primary' : '' }}">
                            {{ __('layout.reports') }}
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6" /></svg>
                        </button>
                        <div id="menu-reports" data-dropdown-menu class="clay-card absolute start-0 top-full z-50 mt-2 hidden w-60 p-2">
                            <x-nav-link dropdownItem :href="route('reports.attendance')" :active="request()->routeIs('reports.attendance')">{{ __('reports.attendance_title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('reports.progress')" :active="request()->routeIs('reports.progress')">{{ __('reports.progress_title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('reports.courses')" :active="request()->routeIs('reports.courses')">{{ __('reports.courses_title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')">{{ __('reports.audit_title') }}</x-nav-link>
                        </div>
                    </div>
                @endcan
                @can('record-payments')
                    <div class="relative">
                        <button type="button" data-dropdown-toggle="menu-finance" aria-expanded="false" aria-haspopup="true"
                                class="flex cursor-pointer items-center gap-1 rounded-lg px-2.5 py-2 font-semibold text-slate-600 transition-colors hover:bg-muted hover:text-primary {{ request()->routeIs('payments.*', 'reports.financial') ? 'bg-muted font-extrabold text-primary' : '' }}">
                            {{ __('layout.finance') }}
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6" /></svg>
                        </button>
                        <div id="menu-finance" data-dropdown-menu class="clay-card absolute start-0 top-full z-50 mt-2 hidden w-56 p-2">
                            <x-nav-link dropdownItem :href="route('payments.index')" :active="request()->routeIs('payments.*')">{{ __('payments.title') }}</x-nav-link>
                            <x-nav-link dropdownItem :href="route('reports.financial')" :active="request()->routeIs('reports.financial')">{{ __('reports.financial_title') }}</x-nav-link>
                        </div>
                    </div>
                @endcan
            @endauth
        </nav>
        <div class="flex items-center gap-2">
            <a href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="rounded-lg px-2.5 py-2 text-sm font-semibold text-slate-600 transition-colors hover:bg-muted hover:text-primary">
                {{ __('layout.switch_language') }}
            </a>
            @auth
                <a href="{{ route('notifications.index') }}" class="relative rounded-lg px-2.5 py-2 text-slate-600 transition-colors hover:bg-muted hover:text-primary" aria-label="{{ __('notifications.title') }}">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" /><path d="M13.7 21a2 2 0 0 1-3.4 0" /></svg>
                    @if (($unreadNotificationsCount ?? 0) > 0)
                        <span class="absolute -top-0.5 -end-0.5 rounded-full border-2 border-slate-900 bg-accent px-1 text-[10px] font-extrabold leading-4 text-white">{{ $unreadNotificationsCount }}</span>
                    @endif
                </a>
                <span class="hidden items-center gap-2 lg:flex">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-slate-900 bg-secondary font-display text-sm font-bold text-slate-900" aria-hidden="true">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                    <span class="max-w-28 truncate text-sm font-semibold text-slate-600">{{ auth()->user()->name }}</span>
                    <span class="clay-chip bg-slate-900 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-white">{{ auth()->user()->role->label() }}</span>
                </span>
                <form method="POST" action="{{ route('logout') }}" class="hidden lg:inline">
                    @csrf
                    <button type="submit" class="cursor-pointer rounded-lg px-2.5 py-2 text-sm font-semibold text-slate-600 transition-colors hover:bg-muted hover:text-primary">{{ __('layout.logout') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="clay-btn hidden cursor-pointer bg-secondary px-4 py-1.5 text-sm font-extrabold text-slate-900 lg:inline-block">{{ __('auth.login_button') }}</a>
            @endauth
            <button type="button" data-mobile-toggle="mobile-nav" aria-expanded="false" aria-label="{{ __('layout.menu') }}"
                    class="cursor-pointer rounded-lg border-2 border-slate-900 p-2 text-slate-700 lg:hidden">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16 M4 12h16 M4 17h16" /></svg>
            </button>
        </div>
    </div>
    <div id="mobile-nav" class="hidden border-t-2 border-slate-900 bg-white lg:hidden">
        <nav class="mx-auto max-w-6xl space-y-1 px-4 py-4 text-sm" aria-label="Mobile">
            <x-nav-link dropdownItem :href="route('home')" :active="request()->routeIs('home')">{{ __('layout.home') }}</x-nav-link>
            <x-nav-link dropdownItem :href="route('catalog')" :active="request()->routeIs('catalog*')">{{ __('pages.courses_nav') }}</x-nav-link>
            <x-nav-link dropdownItem :href="route('gallery')" :active="request()->routeIs('gallery')">{{ __('gallery.title') }}</x-nav-link>
            <x-nav-link dropdownItem :href="route('about')" :active="request()->routeIs('about')">{{ __('pages.about_nav') }}</x-nav-link>
            <x-nav-link dropdownItem :href="route('contact')" :active="request()->routeIs('contact')">{{ __('pages.contact_nav') }}</x-nav-link>
            @auth
                <x-nav-link dropdownItem :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('layout.dashboard') }}</x-nav-link>
                @if (auth()->user()->hasRole(\App\Enums\Role::SuperAdmin, \App\Enums\Role::Manager, \App\Enums\Role::Teacher))
                    <x-nav-link dropdownItem :href="route('sessions.index')" :active="request()->routeIs('sessions.*')">{{ __('sessions.title') }}</x-nav-link>
                @endif
                <x-nav-link dropdownItem :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">{{ __('notifications.title') }}</x-nav-link>
                @can('manage-center')
                    <p class="px-3 pb-1 pt-3 text-xs font-extrabold uppercase tracking-wide text-slate-400">{{ __('layout.quick_create') }}</p>
                    <div class="grid grid-cols-2 gap-1">
                        <x-nav-link dropdownItem :href="route('students.create')">{{ __('students.create_button') }}</x-nav-link>
                        <x-nav-link dropdownItem :href="route('parents.create')">{{ __('guardians.create_button') }}</x-nav-link>
                        <x-nav-link dropdownItem :href="route('courses.create')">{{ __('courses.create_button') }}</x-nav-link>
                        <x-nav-link dropdownItem :href="route('groups.create')">{{ __('groups.create_button') }}</x-nav-link>
                        <x-nav-link dropdownItem :href="route('teachers.create')">{{ __('teachers.create_button') }}</x-nav-link>
                        <x-nav-link dropdownItem :href="route('enrollments.create')">{{ __('enrollments.create_button') }}</x-nav-link>
                    </div>
                    <p class="px-3 pb-1 pt-3 text-xs font-extrabold uppercase tracking-wide text-slate-400">{{ __('layout.manage') }}</p>
                    <x-nav-link dropdownItem :href="route('courses.index')" :active="request()->routeIs('courses.*')">{{ __('courses.title') }}</x-nav-link>
                    <x-nav-link dropdownItem :href="route('groups.index')" :active="request()->routeIs('groups.*')">{{ __('groups.title') }}</x-nav-link>
                    <x-nav-link dropdownItem :href="route('enrollments.index')" :active="request()->routeIs('enrollments.*')">{{ __('enrollments.title') }}</x-nav-link>
                    <x-nav-link dropdownItem :href="route('teachers.index')" :active="request()->routeIs('teachers.*')">{{ __('teachers.title') }}</x-nav-link>
                    <x-nav-link dropdownItem :href="route('students.index')" :active="request()->routeIs('students.*')">{{ __('students.title') }}</x-nav-link>
                    <x-nav-link dropdownItem :href="route('parents.index')" :active="request()->routeIs('parents.*')">{{ __('guardians.title') }}</x-nav-link>
                    <x-nav-link dropdownItem :href="route('contact-messages.index')" :active="request()->routeIs('contact-messages.*')">{{ __('pages.inbox_title') }}</x-nav-link>
                    <x-nav-link dropdownItem :href="route('galleries.index')" :active="request()->routeIs('galleries.*')">{{ __('gallery.manage_title') }}</x-nav-link>
                    <p class="px-3 pb-1 pt-3 text-xs font-extrabold uppercase tracking-wide text-slate-400">{{ __('layout.reports') }}</p>
                    <x-nav-link dropdownItem :href="route('reports.attendance')" :active="request()->routeIs('reports.attendance')">{{ __('reports.attendance_title') }}</x-nav-link>
                    <x-nav-link dropdownItem :href="route('reports.progress')" :active="request()->routeIs('reports.progress')">{{ __('reports.progress_title') }}</x-nav-link>
                    <x-nav-link dropdownItem :href="route('reports.courses')" :active="request()->routeIs('reports.courses')">{{ __('reports.courses_title') }}</x-nav-link>
                    <x-nav-link dropdownItem :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')">{{ __('reports.audit_title') }}</x-nav-link>
                @endcan
                @can('record-payments')
                    <p class="px-3 pb-1 pt-3 text-xs font-extrabold uppercase tracking-wide text-slate-400">{{ __('layout.finance') }}</p>
                    <x-nav-link dropdownItem :href="route('payments.index')" :active="request()->routeIs('payments.*')">{{ __('payments.title') }}</x-nav-link>
                    <x-nav-link dropdownItem :href="route('reports.financial')" :active="request()->routeIs('reports.financial')">{{ __('reports.financial_title') }}</x-nav-link>
                @endcan
                <form method="POST" action="{{ route('logout') }}" class="pt-2">
                    @csrf
                    <button type="submit" class="w-full cursor-pointer rounded-lg bg-muted px-3 py-2 text-start font-semibold text-slate-600">{{ __('layout.logout') }} ({{ auth()->user()->name }})</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="clay-btn mt-2 block cursor-pointer bg-secondary px-3 py-2 text-center text-sm font-extrabold text-slate-900">{{ __('auth.login_button') }}</a>
            @endauth
        </nav>
    </div>
</header>
<main class="mx-auto w-full max-w-6xl grow px-4 py-8">
    <div id="flash-data" class="hidden" data-success="{{ session('status') }}" data-errors='@json($errors->all())' data-confirm-button="{{ __('layout.confirm_button') }}" data-cancel-button="{{ __('layout.cancel_button') }}"></div>
    @yield('content')
</main>
<footer class="border-t-[3px] border-slate-900 bg-white">
    <div class="mx-auto grid max-w-6xl grid-cols-1 gap-8 px-4 py-10 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <x-logo :tagline="true" markSize="h-10 w-10" textSize="text-xl" />
            <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ __('layout.footer_blurb') }}</p>
        </div>
        <nav aria-label="Explore">
            <h2 class="font-display font-bold text-slate-900">{{ __('layout.explore') }}</h2>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="{{ route('home') }}" class="font-semibold text-slate-600 hover:text-primary">{{ __('layout.home') }}</a></li>
                <li><a href="{{ route('catalog') }}" class="font-semibold text-slate-600 hover:text-primary">{{ __('pages.courses_nav') }}</a></li>
                <li><a href="{{ route('gallery') }}" class="font-semibold text-slate-600 hover:text-primary">{{ __('gallery.title') }}</a></li>
                <li><a href="{{ route('about') }}" class="font-semibold text-slate-600 hover:text-primary">{{ __('pages.about_nav') }}</a></li>
                <li><a href="{{ route('contact') }}" class="font-semibold text-slate-600 hover:text-primary">{{ __('pages.contact_nav') }}</a></li>
            </ul>
        </nav>
        <nav aria-label="Accounts">
            <h2 class="font-display font-bold text-slate-900">{{ __('layout.accounts') }}</h2>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="{{ route('login') }}" class="font-semibold text-slate-600 hover:text-primary">{{ __('auth.login_button') }}</a></li>
                @auth
                    <li><a href="{{ route('dashboard') }}" class="font-semibold text-slate-600 hover:text-primary">{{ __('layout.dashboard') }}</a></li>
                    <li><a href="{{ route('notifications.index') }}" class="font-semibold text-slate-600 hover:text-primary">{{ __('notifications.title') }}</a></li>
                @endauth
            </ul>
        </nav>
        <div>
            <h2 class="font-display font-bold text-slate-900">{{ __('pages.info_title') }}</h2>
            <ul class="mt-3 space-y-2 text-sm text-slate-600">
                <li>{{ __('pages.contact_address') }}</li>
                <li dir="ltr" class="text-start">
                    <a href="tel:{{ config('services.bidaya.contact_phone') }}" class="font-semibold hover:text-primary">{{ config('services.bidaya.contact_phone') }}</a>
                </li>
                <li>
                    <a href="mailto:{{ config('services.bidaya.contact_email') }}" class="font-semibold hover:text-primary">{{ config('services.bidaya.contact_email') }}</a>
                </li>
                <li>{{ __('pages.contact_hours') }}</li>
            </ul>
        </div>
    </div>
    <div class="border-t-2 border-slate-900/10">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-2 px-4 py-4">
            <p class="text-xs text-slate-400">{{ __('layout.footer_rights', ['year' => date('Y')]) }}</p>
            <a href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="text-xs font-bold text-slate-500 hover:text-primary">
                {{ __('layout.switch_language') }}
            </a>
        </div>
    </div>
</footer>
</body>
</html>
