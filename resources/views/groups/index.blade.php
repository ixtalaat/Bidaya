@extends('layouts.app')

@section('title', __('groups.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-xl font-bold">{{ __('groups.title') }}</h1>
    <a href="{{ route('groups.create') }}" class="rounded bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">
        {{ __('groups.create_button') }}
    </a>
</div>

<form method="GET" action="{{ route('groups.index') }}" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('groups.search_placeholder') }}"
           class="w-full max-w-xs rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
    <select name="course_id" class="rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        <option value="">{{ __('groups.all_courses') }}</option>
        @foreach ($allCourses as $course)
            <option value="{{ $course->id }}" @selected((string) request('course_id') === (string) $course->id)>{{ $course->name }}</option>
        @endforeach
    </select>
    <select name="status" class="rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        <option value="">{{ __('groups.all_statuses') }}</option>
        @foreach (\App\Enums\GroupStatus::cases() as $option)
            <option value="{{ $option->value }}" @selected(request('status') === $option->value)>{{ $option->label() }}</option>
        @endforeach
    </select>
    <button type="submit" class="rounded bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
        {{ __('groups.search_button') }}
    </button>
</form>

<div class="overflow-hidden rounded-lg bg-white shadow">
    <table class="w-full text-sm">
        <thead class="bg-slate-100">
            <tr>
                <th class="px-4 py-2 text-start font-medium">{{ __('groups.name') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('groups.course') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('groups.teacher') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('groups.capacity') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('groups.status') }}</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($groups as $group)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2">
                        <a href="{{ route('groups.show', $group) }}" class="text-indigo-700 hover:underline">{{ $group->name }}</a>
                    </td>
                    <td class="px-4 py-2">{{ $group->course->name }}</td>
                    <td class="px-4 py-2">{{ $group->teacher->user->name }}</td>
                    <td class="px-4 py-2">{{ $group->active_enrollments_count }} / {{ $group->capacity }}</td>
                    <td class="px-4 py-2">{{ $group->status->label() }}</td>
                    <td class="px-4 py-2 text-end">
                        <a href="{{ route('groups.edit', $group) }}" class="text-slate-600 hover:text-indigo-700">{{ __('students.edit_button') }}</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-slate-500">{{ __('groups.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $groups->links() }}</div>
@endsection
