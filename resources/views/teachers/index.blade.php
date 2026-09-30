@extends('layouts.app')

@section('title', __('teachers.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-xl font-bold">{{ __('teachers.title') }}</h1>
    <a href="{{ route('teachers.create') }}" class="rounded bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">
        {{ __('teachers.create_button') }}
    </a>
</div>

<form method="GET" action="{{ route('teachers.index') }}" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('teachers.search_placeholder') }}"
           class="w-full max-w-xs rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
    <select name="status" class="rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        <option value="">{{ __('teachers.all_statuses') }}</option>
        @foreach (\App\Enums\TeacherStatus::cases() as $option)
            <option value="{{ $option->value }}" @selected(request('status') === $option->value)>{{ $option->label() }}</option>
        @endforeach
    </select>
    <button type="submit" class="rounded bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
        {{ __('teachers.search_button') }}
    </button>
</form>

<div class="overflow-hidden rounded-lg bg-white shadow">
    <table class="w-full text-sm">
        <thead class="bg-slate-100">
            <tr>
                <th class="px-4 py-2 text-start font-medium">{{ __('teachers.name') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('teachers.email') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('teachers.specialization') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('teachers.status') }}</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($teachers as $teacher)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2">
                        <a href="{{ route('teachers.show', $teacher) }}" class="text-indigo-700 hover:underline">{{ $teacher->user->name }}</a>
                    </td>
                    <td class="px-4 py-2">{{ $teacher->user->email }}</td>
                    <td class="px-4 py-2">{{ $teacher->specialization ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $teacher->status->label() }}</td>
                    <td class="px-4 py-2 text-end">
                        <a href="{{ route('teachers.edit', $teacher) }}" class="text-slate-600 hover:text-indigo-700">{{ __('students.edit_button') }}</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-slate-500">{{ __('teachers.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $teachers->links() }}</div>
@endsection
