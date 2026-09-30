<?php

namespace Database\Factories;

use App\Enums\GroupStatus;
use App\Models\Course;
use App\Models\Group;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'teacher_id' => Teacher::factory(),
            'name' => 'Group '.fake()->lexify('?'),
            'capacity' => 15,
            'classroom' => 'Room '.fake()->numberBetween(1, 10),
            'start_date' => fake()->date(),
            'end_date' => fake()->dateTimeBetween('+2 months', '+6 months')->format('Y-m-d'),
            'status' => GroupStatus::Active,
        ];
    }
}
