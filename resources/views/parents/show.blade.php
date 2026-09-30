@extends('layouts.app')

@section('title', __('guardians.show_title'))

@section('content')
<div class="mx-auto max-w-2xl rounded-lg bg-white p-6 shadow">
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h1 class="text-xl font-bold">{{ $guardian->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $guardian->phone }}{{ $guardian->email ? ' — '.$guardian->email : '' }}</p>
        </div>
        <a href="{{ route('parents.edit', $guardian) }}" class="rounded bg-slate-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-slate-800">
            {{ __('students.edit_button') }}
        </a>
    </div>

    <dl class="grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
        <div>
            <dt class="font-medium text-slate-500">{{ __('guardians.address') }}</dt>
            <dd>{{ $guardian->address ?? '—' }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-500">{{ __('guardians.emergency_contact') }}</dt>
            <dd>{{ $guardian->emergency_contact ?? '—' }}</dd>
        </div>
    </dl>

    <h2 class="mb-2 mt-6 font-bold">{{ __('guardians.children') }}</h2>
    <ul class="space-y-1 text-sm">
        @forelse ($guardian->students as $student)
            <li>
                <a href="{{ route('students.show', $student) }}" class="text-indigo-700 hover:underline">{{ $student->name }}</a>
                <span class="text-slate-500">({{ $student->pivot->relationship->label() }})</span>
            </li>
        @empty
            <li class="text-slate-500">{{ __('students.empty') }}</li>
        @endforelse
    </ul>

    <form method="POST" action="{{ route('parents.destroy', $guardian) }}" class="mt-6"
          onsubmit="return confirm({{ json_encode(__('guardians.delete_confirm')) }})">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">
            {{ __('guardians.delete_button') }}
        </button>
    </form>
</div>
@endsection
