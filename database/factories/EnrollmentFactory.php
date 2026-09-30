<?php

namespace Database\Factories;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'group_id' => Group::factory(),
            'enrollment_date' => fake()->date(),
            'start_date' => fake()->date(),
            'monthly_fee' => 2000,
            'discount_amount' => 0,
            'status' => EnrollmentStatus::Active,
        ];
    }
}
