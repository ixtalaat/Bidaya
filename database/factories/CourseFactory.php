<?php

namespace Database\Factories;

use App\Enums\CourseStatus;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['Language', 'Math', 'Science', 'Arts', 'Sports']),
            'min_age' => 4,
            'max_age' => 12,
            'level' => fake()->randomElement(['Beginner', 'Intermediate', 'Advanced']),
            'duration_weeks' => 12,
            'sessions_count' => 24,
            'monthly_fee' => 2000,
            'status' => CourseStatus::Active,
        ];
    }
}
