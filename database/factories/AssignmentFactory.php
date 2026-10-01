<?php

namespace Database\Factories;

use App\Enums\AssignmentStatus;
use App\Models\Assignment;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_id' => Group::factory(),
            'title' => fake()->randomElement(['Homework 1', 'Worksheet 2', 'Reading task']),
            'description' => fake()->sentence(),
            'due_date' => fake()->dateTimeBetween('now', '+2 weeks')->format('Y-m-d'),
            'max_score' => 10,
            'status' => AssignmentStatus::Published,
        ];
    }
}
