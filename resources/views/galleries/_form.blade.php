<div class="space-y-4">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="type" class="mb-1 block text-sm font-medium">{{ __('gallery.type') }}</label>
            <select id="type" name="type" required
                    class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                @foreach (\App\Enums\GalleryType::cases() as $option)
                    <option value="{{ $option->value }}" @selected(old('type', $item->type?->value) === $option->value)>{{ $option->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2 pb-2">
            <input id="published" type="checkbox" name="published" value="1" @checked(old('published', $item->published ?? true))
                   class="h-5 w-5 rounded border-2 border-slate-900">
            <label for="published" class="text-sm font-medium">{{ __('gallery.published') }}</label>
        </div>
    </div>

    <div>
        <label for="title" class="mb-1 block text-sm font-medium">{{ __('gallery.item_title') }}</label>
        <input id="title" type="text" name="title" value="{{ old('title', $item->title) }}" required
               class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="mb-1 block text-sm font-medium">{{ __('gallery.description') }}</label>
        <textarea id="description" name="description" rows="2"
                  class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">{{ old('description', $item->description) }}</textarea>
    </div>

    <div>
        <label for="photo" class="mb-1 block text-sm font-medium">{{ __('gallery.photo') }}</label>
        @if ($item->photo_path)
            <p class="mb-2">
                <img src="{{ Storage::url($item->photo_path) }}" alt="" class="h-24 rounded-xl border-2 border-slate-900 object-cover">
            </p>
        @endif
        <input id="photo" type="file" name="photo" accept="image/*"
               class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        @error('photo')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="video_url" class="mb-1 block text-sm font-medium">{{ __('gallery.video_url') }}</label>
        <input id="video_url" type="url" name="video_url" value="{{ old('video_url', $item->video_url) }}" dir="ltr"
               placeholder="https://www.youtube.com/watch?v=…"
               class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        <p class="mt-1 text-xs text-slate-500">{{ __('gallery.video_hint') }}</p>
        @error('video_url')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
