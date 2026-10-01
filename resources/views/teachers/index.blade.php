@extends('layouts.app')

@section('title', __('teachers.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="font-display text-xl font-bold">{{ __('teachers.title') }}</h1>
    <a href="{{ route('teachers.create') }}" class="rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
        {{ __('teachers.create_button') }}
    </a>
</div>

<form method="GET" action="{{ route('teachers.index') }}" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('teachers.search_placeholder') }}"
           class="w-full max-w-xs rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
    <select name="status" class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        <option value="">{{ __('teachers.all_statuses') }}</option>
        @foreach (\App\Enums\TeacherStatus::cases() as $option)
            <option value="{{ $option->value }}" @selected(request('status') === $option->value)>{{ $option->label() }}</option>
        @endforeach
    </select>
    <button type="submit" class="rounded clay-btn cursor-pointer bg-white px-4 py-2 text-sm font-extrabold text-slate-900">
        {{ __('teachers.search_button') }}
    </button>
</form>

<div class="overflow-hidden clay-card">
    <table class="w-full text-sm">
        <thead class="bg-background">
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
                        <a href="{{ route('teachers.show', $teacher) }}" class="text-primary font-semibold hover:underline">{{ $teacher->user->name }}</a>
                    </td>
                    <td class="px-4 py-2">{{ $teacher->user->email }}</td>
                    <td class="px-4 py-2">{{ $teacher->specialization ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $teacher->status->label() }}</td>
                    <td class="px-4 py-2 text-end">
                        <a href="{{ route('teachers.edit', $teacher) }}" class="font-semibold text-slate-600 hover:text-primary">{{ __('students.edit_button') }}</a>
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
