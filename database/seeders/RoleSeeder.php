<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(
            ['name' => Role::ADMIN],
            ['description' => 'Administrador del sistema con acceso total']
        );

        Role::firstOrCreate(
            ['name' => Role::VENDEDOR],
            ['description' => 'Vendedor con acceso a clientes, productos y pedidos']
        );
    }
}
