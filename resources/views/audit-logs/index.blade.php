@extends('layouts.app')

@section('title', __('reports.audit_title'))

@section('content')
<h1 class="mb-6 font-display text-xl font-bold">{{ __('reports.audit_title') }}</h1>

<form method="GET" action="{{ route('audit-logs.index') }}" class="mb-4 flex flex-wrap items-end gap-2">
    <div>
        <label for="action" class="mb-1 block text-sm font-medium">{{ __('reports.action') }}</label>
        <select id="action" name="action" class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            <option value="">{{ __('reports.all_actions') }}</option>
            @foreach ($actions as $action)
                <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="date_from" class="mb-1 block text-sm font-medium">{{ __('reports.from') }}</label>
        <input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}"
               class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
    </div>
    <div>
        <label for="date_to" class="mb-1 block text-sm font-medium">{{ __('reports.to') }}</label>
        <input id="date_to" type="date" name="date_to" value="{{ request('date_to') }}"
               class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
    </div>
    <button type="submit" class="rounded clay-btn cursor-pointer bg-white px-4 py-2 text-sm font-extrabold text-slate-900">
        {{ __('reports.filter_button') }}
    </button>
</form>

<div class="overflow-hidden clay-card">
    <table class="w-full text-sm">
        <thead class="bg-background">
            <tr>
                <th class="px-4 py-2 text-start font-medium">{{ __('reports.date') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('reports.user') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('reports.action') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('reports.entity') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2">{{ $log->created_at->toDateTimeString() }}</td>
                    <td class="px-4 py-2">{{ $log->user?->name ?? '—' }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('audit-logs.show', $log) }}" class="text-primary font-semibold hover:underline">{{ $log->action }}</a>
                    </td>
                    <td class="px-4 py-2">{{ class_basename($log->entity_type) }} #{{ $log->entity_id }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-slate-500">{{ __('reports.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $logs->links() }}</div>
@endsection
