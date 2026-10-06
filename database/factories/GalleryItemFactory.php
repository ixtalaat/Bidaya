<?php

namespace Database\Factories;

use App\Enums\GalleryType;
use App\Models\GalleryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GalleryItem>
 */
class GalleryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => GalleryType::Photo,
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'published' => true,
        ];
    }
}
