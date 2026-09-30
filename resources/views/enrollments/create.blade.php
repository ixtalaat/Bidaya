@extends('layouts.app')

@section('title', __('enrollments.create_title'))

@section('content')
<div class="mx-auto max-w-xl rounded-lg bg-white p-6 shadow">
    <h1 class="mb-6 text-xl font-bold">{{ __('enrollments.create_title') }}</h1>

    <form method="POST" action="{{ route('enrollments.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="student_id" class="mb-1 block text-sm font-medium">{{ __('enrollments.student') }}</label>
            <select id="student_id" name="student_id" required
                    class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                <option value="">{{ __('enrollments.select_student') }}</option>
                @foreach ($allStudents as $student)
                    <option value="{{ $student->id }}" @selected((string) old('student_id') === (string) $student->id)>{{ $student->name }}</option>
                @endforeach
            </select>
            @error('student_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="group_id" class="mb-1 block text-sm font-medium">{{ __('enrollments.group') }}</label>
            <select id="group_id" name="group_id" required
                    class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                <option value="">{{ __('enrollments.select_group') }}</option>
                @foreach ($allGroups as $group)
                    <option value="{{ $group->id }}" @selected((string) old('group_id', $preselectedGroupId) === (string) $group->id)>
                        {{ $group->name }} ({{ $group->course->name }} — {{ number_format($group->course->monthly_fee, 2) }})
                    </option>
                @endforeach
            </select>
            @error('group_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="enrollment_date" class="mb-1 block text-sm font-medium">{{ __('enrollments.enrollment_date') }}</label>
                <input id="enrollment_date" type="date" name="enrollment_date" value="{{ old('enrollment_date', date('Y-m-d')) }}" required
                       class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            </div>
            <div>
                <label for="start_date" class="mb-1 block text-sm font-medium">{{ __('enrollments.start_date') }}</label>
                <input id="start_date" type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                       class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="end_date" class="mb-1 block text-sm font-medium">{{ __('enrollments.end_date') }}</label>
                <input id="end_date" type="date" name="end_date" value="{{ old('end_date') }}"
                       class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            </div>
            <div>
                <label for="discount_amount" class="mb-1 block text-sm font-medium">{{ __('enrollments.discount_amount') }}</label>
                <input id="discount_amount" type="number" name="discount_amount" value="{{ old('discount_amount', 0) }}" min="0" step="0.01" required
                       class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                @error('discount_amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" class="w-full rounded bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">
            {{ __('enrollments.save_button') }}
        </button>
    </form>
</div>
@endsection
