<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'date_of_birth' => fake()->dateTimeBetween('-14 years', '-4 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(Gender::cases()),
            'school' => fake()->words(2, true),
            'grade_level' => 'Grade '.fake()->numberBetween(1, 6),
            'status' => StudentStatus::Active,
        ];
    }
}
