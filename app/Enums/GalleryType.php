<?php

namespace App\Enums;

enum GalleryType: string
{
    case Photo = 'photo';
    case Video = 'video';

    public function label(): string
    {
        return __("enums.gallery_types.{$this->value}");
    }
}
