<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => Role::ADMIN, 'description' => 'Admin']);
    Role::create(['name' => Role::VENDEDOR, 'description' => 'Vendedor']);
});

test('usuario puede hacer login correctamente', function () {
    $user = User::factory()->create([
        'email' => 'login@example.com',
        'password' => Hash::make('password123'),
    ]);
    $user->roles()->attach(Role::where('name', Role::VENDEDOR)->first());

    $response = $this->postJson('/api/auth/login', [
        'email' => 'login@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user' => ['id', 'name', 'email'],
                'token',
                'token_type',
            ],
        ])
        ->assertJson([
            'success' => true,
            'data' => [
                'user' => ['email' => 'login@example.com'],
                'token_type' => 'Bearer',
            ],
        ]);

    expect($response->json('data.token'))->not->toBeEmpty();
});

test('login falla con credenciales incorrectas', function () {
    User::factory()->create(['email' => 'user@example.com']);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'user@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422);
});

test('login falla sin email', function () {
    $response = $this->postJson('/api/auth/login', [
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
