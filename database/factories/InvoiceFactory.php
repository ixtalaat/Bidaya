<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Enrollment;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $periodStart = fake()->dateTime();

        return [
            'enrollment_id' => Enrollment::factory(),
            'period_start' => $periodStart->format('Y-m-d'),
            'period_end' => (clone $periodStart)->modify('+1 month -1 day')->format('Y-m-d'),
            'amount' => 2000,
            'discount_amount' => 0,
            'total' => 2000,
            'status' => InvoiceStatus::Pending,
            'due_date' => $periodStart->format('Y-m-d'),
        ];
    }
}
