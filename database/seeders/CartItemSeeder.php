<?php

namespace Database\Seeders;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CartItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::pluck('id')->toArray();
        $users = User::all();

        $users->each(function ($user) use ($products) {
            CartItem::factory(rand(1, 6))->create([
                'user_id' => $user,
                'product_id' => array_rand($products, 1),
            ]);
        });
    }
}
