@extends('layouts.app')

@section('title', __('guardians.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="font-display text-xl font-bold">{{ __('guardians.title') }}</h1>
    <a href="{{ route('parents.create') }}" class="rounded clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
        {{ __('guardians.create_button') }}
    </a>
</div>

<form method="GET" action="{{ route('parents.index') }}" class="mb-4 flex gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('guardians.search_placeholder') }}"
           class="w-full max-w-sm rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
    <button type="submit" class="rounded clay-btn cursor-pointer bg-white px-4 py-2 text-sm font-extrabold text-slate-900">
        {{ __('guardians.search_button') }}
    </button>
</form>

<div class="overflow-hidden clay-card">
    <table class="w-full text-sm">
        <thead class="bg-slate-100 text-start">
            <tr>
                <th class="px-4 py-2 text-start font-medium">{{ __('guardians.name') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('guardians.phone') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('guardians.children_count') }}</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($guardians as $guardian)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2">
                        <a href="{{ route('parents.show', $guardian) }}" class="text-primary font-semibold hover:underline">{{ $guardian->name }}</a>
                    </td>
                    <td class="px-4 py-2">{{ $guardian->phone }}</td>
                    <td class="px-4 py-2">{{ $guardian->students_count }}</td>
                    <td class="px-4 py-2 text-end">
                        <a href="{{ route('parents.edit', $guardian) }}" class="font-semibold text-slate-600 hover:text-primary">{{ __('students.edit_button') }}</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-slate-500">{{ __('guardians.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $guardians->links() }}</div>
@endsection
