@extends('layouts.app')

@section('title', __('assessments.title'))

@section('content')
<div class="mb-6">
    <a href="{{ route('groups.show', $group) }}" class="text-sm text-indigo-700 hover:underline">← {{ $group->name }}</a>
    <div class="mt-1 flex items-center justify-between">
        <h1 class="text-xl font-bold">{{ __('assessments.title') }}</h1>
        @can('create', \App\Models\Assessment::class)
            <a href="{{ route('groups.assessments.create', $group) }}" class="rounded bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">
                {{ __('assessments.create_button') }}
            </a>
        @endcan
    </div>
</div>

<div class="overflow-hidden rounded-lg bg-white shadow">
    <table class="w-full text-sm">
        <thead class="bg-slate-100">
            <tr>
                <th class="px-4 py-2 text-start font-medium">{{ __('assessments.name') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('assessments.type') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('assessments.max_score') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('assessments.assessment_date') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($group->assessments as $assessment)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2">
                        <a href="{{ route('groups.assessments.show', [$group, $assessment]) }}" class="text-indigo-700 hover:underline">{{ $assessment->name }}</a>
                    </td>
                    <td class="px-4 py-2">{{ $assessment->type->label() }}</td>
                    <td class="px-4 py-2">{{ number_format($assessment->max_score, 2) }}</td>
                    <td class="px-4 py-2">{{ $assessment->assessment_date->toDateString() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-slate-500">{{ __('assessments.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
