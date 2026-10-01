@extends('layouts.app')

@section('title', __('assessments.grade_title'))

@section('content')
<div class="mb-6">
    <a href="{{ route('groups.assessments.show', [$group, $assessment]) }}" class="text-sm text-primary font-semibold hover:underline">← {{ $assessment->name }}</a>
    <h1 class="mt-1 font-display text-xl font-bold">{{ __('assessments.grade_title') }}</h1>
    <p class="mt-1 text-sm text-slate-500">
        {{ __('assessments.max_score') }}: {{ number_format($assessment->max_score, 2) }}
    </p>
</div>

<div class="overflow-hidden clay-card">
    <form method="POST" action="{{ route('groups.assessments.results.update', [$group, $assessment]) }}">
        @csrf
        @method('PUT')

        <table class="w-full text-sm">
            <thead class="bg-background">
                <tr>
                    <th class="px-4 py-2 text-start font-medium">{{ __('assessments.student') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('assessments.score') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('assessments.feedback') }}</th>
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
                                   class="w-28 rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                            @error("records.{$index}.score")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error("records.{$index}.enrollment_id")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </td>
                        <td class="px-4 py-2">
                            <input type="text" name="records[{{ $index }}][feedback]" value="{{ old("records.{$index}.feedback", $result?->feedback) }}"
                                   placeholder="{{ __('assessments.feedback_placeholder') }}"
                                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
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
                <button type="submit" class="rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
                    {{ __('assessments.save_grades_button') }}
                </button>
            </div>
        @endif
    </form>
</div>
@endsection
