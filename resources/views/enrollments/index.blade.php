@extends('layouts.app')

@section('title', __('enrollments.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="font-display text-xl font-bold">{{ __('enrollments.title') }}</h1>
    <a href="{{ route('enrollments.create') }}" class="rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
        {{ __('enrollments.create_button') }}
    </a>
</div>

<form method="GET" action="{{ route('enrollments.index') }}" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('enrollments.search_placeholder') }}"
           class="w-full max-w-xs rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
    <select name="group_id" class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        <option value="">{{ __('enrollments.all_groups') }}</option>
        @foreach ($allGroups as $group)
            <option value="{{ $group->id }}" @selected((string) request('group_id') === (string) $group->id)>
                {{ $group->name }} ({{ $group->course->name }})
            </option>
        @endforeach
    </select>
    <select name="status" class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        <option value="">{{ __('enrollments.all_statuses') }}</option>
        @foreach (\App\Enums\EnrollmentStatus::cases() as $option)
            <option value="{{ $option->value }}" @selected(request('status') === $option->value)>{{ $option->label() }}</option>
        @endforeach
    </select>
    <button type="submit" class="rounded clay-btn cursor-pointer bg-white px-4 py-2 text-sm font-extrabold text-slate-900">
        {{ __('enrollments.search_button') }}
    </button>
</form>

<div class="overflow-hidden clay-card">
    <table class="w-full text-sm">
        <thead class="bg-background">
            <tr>
                <th class="px-4 py-2 text-start font-medium">{{ __('enrollments.student') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('enrollments.group') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('enrollments.final_monthly_fee') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('enrollments.status') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('enrollments.enrollment_date') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($enrollments as $enrollment)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2">
                        <a href="{{ route('enrollments.show', $enrollment) }}" class="text-primary font-semibold hover:underline">{{ $enrollment->student->name }}</a>
                    </td>
                    <td class="px-4 py-2">{{ $enrollment->group->name }} ({{ $enrollment->group->course->name }})</td>
                    <td class="px-4 py-2">{{ number_format($enrollment->final_monthly_fee, 2) }}</td>
                    <td class="px-4 py-2">{{ $enrollment->status->label() }}</td>
                    <td class="px-4 py-2">{{ $enrollment->enrollment_date->toDateString() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-slate-500">{{ __('enrollments.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $enrollments->links() }}</div>
@endsection
