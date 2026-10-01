@extends('layouts.app')

@section('title', __('students.show_title'))

@section('content')
<div class="mx-auto max-w-2xl clay-card p-6">
    <div class="mb-4 flex items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            @if ($student->photo_path)
                <img src="{{ Storage::url($student->photo_path) }}" alt="{{ $student->name }}"
                     class="h-16 w-16 rounded-full object-cover">
            @endif
            <div>
                <h1 class="font-display text-xl font-bold">{{ $student->name }}</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $student->gender->label() }} —
                    {{ __('students.years_old', ['count' => $student->age ?? '—']) }} —
                    {{ $student->status->label() }}
                </p>
            </div>
        </div>
        <a href="{{ route('students.edit', $student) }}" class="shrink-0 rounded clay-btn cursor-pointer bg-white px-3 py-1.5 text-sm font-extrabold text-slate-900">
            {{ __('students.edit_button') }}
        </a>
    </div>

    <dl class="grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
        <div>
            <dt class="font-medium text-slate-500">{{ __('students.date_of_birth') }}</dt>
            <dd>{{ $student->date_of_birth->toDateString() }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('students.school') }}</dt>
            <dd>{{ $student->school ?? '—' }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('students.grade_level') }}</dt>
            <dd>{{ $student->grade_level ?? '—' }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('students.address') }}</dt>
            <dd>{{ $student->address ?? '—' }}</dd>
        </div>
    </dl>

    @if ($student->notes)
        <div class="mt-3 text-sm">
            <p class="font-medium text-slate-500">{{ __('students.notes') }}</p>
            <p>{{ $student->notes }}</p>
        </div>
    @endif

    <h2 class="mb-2 mt-6 font-bold">{{ __('students.guardians') }}</h2>
    <ul class="space-y-1 text-sm">
        @forelse ($student->guardians as $guardian)
            <li>
                <a href="{{ route('parents.show', $guardian) }}" class="text-primary font-semibold hover:underline">{{ $guardian->name }}</a>
                <span class="text-slate-500">({{ $guardian->pivot->relationship->label() }} — {{ $guardian->phone }})</span>
            </li>
        @empty
            <li class="text-slate-500">—</li>
        @endforelse
    </ul>

    <form method="POST" action="{{ route('students.destroy', $student) }}" class="mt-6"
          data-confirm="{{ __('students.delete_confirm') }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded clay-btn cursor-pointer bg-red-600 px-4 py-2 text-sm font-extrabold text-white">
            {{ __('students.delete_button') }}
        </button>
    </form>
</div>
@endsection
