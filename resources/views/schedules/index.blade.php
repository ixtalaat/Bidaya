@extends('layouts.app')

@section('title', __('schedules.title'))

@section('content')
<div class="mb-6">
    <a href="{{ route('groups.show', $group) }}" class="text-sm text-primary font-semibold hover:underline">← {{ $group->name }}</a>
    <h1 class="mt-1 font-display text-xl font-bold">{{ __('schedules.title') }}</h1>
</div>

<div class="mb-6 clay-card p-6">
    <form method="POST" action="{{ route('groups.schedules.store', $group) }}" class="flex flex-wrap items-end gap-3">
        @csrf
        <div>
            <label for="day_of_week" class="mb-1 block text-sm font-medium">{{ __('schedules.day') }}</label>
            <select id="day_of_week" name="day_of_week" required
                    class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                @foreach (\App\Enums\DayOfWeek::cases() as $day)
                    <option value="{{ $day->value }}">{{ $day->label() }}</option>
                @endforeach
            </select>
            @error('day_of_week')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="start_time" class="mb-1 block text-sm font-medium">{{ __('schedules.start_time') }}</label>
            <input id="start_time" type="time" name="start_time" required
                   class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        </div>
        <div>
            <label for="end_time" class="mb-1 block text-sm font-medium">{{ __('schedules.end_time') }}</label>
            <input id="end_time" type="time" name="end_time" required
                   class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            @error('end_time')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="classroom" class="mb-1 block text-sm font-medium">{{ __('schedules.classroom') }}</label>
            <input id="classroom" type="text" name="classroom" placeholder="{{ $group->classroom }}"
                   class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        </div>
        <button type="submit" class="rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('schedules.add_button') }}
        </button>
    </form>
</div>

<div class="overflow-hidden clay-card">
    <table class="w-full text-sm">
        <thead class="bg-background">
            <tr>
                <th class="px-4 py-2 text-start font-medium">{{ __('schedules.day') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('schedules.start_time') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('schedules.end_time') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('schedules.classroom') }}</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($group->schedules as $schedule)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2">{{ $schedule->day_of_week->label() }}</td>
                    <td class="px-4 py-2">{{ substr($schedule->start_time, 0, 5) }}</td>
                    <td class="px-4 py-2">{{ substr($schedule->end_time, 0, 5) }}</td>
                    <td class="px-4 py-2">{{ $schedule->classroom ?? $group->classroom ?? '—' }}</td>
                    <td class="px-4 py-2 text-end">
                        <a href="{{ route('groups.schedules.edit', [$group, $schedule]) }}" class="font-semibold text-slate-600 hover:text-primary">{{ __('students.edit_button') }}</a>
                        <form method="POST" action="{{ route('groups.schedules.destroy', [$group, $schedule]) }}" class="inline ms-3"
                              data-confirm="{{ __('schedules.delete_confirm') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-700 hover:underline">{{ __('schedules.delete_button') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-slate-500">{{ __('schedules.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    <a href="{{ route('sessions.generate', $group) }}" class="rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
        {{ __('schedules.generate_button') }}
    </a>
</div>
@endsection
