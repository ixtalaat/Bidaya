@extends('layouts.app')

@section('title', __('teachers.show_title'))

@section('content')
<div class="mx-auto max-w-2xl rounded-lg bg-white p-6 shadow">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h1 class="text-xl font-bold">{{ $teacher->user->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $teacher->user->email }} — {{ $teacher->specialization ?? '—' }} — {{ $teacher->status->label() }}
            </p>
        </div>
        <a href="{{ route('teachers.edit', $teacher) }}" class="rounded bg-slate-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-slate-800">
            {{ __('students.edit_button') }}
        </a>
    </div>

    <dl class="grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
        <div>
            <dt class="font-medium text-slate-500">{{ __('teachers.qualification') }}</dt>
            <dd>{{ $teacher->qualification ?? '—' }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('teachers.hire_date') }}</dt>
            <dd>{{ $teacher->hire_date?->toDateString() ?? '—' }}</dd>
        </div>
    </dl>

    @if ($teacher->notes)
        <div class="mt-3 text-sm">
            <p class="font-medium text-slate-500">{{ __('teachers.notes') }}</p>
            <p>{{ $teacher->notes }}</p>
        </div>
    @endif

    <h2 class="mb-2 mt-6 font-bold">{{ __('teachers.groups') }}</h2>
    <ul class="space-y-1 text-sm">
        @forelse ($teacher->groups as $group)
            <li>
                <a href="{{ route('groups.show', $group) }}" class="text-indigo-700 hover:underline">{{ $group->name }}</a>
                <span class="text-slate-500">({{ $group->course->name }} — {{ $group->status->label() }})</span>
            </li>
        @empty
            <li class="text-slate-500">—</li>
        @endforelse
    </ul>

    <form method="POST" action="{{ route('teachers.destroy', $teacher) }}" class="mt-6"
          data-confirm="{{ __('teachers.delete_confirm') }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">
            {{ __('teachers.delete_button') }}
        </button>
    </form>
</div>
@endsection
