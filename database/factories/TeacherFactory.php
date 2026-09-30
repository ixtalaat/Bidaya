<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Enums\TeacherStatus;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Teacher>
 */
class TeacherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->withRole(Role::Teacher),
            'specialization' => fake()->randomElement(['English', 'Math', 'Science', 'Art']),
            'qualification' => fake()->sentence(3),
            'hire_date' => fake()->date(),
            'status' => TeacherStatus::Active,
        ];
    }
}
