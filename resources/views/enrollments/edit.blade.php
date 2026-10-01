@extends('layouts.app')

@section('title', __('enrollments.edit_title'))

@section('content')
<div class="mx-auto max-w-xl clay-card p-6">
    <h1 class="mb-2 font-display text-xl font-bold">{{ __('enrollments.edit_title') }}</h1>
    <p class="mb-6 text-sm text-slate-500">{{ $enrollment->student->name }} — {{ $enrollment->group->name }}</p>

    <form method="POST" action="{{ route('enrollments.update', $enrollment) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="status" class="mb-1 block text-sm font-medium">{{ __('enrollments.status') }}</label>
            <select id="status" name="status" required
                    class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                @foreach (\App\Enums\EnrollmentStatus::cases() as $option)
                    <option value="{{ $option->value }}" @selected(old('status', $enrollment->status->value) === $option->value)>
                        {{ $option->label() }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="end_date" class="mb-1 block text-sm font-medium">{{ __('enrollments.end_date') }}</label>
            <input id="end_date" type="date" name="end_date" value="{{ old('end_date', $enrollment->end_date?->format('Y-m-d')) }}"
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        </div>

        <button type="submit" class="w-full rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('enrollments.update_button') }}
        </button>
    </form>
</div>
@endsection
