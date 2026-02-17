<?php

use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => Role::ADMIN, 'description' => 'Admin']);
    Role::create(['name' => Role::VENDEDOR, 'description' => 'Vendedor']);
});

test('usuario autenticado puede crear un pedido', function () {
    $user = User::factory()->create();
    $user->roles()->attach(Role::where('name', Role::VENDEDOR)->first());

    $client = Client::factory()->create();
    $product1 = Product::factory()->create(['price' => 10.50]);
    $product2 = Product::factory()->create(['price' => 25.00]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/orders', [
            'client_id' => $client->id,
            'order_date' => now()->format('Y-m-d'),
            'products' => [
                ['product_id' => $product1->id, 'quantity' => 2, 'price' => 10.50],
                ['product_id' => $product2->id, 'quantity' => 1, 'price' => 25.00],
            ],
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'order_number',
                'client_id',
                'order_date',
                'status',
                'total',
                'products',
            ],
        ])
        ->assertJson([
            'success' => true,
            'data' => [
                'client_id' => $client->id,
                'total' => 46.00, // 2*10.50 + 1*25
            ],
        ]);

    $this->assertDatabaseHas('orders', ['client_id' => $client->id]);
    $order = Order::where('client_id', $client->id)->first();
    expect($order->products)->toHaveCount(2);
});

test('crear pedido requiere autenticación', function () {
    $client = Client::factory()->create();
    $product = Product::factory()->create();

    $response = $this->postJson('/api/orders', [
        'client_id' => $client->id,
        'order_date' => now()->format('Y-m-d'),
        'products' => [
            ['product_id' => $product->id, 'quantity' => 1, 'price' => $product->price],
        ],
    ]);

    $response->assertStatus(401);
});

test('crear pedido falla sin productos', function () {
    $user = User::factory()->create();
    $user->roles()->attach(Role::where('name', Role::VENDEDOR)->first());
    $client = Client::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/orders', [
            'client_id' => $client->id,
            'order_date' => now()->format('Y-m-d'),
            'products' => [],
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['products']);
});
