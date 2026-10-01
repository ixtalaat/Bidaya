@extends('layouts.app')

@section('title', __('courses.show_title'))

@section('content')
<div class="mx-auto max-w-2xl clay-card p-6">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h1 class="font-display text-xl font-bold">{{ $course->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $course->category ?? '—' }} — {{ $course->status->label() }} — {{ number_format($course->monthly_fee, 2) }}
            </p>
        </div>
        <a href="{{ route('courses.edit', $course) }}" class="rounded clay-btn cursor-pointer bg-white px-3 py-1.5 text-sm font-extrabold text-slate-900">
            {{ __('students.edit_button') }}
        </a>
    </div>

    @if ($course->description)
        <p class="text-sm">{{ $course->description }}</p>
    @endif

    <dl class="mt-4 grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
        <div>
            <dt class="font-medium text-slate-500">{{ __('courses.level') }}</dt>
            <dd>{{ $course->level ?? '—' }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('courses.min_age') }} / {{ __('courses.max_age') }}</dt>
            <dd>
                @if ($course->min_age !== null && $course->max_age !== null)
                    {{ __('courses.age_range', ['min' => $course->min_age, 'max' => $course->max_age]) }}
                @else
                    —
                @endif
            </dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('courses.duration_weeks') }}</dt>
            <dd>{{ $course->duration_weeks ?? '—' }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('courses.sessions_count') }}</dt>
            <dd>{{ $course->sessions_count ?? '—' }}</dd>
        </div>
    </dl>

    <h2 class="mb-2 mt-6 font-bold">{{ __('courses.groups') }}</h2>
    <ul class="space-y-1 text-sm">
        @forelse ($course->groups as $group)
            <li>
                <a href="{{ route('groups.show', $group) }}" class="text-primary font-semibold hover:underline">{{ $group->name }}</a>
                <span class="text-slate-500">({{ $group->status->label() }})</span>
            </li>
        @empty
            <li class="text-slate-500">—</li>
        @endforelse
    </ul>

    <form method="POST" action="{{ route('courses.destroy', $course) }}" class="mt-6"
          data-confirm="{{ __('courses.delete_confirm') }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded clay-btn cursor-pointer bg-red-600 px-4 py-2 text-sm font-extrabold text-white">
            {{ __('courses.delete_button') }}
        </button>
    </form>
</div>
@endsection
