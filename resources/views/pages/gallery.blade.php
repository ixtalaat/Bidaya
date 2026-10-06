@extends('layouts.app')

@section('title', __('gallery.title'))

@section('content')
<div class="mx-auto max-w-3xl text-center">
    <h1 class="font-display text-3xl font-bold text-slate-900">{{ __('gallery.title') }}</h1>
    <p class="mt-2 text-slate-600">{{ __('gallery.subtitle') }}</p>
</div>

<div class="mt-6 flex justify-center gap-2">
    <a href="{{ route('gallery') }}" class="clay-chip px-4 py-1.5 text-sm font-extrabold {{ $activeType === null ? 'bg-slate-900 text-white' : 'bg-white text-slate-900' }}">{{ __('gallery.all') }}</a>
    <a href="{{ route('gallery', ['type' => 'photo']) }}" class="clay-chip px-4 py-1.5 text-sm font-extrabold {{ $activeType?->value === 'photo' ? 'bg-slate-900 text-white' : 'bg-white text-slate-900' }}">{{ __('gallery.photos') }}</a>
    <a href="{{ route('gallery', ['type' => 'video']) }}" class="clay-chip px-4 py-1.5 text-sm font-extrabold {{ $activeType?->value === 'video' ? 'bg-slate-900 text-white' : 'bg-white text-slate-900' }}">{{ __('gallery.videos') }}</a>
</div>

<div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3">
    @forelse ($items as $item)
        <div class="reveal clay-card overflow-hidden">
            @if ($item->type === \App\Enums\GalleryType::Photo && $item->photo_path)
                <button type="button" data-lightbox="{{ Storage::url($item->photo_path) }}" data-caption="{{ $item->title }}"
                        class="block w-full cursor-pointer" aria-label="{{ $item->title }}">
                    <img src="{{ Storage::url($item->photo_path) }}" alt="{{ $item->title }}" loading="lazy"
                         class="h-52 w-full border-b-[3px] border-slate-900 object-cover">
                </button>
            @elseif ($item->type === \App\Enums\GalleryType::Video && $item->embed_url)
                <div class="border-b-[3px] border-slate-900">
                    <iframe src="{{ $item->embed_url }}" title="{{ $item->title }}" loading="lazy"
                            class="h-52 w-full" referrerpolicy="no-referrer"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                </div>
            @endif
            <div class="p-4">
                <h2 class="font-display font-bold text-slate-900">{{ $item->title }}</h2>
                @if ($item->description)
                    <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $item->description }}</p>
                @endif
            </div>
        </div>
    @empty
        <p class="clay-card col-span-full p-6 text-center text-sm text-slate-500">{{ __('gallery.empty') }}</p>
    @endforelse
</div>

<div class="mt-6">{{ $items->links() }}</div>
@endsection
