<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );
        if (! $admin->hasRole(Role::ADMIN)) {
            $admin->roles()->attach(Role::where('name', Role::ADMIN)->first());
        }

        $vendedor = User::firstOrCreate(
            ['email' => 'vendedor@example.com'],
            ['name' => 'Vendedor', 'password' => Hash::make('password')]
        );
        if (! $vendedor->hasRole(Role::VENDEDOR)) {
            $vendedor->roles()->attach(Role::where('name', Role::VENDEDOR)->first());
        }

        User::factory(8)->create()->each(function (User $user) {
            $user->roles()->attach(Role::where('name', Role::VENDEDOR)->first());
        });
    }
}
