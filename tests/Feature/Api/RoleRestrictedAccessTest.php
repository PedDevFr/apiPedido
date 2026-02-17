<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => Role::ADMIN, 'description' => 'Admin']);
    Role::create(['name' => Role::VENDEDOR, 'description' => 'Vendedor']);
});

test('usuario con rol vendedor puede acceder a clientes', function () {
    $user = User::factory()->create();
    $user->roles()->attach(Role::where('name', Role::VENDEDOR)->first());

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/clients');

    $response->assertStatus(200);
});

test('usuario con rol admin puede acceder a clientes', function () {
    $user = User::factory()->create();
    $user->roles()->attach(Role::where('name', Role::ADMIN)->first());

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/clients');

    $response->assertStatus(200);
});

test('usuario sin rol asignado no puede acceder a recursos protegidos', function () {
    $user = User::factory()->create();
    // No asignar ningún rol

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/clients');

    $response->assertStatus(403)
        ->assertJson(['success' => false]);

    expect($response->json('message'))->toContain('permisos');
});

test('usuario no autenticado no puede acceder a recursos protegidos', function () {
    $response = $this->getJson('/api/clients');

    $response->assertStatus(401);
});
