@extends('layouts.app')

@section('title', __('assessments.show_title'))

@section('content')
<div class="mx-auto max-w-2xl rounded-lg bg-white p-6 shadow">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h1 class="text-xl font-bold">{{ $assessment->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $assessment->type->label() }} —
                {{ __('assessments.max_score') }}: {{ number_format($assessment->max_score, 2) }} —
                {{ $assessment->assessment_date->toDateString() }}
            </p>
            <p class="mt-1 text-sm text-slate-500">
                {{ __('assessments.average') }}: {{ $assessment->averageScore() ?? '—' }} —
                {{ __('assessments.results_count', ['count' => $assessment->results->count()]) }}
            </p>
        </div>
        <div class="flex shrink-0 gap-2">
            @can('update', $assessment)
                <a href="{{ route('groups.assessments.results.edit', [$group, $assessment]) }}" class="rounded bg-indigo-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-800">
                    {{ __('assessments.grade_button') }}
                </a>
                <a href="{{ route('groups.assessments.edit', [$group, $assessment]) }}" class="rounded bg-slate-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-slate-800">
                    {{ __('students.edit_button') }}
                </a>
            @endcan
        </div>
    </div>

    @if ($assessment->description)
        <p class="mb-4 text-sm">{{ $assessment->description }}</p>
    @endif

    <div class="overflow-hidden rounded border border-slate-200">
        <table class="w-full text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-2 text-start font-medium">{{ __('assessments.student') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('assessments.score') }}</th>
                    <th class="px-4 py-2 text-start font-medium">%</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('assessments.feedback') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assessment->results as $result)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2">{{ $result->enrollment->student->name }}</td>
                        <td class="px-4 py-2">{{ number_format($result->score, 2) }} / {{ number_format($assessment->max_score, 2) }}</td>
                        <td class="px-4 py-2">{{ $result->percentage }}%</td>
                        <td class="px-4 py-2">{{ $result->feedback ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-500">{{ __('assessments.no_results') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @can('delete', $assessment)
        <form method="POST" action="{{ route('groups.assessments.destroy', [$group, $assessment]) }}" class="mt-6"
              onsubmit="return confirm({{ json_encode(__('assessments.delete_confirm')) }})">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">
                {{ __('assessments.delete_button') }}
            </button>
        </form>
    @endcan
</div>
@endsection
