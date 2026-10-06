@extends('layouts.app')

@section('title', __('gallery.create_title'))

@section('content')
<div class="mx-auto max-w-2xl clay-card p-6">
    <h1 class="mb-6 font-display text-xl font-bold">{{ __('gallery.create_title') }}</h1>

    <form method="POST" action="{{ route('galleries.store') }}" enctype="multipart/form-data">
        @csrf
        @include('galleries._form', ['item' => new \App\Models\GalleryItem()])

        <button type="submit" class="clay-btn mt-6 w-full cursor-pointer bg-secondary px-4 py-2 text-sm font-extrabold text-slate-900">
            {{ __('gallery.save_button') }}
        </button>
    </form>
</div>
@endsection
