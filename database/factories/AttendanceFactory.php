<?php

namespace Database\Factories;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'session_id' => Session::factory(),
            'enrollment_id' => Enrollment::factory(),
            'status' => fake()->randomElement(AttendanceStatus::cases()),
        ];
    }
}
