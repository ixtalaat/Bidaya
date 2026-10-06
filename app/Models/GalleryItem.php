<?php

namespace App\Models;

use App\Enums\GalleryType;
use Database\Factories\GalleryItemFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    /** @use HasFactory<GalleryItemFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'description',
        'photo_path',
        'video_url',
        'published',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => GalleryType::class,
            'published' => 'boolean',
        ];
    }

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('published', true);
    }

    /**
     * Extract a YouTube video ID from watch, share, embed, and Shorts URLs.
     */
    public static function extractVideoId(?string $url): ?string
    {
        if ($url === null || trim($url) === '') {
            return null;
        }

        $patterns = [
            '/youtu\.be\/([A-Za-z0-9_-]{11})/',
            '/youtube\.com\/watch\?.*v=([A-Za-z0-9_-]{11})/',
            '/youtube\.com\/embed\/([A-Za-z0-9_-]{11})/',
            '/youtube\.com\/shorts\/([A-Za-z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches) === 1) {
                return $matches[1];
            }
        }

        return null;
    }

    protected function embedUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $videoId = self::extractVideoId($this->video_url);

            return $videoId === null ? null : "https://www.youtube.com/embed/{$videoId}";
        });
    }
}
