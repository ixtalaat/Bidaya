<?php

namespace Database\Factories;

use App\Enums\SessionStatus;
use App\Models\Group;
use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Session>
 */
class SessionFactory extends Factory
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
            'teacher_id' => fn (array $attributes) => Group::find($attributes['group_id'])->teacher_id,
            'date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'start_time' => '17:00',
            'end_time' => '18:00',
            'classroom' => null,
            'status' => SessionStatus::Scheduled,
        ];
    }
}
