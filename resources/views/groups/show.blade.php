@extends('layouts.app')

@section('title', __('groups.show_title'))

@section('content')
<div class="mx-auto max-w-2xl rounded-lg bg-white p-6 shadow">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h1 class="text-xl font-bold">{{ $group->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $group->course->name }} — {{ $group->teacher->user->name }} — {{ $group->status->label() }}
            </p>
            <p class="mt-1 text-sm text-slate-500">
                {{ __('groups.seats', ['enrolled' => $group->enrollments->filter(fn ($enrollment) => $enrollment->isActive())->count(), 'capacity' => $group->capacity]) }}
            </p>
        </div>
        <div class="flex shrink-0 gap-2">
            <a href="{{ route('groups.assessments.index', $group) }}" class="rounded bg-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-800 hover:bg-slate-300">
                {{ __('assessments.title') }}
            </a>
            <a href="{{ route('groups.schedules.index', $group) }}" class="rounded bg-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-800 hover:bg-slate-300">
                {{ __('schedules.title') }}
            </a>
            <a href="{{ route('enrollments.create', ['group_id' => $group->id]) }}" class="rounded bg-indigo-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-800">
                {{ __('groups.enroll_button') }}
            </a>
            <a href="{{ route('groups.edit', $group) }}" class="rounded bg-slate-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-slate-800">
                {{ __('students.edit_button') }}
            </a>
        </div>
    </div>

    <dl class="grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
        <div>
            <dt class="font-medium text-slate-500">{{ __('groups.classroom') }}</dt>
            <dd>{{ $group->classroom ?? '—' }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('groups.start_date') }} / {{ __('groups.end_date') }}</dt>
            <dd>{{ $group->start_date->toDateString() }} / {{ $group->end_date?->toDateString() ?? '—' }}</dd>
        </div>
    </dl>

    <h2 class="mb-2 mt-6 font-bold">{{ __('groups.enrollments') }}</h2>
    <ul class="space-y-1 text-sm">
        @forelse ($group->enrollments as $enrollment)
            <li>
                <a href="{{ route('enrollments.show', $enrollment) }}" class="text-indigo-700 hover:underline">{{ $enrollment->student->name }}</a>
                <span class="text-slate-500">({{ $enrollment->status->label() }})</span>
            </li>
        @empty
            <li class="text-slate-500">—</li>
        @endforelse
    </ul>

    <form method="POST" action="{{ route('groups.destroy', $group) }}" class="mt-6"
          onsubmit="return confirm({{ json_encode(__('groups.delete_confirm')) }})">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">
            {{ __('groups.delete_button') }}
        </button>
    </form>
</div>
@endsection
