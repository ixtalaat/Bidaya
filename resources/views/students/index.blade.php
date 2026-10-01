@extends('layouts.app')

@section('title', __('students.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="font-display text-xl font-bold">{{ __('students.title') }}</h1>
    <a href="{{ route('students.create') }}" class="rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
        {{ __('students.create_button') }}
    </a>
</div>

<form method="GET" action="{{ route('students.index') }}" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('students.search_placeholder') }}"
           class="w-full max-w-xs rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
    <select name="status" class="rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        <option value="">{{ __('students.all_statuses') }}</option>
        @foreach (\App\Enums\StudentStatus::cases() as $option)
            <option value="{{ $option->value }}" @selected(request('status') === $option->value)>{{ $option->label() }}</option>
        @endforeach
    </select>
    <button type="submit" class="rounded clay-btn cursor-pointer bg-white px-4 py-2 text-sm font-extrabold text-slate-900">
        {{ __('students.search_button') }}
    </button>
</form>

<div class="overflow-hidden clay-card">
    <table class="w-full text-sm">
        <thead class="bg-background">
            <tr>
                <th class="px-4 py-2 text-start font-medium">{{ __('students.name') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('students.age') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('students.status') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('students.guardians') }}</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2">
                        <a href="{{ route('students.show', $student) }}" class="text-primary font-semibold hover:underline">{{ $student->name }}</a>
                    </td>
                    <td class="px-4 py-2">{{ $student->age ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $student->status->label() }}</td>
                    <td class="px-4 py-2">{{ $student->guardians->pluck('name')->join(', ') ?: '—' }}</td>
                    <td class="px-4 py-2 text-end">
                        <a href="{{ route('students.edit', $student) }}" class="font-semibold text-slate-600 hover:text-primary">{{ __('students.edit_button') }}</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-slate-500">{{ __('students.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $students->links() }}</div>
@endsection
