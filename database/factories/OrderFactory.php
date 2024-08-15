<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'total' => 0,
            'status' => $this->faker->randomElement([OrderStatus::Pending->value, OrderStatus::Cancelled->value, OrderStatus::Completed->value]),
            'address' => $this->faker->address(),
        ];
    }
}
