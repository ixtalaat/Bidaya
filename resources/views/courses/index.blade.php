@extends('layouts.app')

@section('title', __('courses.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="font-display text-xl font-bold">{{ __('courses.title') }}</h1>
    <a href="{{ route('courses.create') }}" class="rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
        {{ __('courses.create_button') }}
    </a>
</div>

<form method="GET" action="{{ route('courses.index') }}" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('courses.search_placeholder') }}"
           class="w-full max-w-xs rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
    <select name="status" class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        <option value="">{{ __('courses.all_statuses') }}</option>
        @foreach (\App\Enums\CourseStatus::cases() as $option)
            <option value="{{ $option->value }}" @selected(request('status') === $option->value)>{{ $option->label() }}</option>
        @endforeach
    </select>
    <button type="submit" class="rounded clay-btn cursor-pointer bg-white px-4 py-2 text-sm font-extrabold text-slate-900">
        {{ __('courses.search_button') }}
    </button>
</form>

<div class="overflow-hidden clay-card">
    <table class="w-full text-sm">
        <thead class="bg-background">
            <tr>
                <th class="px-4 py-2 text-start font-medium">{{ __('courses.name') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('courses.category') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('courses.monthly_fee') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('courses.status') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('courses.groups_count') }}</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($courses as $course)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2">
                        <a href="{{ route('courses.show', $course) }}" class="text-primary font-semibold hover:underline">{{ $course->name }}</a>
                    </td>
                    <td class="px-4 py-2">{{ $course->category ?? '—' }}</td>
                    <td class="px-4 py-2">{{ number_format($course->monthly_fee, 2) }}</td>
                    <td class="px-4 py-2">{{ $course->status->label() }}</td>
                    <td class="px-4 py-2">{{ $course->groups_count }}</td>
                    <td class="px-4 py-2 text-end">
                        <a href="{{ route('courses.edit', $course) }}" class="font-semibold text-slate-600 hover:text-primary">{{ __('students.edit_button') }}</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-slate-500">{{ __('courses.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $courses->links() }}</div>
@endsection
