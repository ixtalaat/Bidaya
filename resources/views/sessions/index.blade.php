@extends('layouts.app')

@section('title', __('sessions.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="font-display text-xl font-bold">{{ __('sessions.title') }}</h1>
    @can('create', \App\Models\Session::class)
        <a href="{{ route('sessions.create') }}" class="rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('sessions.create_button') }}
        </a>
    @endcan
</div>

<form method="GET" action="{{ route('sessions.index') }}" class="mb-4 flex flex-wrap gap-2">
    <select name="group_id" class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        <option value="">{{ __('sessions.all_groups') }}</option>
        @foreach ($allGroups as $group)
            <option value="{{ $group->id }}" @selected((string) request('group_id') === (string) $group->id)>{{ $group->name }}</option>
        @endforeach
    </select>
    <input type="date" name="date" value="{{ request('date') }}"
           class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
    <select name="status" class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        <option value="">{{ __('sessions.all_statuses') }}</option>
        @foreach (\App\Enums\SessionStatus::cases() as $option)
            <option value="{{ $option->value }}" @selected(request('status') === $option->value)>{{ $option->label() }}</option>
        @endforeach
    </select>
    <button type="submit" class="rounded clay-btn cursor-pointer bg-white px-4 py-2 text-sm font-extrabold text-slate-900">
        {{ __('sessions.filter_button') }}
    </button>
</form>

@if (session('skipped'))
    <div class="mb-4 rounded bg-amber-50 px-4 py-3 text-sm text-amber-800">
        <p class="font-medium">{{ __('sessions.skipped_title') }}</p>
        <ul class="mt-1 list-disc ps-5">
            @foreach (session('skipped') as $skipped)
                <li>{{ $skipped['date'] }} ({{ $skipped['time'] }}) — {{ $skipped['reason'] }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="overflow-hidden clay-card">
    <table class="w-full text-sm">
        <thead class="bg-background">
            <tr>
                <th class="px-4 py-2 text-start font-medium">{{ __('sessions.date') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('sessions.group') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('sessions.start_time') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('sessions.classroom') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('sessions.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sessions as $session)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2">{{ $session->date->toDateString() }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('sessions.show', $session) }}" class="text-primary font-semibold hover:underline">{{ $session->group->name }}</a>
                    </td>
                    <td class="px-4 py-2">{{ substr($session->start_time, 0, 5) }} – {{ substr($session->end_time, 0, 5) }}</td>
                    <td class="px-4 py-2">{{ $session->classroom ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $session->status->label() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-slate-500">{{ __('sessions.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $sessions->links() }}</div>
@endsection
