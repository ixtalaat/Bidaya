@extends('layouts.app')

@section('title', __('assignments.grade_title'))

@section('content')
<div class="mb-6">
    <a href="{{ route('groups.assignments.show', [$group, $assignment]) }}" class="text-sm font-semibold text-primary hover:underline">← {{ $assignment->title }}</a>
    <h1 class="mt-1 font-display text-xl font-bold">{{ __('assignments.grade_title') }}</h1>
    <p class="mt-1 text-sm text-slate-500">
        {{ __('assignments.max_score') }}: {{ number_format($assignment->max_score, 2) }}
    </p>
</div>

<div class="overflow-hidden clay-card">
    <form method="POST" action="{{ route('groups.assignments.grades.update', [$group, $assignment]) }}">
        @csrf
        @method('PUT')

        <table class="w-full text-sm">
            <thead class="bg-background">
                <tr>
                    <th class="px-4 py-2 text-start font-medium">{{ __('assignments.student') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('assignments.score') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('assignments.feedback') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($enrollments as $index => $enrollment)
                    @php($result = $existing->get($enrollment->id))
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2">
                            {{ $enrollment->student->name }}
                            <input type="hidden" name="records[{{ $index }}][enrollment_id]" value="{{ $enrollment->id }}">
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" name="records[{{ $index }}][score]" min="0" step="0.01"
                                   value="{{ old("records.{$index}.score", $result?->score) }}"
                                   class="w-28 rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                            @error("records.{$index}.score")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error("records.{$index}.enrollment_id")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </td>
                        <td class="px-4 py-2">
                            <input type="text" name="records[{{ $index }}][feedback]" value="{{ old("records.{$index}.feedback", $result?->feedback) }}"
                                   placeholder="{{ __('assignments.feedback_placeholder') }}"
                                   class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-slate-500">{{ __('attendance.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @error('records')
            <p class="px-4 py-2 text-sm text-red-600">{{ $message }}</p>
        @enderror

        @if ($enrollments->isNotEmpty())
            <div class="border-t border-slate-100 p-4">
                <button type="submit" class="clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
                    {{ __('assignments.save_grades_button') }}
                </button>
            </div>
        @endif
    </form>
</div>
@endsection
