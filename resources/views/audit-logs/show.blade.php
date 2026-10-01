@extends('layouts.app')

@section('title', __('reports.audit_title'))

@section('content')
<div class="mx-auto max-w-2xl rounded-lg bg-white p-6 shadow">
    <h1 class="text-xl font-bold">{{ $log->action }}</h1>
    <p class="mt-1 text-sm text-slate-500">
        {{ $log->created_at->toDateTimeString() }} — {{ $log->user?->name ?? '—' }} —
        {{ class_basename($log->entity_type) }} #{{ $log->entity_id }}
    </p>

    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <h2 class="mb-1 text-sm font-bold">{{ __('reports.before') }}</h2>
            <pre class="overflow-auto rounded bg-slate-50 p-3 text-xs">{{ json_encode($log->before ?? __('reports.no_changes'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        <div>
            <h2 class="mb-1 text-sm font-bold">{{ __('reports.after') }}</h2>
            <pre class="overflow-auto rounded bg-slate-50 p-3 text-xs">{{ json_encode($log->after ?? __('reports.no_changes'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
</div>
@endsection
