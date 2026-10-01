@extends('layouts.app')

@section('title', __('assignments.title'))

@section('content')
<div class="mb-6">
    <a href="{{ route('groups.show', $group) }}" class="text-sm font-semibold text-primary hover:underline">← {{ $group->name }}</a>
    <div class="mt-1 flex items-center justify-between">
        <h1 class="font-display text-xl font-bold">{{ __('assignments.title') }}</h1>
        @can('create', \App\Models\Assignment::class)
            <a href="{{ route('groups.assignments.create', $group) }}" class="clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
                {{ __('assignments.create_button') }}
            </a>
        @endcan
    </div>
</div>

<div class="overflow-hidden clay-card">
    <table class="w-full text-sm">
        <thead class="bg-background">
            <tr>
                <th class="px-4 py-2 text-start font-medium">{{ __('assignments.name_title') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('assignments.due_date') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('assignments.max_score') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('assignments.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assignments as $assignment)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2">
                        <a href="{{ route('groups.assignments.show', [$group, $assignment]) }}" class="font-semibold text-primary hover:underline">{{ $assignment->title }}</a>
                    </td>
                    <td class="px-4 py-2">{{ $assignment->due_date->toDateString() }}</td>
                    <td class="px-4 py-2">{{ number_format($assignment->max_score, 2) }}</td>
                    <td class="px-4 py-2">{{ $assignment->status->label() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-slate-500">{{ __('assignments.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
