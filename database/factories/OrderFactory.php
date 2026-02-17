<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'user_id' => User::factory(),
            'order_number' => Order::generateOrderNumberForFactory(),
            'order_date' => fake()->dateTimeBetween('-1 year'),
            'status' => fake()->randomElement(Order::STATUSES),
            'total' => 0,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }
}
