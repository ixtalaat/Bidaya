<?php

namespace App\Http\Controllers;

use App\Enums\GalleryType;
use App\Http\Requests\StoreGalleryItemRequest;
use App\Http\Requests\UpdateGalleryItemRequest;
use App\Models\GalleryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $items = GalleryItem::orderByDesc('id')->paginate(15);

        return view('galleries.index', ['items' => $items]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('galleries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGalleryItemRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['photo']);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('gallery-photos', 'public');
        }

        if (($data['type'] ?? null) !== GalleryType::Video->value) {
            $data['video_url'] = null;
        }

        $item = GalleryItem::create($data);

        return redirect()->route('galleries.show', $item)->with('status', __('gallery.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(GalleryItem $gallery): View
    {
        return view('galleries.show', ['item' => $gallery]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GalleryItem $gallery): View
    {
        return view('galleries.edit', ['item' => $gallery]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGalleryItemRequest $request, GalleryItem $gallery): RedirectResponse
    {
        $data = $request->validated();
        unset($data['photo']);

        if ($request->hasFile('photo')) {
            if ($gallery->photo_path !== null) {
                Storage::disk('public')->delete($gallery->photo_path);
            }

            $data['photo_path'] = $request->file('photo')->store('gallery-photos', 'public');
        }

        if (($data['type'] ?? null) !== GalleryType::Video->value) {
            $data['video_url'] = null;
        }

        $gallery->update($data);

        return redirect()->route('galleries.show', $gallery)->with('status', __('gallery.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GalleryItem $gallery): RedirectResponse
    {
        if ($gallery->photo_path !== null) {
            Storage::disk('public')->delete($gallery->photo_path);
        }

        $gallery->delete();

        return redirect()->route('galleries.index')->with('status', __('gallery.deleted'));
    }
}
