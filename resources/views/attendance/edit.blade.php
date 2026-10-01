@extends('layouts.app')

@section('title', __('attendance.title'))

@section('content')
<div class="mb-6">
    <a href="{{ route('sessions.show', $session) }}" class="text-sm text-indigo-700 hover:underline">← {{ $session->group->name }}</a>
    <h1 class="mt-1 text-xl font-bold">{{ __('attendance.title') }}</h1>
    <p class="mt-1 text-sm text-slate-500">
        {{ $session->date->toDateString() }} — {{ substr($session->start_time, 0, 5) }} – {{ substr($session->end_time, 0, 5) }} — {{ $session->status->label() }}
    </p>
</div>

<div class="overflow-hidden rounded-lg bg-white shadow">
    <form method="POST" action="{{ route('sessions.attendance.update', $session) }}">
        @csrf
        @method('PUT')

        <table class="w-full text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-2 text-start font-medium">{{ __('attendance.student') }}</th>
                    @foreach (\App\Enums\AttendanceStatus::cases() as $option)
                        <th class="px-4 py-2 text-center font-medium">{{ $option->label() }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($enrollments as $index => $enrollment)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2">
                            {{ $enrollment->student->name }}
                            <input type="hidden" name="records[{{ $index }}][enrollment_id]" value="{{ $enrollment->id }}">
                        </td>
                        @foreach (\App\Enums\AttendanceStatus::cases() as $option)
                            <td class="px-4 py-2 text-center">
                                <input type="radio" name="records[{{ $index }}][status]" value="{{ $option->value }}"
                                       @checked(old("records.{$index}.status", $existing->get($enrollment->id)?->value ?? \App\Enums\AttendanceStatus::Present->value) === $option->value)>
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-slate-500">{{ __('attendance.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @error('records')
            <p class="px-4 py-2 text-sm text-red-600">{{ $message }}</p>
        @enderror

        @if ($enrollments->isNotEmpty())
            <div class="border-t border-slate-100 p-4">
                <button type="submit" class="rounded bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">
                    {{ __('attendance.save_button') }}
                </button>
            </div>
        @endif
    </form>
</div>
@endsection
