<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        Order::factory(15)->create()->each(function (Order $order) use ($products) {
            $orderProducts = $products->random(rand(1, 5));
            $syncData = [];
            $total = 0;

            foreach ($orderProducts as $product) {
                $qty = rand(1, 5);
                $price = $product->price;
                $syncData[$product->id] = [
                    'quantity' => $qty,
                    'price' => $price,
                ];
                $total += $qty * $price;
            }

            $order->products()->sync($syncData);
            $order->update(['total' => $total]);
        });
    }
}
