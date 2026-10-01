@extends('layouts.app')

@section('title', __('assignments.show_title'))

@section('content')
<div class="mx-auto max-w-2xl clay-card p-6">
    <div class="mb-4 flex items-start justify-between gap-2">
        <div>
            <h1 class="font-display text-xl font-bold">{{ $assignment->title }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ __('assignments.due_date') }}: {{ $assignment->due_date->toDateString() }} —
                {{ __('assignments.max_score') }}: {{ number_format($assignment->max_score, 2) }} —
                {{ $assignment->status->label() }}
            </p>
        </div>
        @can('update', $assignment)
            <div class="flex shrink-0 gap-2">
                <a href="{{ route('groups.assignments.grades.edit', [$group, $assignment]) }}" class="clay-btn cursor-pointer bg-secondary px-3 py-1.5 text-sm font-extrabold text-slate-900">
                    {{ __('assignments.grade_button') }}
                </a>
                <a href="{{ route('groups.assignments.edit', [$group, $assignment]) }}" class="clay-btn cursor-pointer bg-white px-3 py-1.5 text-sm font-extrabold text-slate-900">
                    {{ __('students.edit_button') }}
                </a>
            </div>
        @endcan
    </div>

    @if ($assignment->description)
        <p class="mb-4 text-sm">{{ $assignment->description }}</p>
    @endif

    @if ($assignment->attachment_path)
        <p class="mb-4 text-sm">
            <a href="{{ Storage::url($assignment->attachment_path) }}" target="_blank" class="font-semibold text-primary hover:underline">
                {{ __('assignments.download_attachment') }} ({{ basename($assignment->attachment_path) }})
            </a>
        </p>
    @endif

    <h2 class="mb-2 font-bold">{{ __('assignments.submissions_count', ['count' => $assignment->submissions->count()]) }}</h2>
    <div class="overflow-hidden rounded-xl border-2 border-slate-900">
        <table class="w-full text-sm">
            <thead class="bg-background">
                <tr>
                    <th class="px-4 py-2 text-start font-medium">{{ __('assignments.student') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('submissions.status') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('assignments.score') }}</th>
                    <th class="px-4 py-2 text-start font-medium">{{ __('assignments.feedback') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assignment->submissions as $submission)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2">{{ $submission->enrollment->student->name }}</td>
                        <td class="px-4 py-2">{{ $submission->status->label() }}</td>
                        <td class="px-4 py-2">
                            @if ($submission->score !== null)
                                {{ number_format($submission->score, 2) }} / {{ number_format($assignment->max_score, 2) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $submission->feedback ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-500">{{ __('assignments.no_submissions') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($submitTargets !== [])
        <h2 class="mb-2 mt-6 font-bold">{{ __('submissions.submit_button') }}</h2>
        @foreach ($submitTargets as $target)
            <form method="POST" action="{{ $target['submission'] ? route('groups.assignments.submissions.update', [$group, $assignment, $target['submission']]) : route('groups.assignments.submissions.store', [$group, $assignment]) }}"
                  enctype="multipart/form-data" class="mb-4 rounded-xl border-2 border-slate-900 bg-muted p-4">
                @csrf
                @if ($target['submission'])
                    @method('PUT')
                @endif
                <input type="hidden" name="enrollment_id" value="{{ $target['enrollment']->id }}">
                <p class="mb-2 text-sm font-bold">{{ $target['enrollment']->student->name }}
                    @if ($target['submission'])
                        <span class="font-normal text-slate-500">({{ $target['submission']->status->label() }})</span>
                    @endif
                </p>
                @if ($target['submission']?->score !== null)
                    <p class="mb-2 text-xs text-slate-500">{{ __('submissions.resubmit_resets_grade') }}</p>
                @endif
                <textarea name="answer" rows="3" placeholder="{{ __('submissions.answer_placeholder') }}"
                          class="w-full rounded-xl border-2 border-slate-900 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none">{{ old('answer', $target['submission']?->answer) }}</textarea>
                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx"
                       class="mt-2 w-full rounded-xl border-2 border-slate-900 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none">
                <button type="submit" class="clay-btn mt-3 cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
                    {{ $target['submission'] ? __('submissions.resubmit_button') : __('submissions.submit_button') }}
                </button>
            </form>
        @endforeach
    @endif

    @can('delete', $assignment)
        <form method="POST" action="{{ route('groups.assignments.destroy', [$group, $assignment]) }}" class="mt-6" data-confirm="{{ __('assignments.delete_confirm') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="clay-btn cursor-pointer bg-red-600 px-4 py-2 text-sm font-extrabold text-white">
                {{ __('assignments.delete_button') }}
            </button>
        </form>
    @endcan
</div>
@endsection
