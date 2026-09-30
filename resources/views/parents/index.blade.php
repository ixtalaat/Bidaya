@extends('layouts.app')

@section('title', __('guardians.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-xl font-bold">{{ __('guardians.title') }}</h1>
    <a href="{{ route('parents.create') }}" class="rounded bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">
        {{ __('guardians.create_button') }}
    </a>
</div>

<form method="GET" action="{{ route('parents.index') }}" class="mb-4 flex gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('guardians.search_placeholder') }}"
           class="w-full max-w-sm rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
    <button type="submit" class="rounded bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
        {{ __('guardians.search_button') }}
    </button>
</form>

<div class="overflow-hidden rounded-lg bg-white shadow">
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
                        <a href="{{ route('parents.show', $guardian) }}" class="text-indigo-700 hover:underline">{{ $guardian->name }}</a>
                    </td>
                    <td class="px-4 py-2">{{ $guardian->phone }}</td>
                    <td class="px-4 py-2">{{ $guardian->students_count }}</td>
                    <td class="px-4 py-2 text-end">
                        <a href="{{ route('parents.edit', $guardian) }}" class="text-slate-600 hover:text-indigo-700">{{ __('students.edit_button') }}</a>
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
