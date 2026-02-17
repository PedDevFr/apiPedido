<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([Role::ADMIN, Role::VENDEDOR]),
            'description' => fake()->sentence(),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => Role::ADMIN,
            'description' => 'Administrador del sistema',
        ]);
    }

    public function vendedor(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => Role::VENDEDOR,
            'description' => 'Vendedor',
        ]);
    }
}
