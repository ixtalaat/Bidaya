<?php

namespace Database\Factories;

use App\Enums\AssessmentType;
use App\Models\Assessment;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assessment>
 */
class AssessmentFactory extends Factory
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
            'name' => fake()->randomElement(['Quiz 1', 'Midterm Exam', 'Homework 3']),
            'type' => fake()->randomElement(AssessmentType::cases()),
            'max_score' => 20,
            'assessment_date' => fake()->date(),
        ];
    }
}
