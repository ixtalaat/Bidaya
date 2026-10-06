<?php

namespace App\Http\Requests;

use App\Enums\GalleryType;
use App\Models\GalleryItem;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateGalleryItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(GalleryType::class)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'published' => ['boolean'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->any()) {
                    return;
                }

                $type = $this->enum('type', GalleryType::class);
                $gallery = $this->route('gallery');

                if ($type === GalleryType::Photo && ! $this->hasFile('photo') && $gallery->photo_path === null) {
                    $validator->errors()->add('photo', __('gallery.photo_required'));
                }

                if ($type === GalleryType::Video
                    && GalleryItem::extractVideoId($this->input('video_url')) === null) {
                    $validator->errors()->add('video_url', __('gallery.video_url_invalid'));
                }
            },
        ];
    }
}
