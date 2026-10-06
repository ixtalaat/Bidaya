@extends('layouts.app')

@section('title', __('gallery.manage_title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="font-display text-xl font-bold">{{ __('gallery.manage_title') }}</h1>
    <a href="{{ route('galleries.create') }}" class="clay-btn cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
        {{ __('gallery.create_button') }}
    </a>
</div>

<div class="overflow-hidden clay-card">
    <table class="w-full text-sm">
        <thead class="bg-background">
            <tr>
                <th class="px-4 py-2 text-start font-medium">{{ __('gallery.item_title') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('gallery.type') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('gallery.published') }}</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2">
                        <a href="{{ route('galleries.show', $item) }}" class="font-semibold text-primary hover:underline">{{ $item->title }}</a>
                    </td>
                    <td class="px-4 py-2">{{ $item->type->label() }}</td>
                    <td class="px-4 py-2">{{ $item->published ? '✓' : '—' }}</td>
                    <td class="px-4 py-2 text-end">
                        <a href="{{ route('galleries.edit', $item) }}" class="font-semibold text-slate-600 hover:text-primary">{{ __('students.edit_button') }}</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-slate-500">{{ __('gallery.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $items->links() }}</div>
@endsection
