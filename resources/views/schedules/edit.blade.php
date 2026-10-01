@extends('layouts.app')

@section('title', __('schedules.title'))

@section('content')
<div class="mx-auto max-w-xl clay-card p-6">
    <h1 class="mb-6 font-display text-xl font-bold">{{ $schedule->day_of_week->label() }} — {{ substr($schedule->start_time, 0, 5) }}</h1>

    <form method="POST" action="{{ route('groups.schedules.update', [$group, $schedule]) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="day_of_week" class="mb-1 block text-sm font-medium">{{ __('schedules.day') }}</label>
                <select id="day_of_week" name="day_of_week" required
                        class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                    @foreach (\App\Enums\DayOfWeek::cases() as $day)
                        <option value="{{ $day->value }}" @selected(old('day_of_week', $schedule->day_of_week->value) == $day->value)>{{ $day->label() }}</option>
                    @endforeach
                </select>
                @error('day_of_week')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="classroom" class="mb-1 block text-sm font-medium">{{ __('schedules.classroom') }}</label>
                <input id="classroom" type="text" name="classroom" value="{{ old('classroom', $schedule->classroom) }}"
                       class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="start_time" class="mb-1 block text-sm font-medium">{{ __('schedules.start_time') }}</label>
                <input id="start_time" type="time" name="start_time" value="{{ old('start_time', substr($schedule->start_time, 0, 5)) }}" required
                       class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            </div>
            <div>
                <label for="end_time" class="mb-1 block text-sm font-medium">{{ __('schedules.end_time') }}</label>
                <input id="end_time" type="time" name="end_time" value="{{ old('end_time', substr($schedule->end_time, 0, 5)) }}" required
                       class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                @error('end_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" class="w-full rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('schedules.update_button') }}
        </button>
    </form>
</div>
@endsection
