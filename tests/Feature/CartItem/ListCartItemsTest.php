<?php

namespace Tests\Feature\CartItem;

use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListCartItemsTest extends TestCase
{
    /**
     * Test the index method of CartItemController.
     */
    public function test_index()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $product = Product::factory()->create();
        $cartItem = CartItem::factory()->create(['user_id' => $user->id, 'product_id' => $product->id]);
        
        // Call the index route
        $response = $this->getJson(route('cart.items'));

        $expectedData = [
            [
                'id' => $cartItem->id,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => format_price($product->price),
                    'quantity' => $product->quantity,
                    'created_at' => $product->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $product->updated_at->format('Y-m-d H:i:s'),
                ],
                'quantity' => $cartItem->quantity,
                'created_at' => $cartItem->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $cartItem->updated_at->format('Y-m-d H:i:s'),
            ]
        ];

        // Assert the response is successful and contains the cart items with product details
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Items retrieved successfully',
                'data' => $expectedData,
            ]);
    }
}
