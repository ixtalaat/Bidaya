@extends('layouts.app')

@section('title', __('sessions.show_title'))

@section('content')
<div class="mx-auto max-w-2xl clay-card p-6">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h1 class="font-display text-xl font-bold">{{ $session->group->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $session->date->toDateString() }} —
                {{ substr($session->start_time, 0, 5) }} – {{ substr($session->end_time, 0, 5) }} —
                {{ $session->status->label() }}
            </p>
            <p class="mt-1 text-sm text-slate-500">
                {{ $session->teacher->user->name }}{{ $session->classroom ? ' — '.$session->classroom : '' }}
            </p>
        </div>
        @can('create', \App\Models\Session::class)
            <a href="{{ route('sessions.edit', $session) }}" class="shrink-0 rounded clay-btn cursor-pointer bg-white px-3 py-1.5 text-sm font-extrabold text-slate-900">
                {{ __('students.edit_button') }}
            </a>
        @endcan
    </div>

    @if ($session->teacher_notes)
        <div class="mb-4 rounded bg-slate-50 px-4 py-3 text-sm">
            <p class="font-medium text-slate-500">{{ __('sessions.teacher_notes') }}</p>
            <p class="mt-1">{{ $session->teacher_notes }}</p>
        </div>
    @endif

    @can('update', $session)
        <div class="mb-4 flex items-center justify-between rounded bg-slate-50 px-4 py-3 text-sm">
            <span class="font-medium text-slate-500">{{ __('attendance.summary_title') }}: {{ $session->attendances->count() }}</span>
            <a href="{{ route('sessions.attendance.edit', $session) }}" class="rounded clay-btn cursor-pointer bg-secondary px-3 py-1.5 text-sm font-extrabold text-slate-900">
                {{ __('attendance.title') }}
            </a>
        </div>
        <form method="POST" action="{{ route('sessions.status', $session) }}" class="space-y-3 border-t border-slate-100 pt-4">
            @csrf
            <div class="flex flex-wrap items-end gap-2">
                <div>
                    <label for="status" class="mb-1 block text-sm font-medium">{{ __('sessions.change_status') }}</label>
                    <select id="status" name="status" required
                            class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                        @foreach ($session->status->allowedTransitions() as $option)
                            <option value="{{ $option->value }}">{{ $option->label() }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
                    {{ __('sessions.apply_status') }}
                </button>
            </div>
            <div>
                <label for="teacher_notes" class="mb-1 block text-sm font-medium">{{ __('sessions.teacher_notes') }}</label>
                <textarea id="teacher_notes" name="teacher_notes" rows="3" placeholder="{{ __('sessions.notes_placeholder') }}"
                          class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">{{ old('teacher_notes', $session->teacher_notes) }}</textarea>
            </div>
        </form>
    @endcan

    @can('create', \App\Models\Session::class)
        <form method="POST" action="{{ route('sessions.destroy', $session) }}" class="mt-6"
              data-confirm="{{ __('sessions.delete_confirm') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded clay-btn cursor-pointer bg-red-600 px-4 py-2 text-sm font-extrabold text-white">
                {{ __('sessions.delete_button') }}
            </button>
        </form>
    @endcan
</div>
@endsection
