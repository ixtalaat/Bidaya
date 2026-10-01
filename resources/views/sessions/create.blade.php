@extends('layouts.app')

@section('title', __('sessions.create_title'))

@section('content')
<div class="mx-auto max-w-xl clay-card p-6">
    <h1 class="mb-6 font-display text-xl font-bold">{{ __('sessions.create_title') }}</h1>

    @if (session('conflicts'))
        <div class="mb-4 rounded bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-medium">{{ __('sessions.conflicting_sessions') }}</p>
            <ul class="mt-1 list-disc ps-5">
                @foreach (session('conflicts') as $conflict)
                    <li>{{ $conflict['date'] }} — {{ $conflict['group']['name'] ?? '' }} ({{ substr($conflict['start_time'], 0, 5) }})</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('sessions.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="group_id" class="mb-1 block text-sm font-medium">{{ __('sessions.group') }}</label>
            <select id="group_id" name="group_id" required
                    class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                @foreach ($allGroups as $group)
                    <option value="{{ $group->id }}" @selected((string) old('group_id') === (string) $group->id)>
                        {{ $group->name }} ({{ $group->course->name }})
                    </option>
                @endforeach
            </select>
            @error('group_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="date" class="mb-1 block text-sm font-medium">{{ __('sessions.date') }}</label>
            <input id="date" type="date" name="date" value="{{ old('date') }}" required
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            @error('date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="start_time" class="mb-1 block text-sm font-medium">{{ __('sessions.start_time') }}</label>
                <input id="start_time" type="time" name="start_time" value="{{ old('start_time') }}" required
                       class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                @error('start_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="end_time" class="mb-1 block text-sm font-medium">{{ __('sessions.end_time') }}</label>
                <input id="end_time" type="time" name="end_time" value="{{ old('end_time') }}" required
                       class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                @error('end_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="classroom" class="mb-1 block text-sm font-medium">{{ __('sessions.classroom') }}</label>
            <input id="classroom" type="text" name="classroom" value="{{ old('classroom') }}"
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        </div>

        <div>
            <label for="teacher_notes" class="mb-1 block text-sm font-medium">{{ __('sessions.teacher_notes') }}</label>
            <textarea id="teacher_notes" name="teacher_notes" rows="3"
                      class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">{{ old('teacher_notes') }}</textarea>
        </div>

        <button type="submit" class="w-full rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('sessions.save_button') }}
        </button>
    </form>
</div>
@endsection
