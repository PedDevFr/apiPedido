<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => Role::ADMIN, 'description' => 'Admin']);
    Role::create(['name' => Role::VENDEDOR, 'description' => 'Vendedor']);
});

test('usuario puede registrarse correctamente', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Juan Pérez',
        'email' => 'juan@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user' => ['id', 'name', 'email', 'roles'],
                'token',
                'token_type',
            ],
        ])
        ->assertJson([
            'success' => true,
            'data' => [
                'user' => [
                    'name' => 'Juan Pérez',
                    'email' => 'juan@example.com',
                ],
                'token_type' => 'Bearer',
            ],
        ]);

    $this->assertDatabaseHas('users', ['email' => 'juan@example.com']);
    $user = User::where('email', 'juan@example.com')->first();
    expect($user->hasRole(Role::VENDEDOR))->toBeTrue();
});

test('registro falla sin nombre', function () {
    $response = $this->postJson('/api/auth/register', [
        'email' => 'juan@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('registro falla con email duplicado', function () {
    User::factory()->create(['email' => 'existente@example.com']);

    $response = $this->postJson('/api/auth/register', [
        'name' => 'Juan Pérez',
        'email' => 'existente@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
