<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();
        
        $users->each(function($user) use ($products) {
            for ($i = 0; $i < rand(1, 3); ++$i) {
                $order = Order::factory(1)->create(['user_id' => $user->id]);

                $newProducts = array_rand($products->toArray(), rand(2, 5));

                for ($j = 0; $j < count($newProducts); ++$j) {
                    $orderItem = OrderItem::create([
                        'order_id' => $order[0]['id'],
                        'product_id' => $products[$newProducts[$j]]['id'],
                        'quantity' => rand(1, 200),
                        'price' => $products[$newProducts[$j]]['price'],
                    ]);
                    $products[$newProducts[$j]]['quantity'] -= $orderItem['quantity'];
                }
                $order[0]->save();
            }
        });
    }
}
