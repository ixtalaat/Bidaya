<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentResult>
 */
class AssessmentResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assessment_id' => Assessment::factory(),
            'enrollment_id' => Enrollment::factory(),
            'score' => fake()->numberBetween(0, 20),
        ];
    }
}
