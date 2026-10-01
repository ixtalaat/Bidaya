@extends('layouts.app')

@section('title', __('sessions.generate_title'))

@section('content')
<div class="mb-6">
    <a href="{{ route('groups.show', $group) }}" class="text-sm text-primary font-semibold hover:underline">← {{ $group->name }}</a>
    <h1 class="mt-1 font-display text-xl font-bold">{{ __('sessions.generate_title') }}</h1>
    <p class="mt-1 text-sm text-slate-500">{{ __('sessions.generate_hint') }}</p>
</div>

<div class="mb-6 clay-card p-6">
    <form method="GET" action="{{ route('sessions.generate', $group) }}" class="flex flex-wrap items-end gap-3">
        <div>
            <label for="start_date" class="mb-1 block text-sm font-medium">{{ __('enrollments.start_date') }}</label>
            <input id="start_date" type="date" name="start_date" value="{{ $startDate ?: $group->start_date->format('Y-m-d') }}" required
                   class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        </div>
        <div>
            <label for="end_date" class="mb-1 block text-sm font-medium">{{ __('enrollments.end_date') }}</label>
            <input id="end_date" type="date" name="end_date"
                   value="{{ $endDate ?: $group->end_date?->format('Y-m-d') }}"
                   class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        </div>
        <button type="submit" class="rounded clay-btn cursor-pointer bg-white px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('sessions.preview_title') }}
        </button>
    </form>
    @error('start_date')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
    @error('end_date')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

@if ($startDate && $endDate)
    <div class="overflow-hidden clay-card">
        <table class="w-full text-sm">
            <thead class="bg-background">
                <tr>
                    <th class="px-4 py-2 text-start font-medium">{{ __('sessions.date') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('schedules.day') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('sessions.start_time') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('sessions.classroom') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($preview['rows'] as $row)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2">{{ $row['date'] }}</td>
                        <td class="px-4 py-2">{{ $row['day'] }}</td>
                        <td class="px-4 py-2">{{ $row['start_time'] }} – {{ $row['end_time'] }}</td>
                        <td class="px-4 py-2">{{ $row['classroom'] ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-500">{{ __('sessions.preview_empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($preview['skipped'] !== [])
        <div class="mt-4 rounded bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <p class="font-medium">{{ __('sessions.skipped_title') }}</p>
            <ul class="mt-1 list-disc ps-5">
                @foreach ($preview['skipped'] as $skipped)
                    <li>{{ $skipped['date'] }} ({{ $skipped['time'] }}) — {{ $skipped['reason'] }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($preview['rows'] !== [])
        <form method="POST" action="{{ route('sessions.generate.store', $group) }}" class="mt-4">
            @csrf
            <input type="hidden" name="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" value="{{ $endDate }}">
            <button type="submit" class="rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
                {{ __('sessions.confirm_generate') }} ({{ count($preview['rows']) }})
            </button>
        </form>
    @endif
@endif
@endsection
