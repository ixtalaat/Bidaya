@extends('layouts.app')

@section('title', __('sessions.edit_title'))

@section('content')
<div class="mx-auto max-w-xl rounded-lg bg-white p-6 shadow">
    <h1 class="mb-2 text-xl font-bold">{{ __('sessions.edit_title') }}</h1>
    <p class="mb-6 text-sm text-slate-500">{{ $session->group->name }} — {{ $session->group->course->name }}</p>

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

    <form method="POST" action="{{ route('sessions.update', $session) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="date" class="mb-1 block text-sm font-medium">{{ __('sessions.date') }}</label>
            <input id="date" type="date" name="date" value="{{ old('date', $session->date->format('Y-m-d')) }}" required
                   class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            @error('date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="start_time" class="mb-1 block text-sm font-medium">{{ __('sessions.start_time') }}</label>
                <input id="start_time" type="time" name="start_time" value="{{ old('start_time', substr($session->start_time, 0, 5)) }}" required
                       class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                @error('start_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="end_time" class="mb-1 block text-sm font-medium">{{ __('sessions.end_time') }}</label>
                <input id="end_time" type="time" name="end_time" value="{{ old('end_time', substr($session->end_time, 0, 5)) }}" required
                       class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                @error('end_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="classroom" class="mb-1 block text-sm font-medium">{{ __('sessions.classroom') }}</label>
            <input id="classroom" type="text" name="classroom" value="{{ old('classroom', $session->classroom) }}"
                   class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        </div>

        <div>
            <label for="teacher_notes" class="mb-1 block text-sm font-medium">{{ __('sessions.teacher_notes') }}</label>
            <textarea id="teacher_notes" name="teacher_notes" rows="3"
                      class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">{{ old('teacher_notes', $session->teacher_notes) }}</textarea>
        </div>

        <button type="submit" class="w-full rounded bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">
            {{ __('sessions.update_button') }}
        </button>
    </form>
</div>
@endsection
