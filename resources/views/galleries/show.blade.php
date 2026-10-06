@extends('layouts.app')

@section('title', __('gallery.show_title'))

@section('content')
<div class="mx-auto max-w-2xl clay-card overflow-hidden p-6">
    <h1 class="font-display text-xl font-bold">{{ $item->title }}</h1>
    <p class="mt-1 text-sm text-slate-500">{{ $item->type->label() }} — {{ $item->published ? __('gallery.published') : '—' }}</p>

    @if ($item->description)
        <p class="mt-3 text-sm">{{ $item->description }}</p>
    @endif

    @if ($item->type === \App\Enums\GalleryType::Photo && $item->photo_path)
        <img src="{{ Storage::url($item->photo_path) }}" alt="{{ $item->title }}"
             class="mt-4 w-full rounded-2xl border-[3px] border-slate-900 object-cover">
    @elseif ($item->type === \App\Enums\GalleryType::Video && $item->embed_url)
        <div class="mt-4 overflow-hidden rounded-2xl border-[3px] border-slate-900">
            <iframe src="{{ $item->embed_url }}" title="{{ $item->title }}"
                    class="aspect-video w-full" referrerpolicy="no-referrer"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
        </div>
    @endif

    <div class="mt-6 flex gap-2">
        <a href="{{ route('galleries.edit', $item) }}" class="clay-btn cursor-pointer bg-white px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('students.edit_button') }}
        </a>
        <form method="POST" action="{{ route('galleries.destroy', $item) }}" data-confirm="{{ __('gallery.delete_confirm') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="clay-btn cursor-pointer bg-red-600 px-4 py-2 text-sm font-extrabold text-white">
                {{ __('gallery.delete_button') }}
            </button>
        </form>
    </div>
</div>
@endsection
