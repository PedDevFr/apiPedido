<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'sku' => fake()->unique()->regexify('[A-Z0-9]{8}'),
            'description' => fake()->optional(0.7)->paragraph(),
            'price' => fake()->randomFloat(2, 1, 500),
            'stock' => fake()->numberBetween(0, 200),
        ];
    }
}
