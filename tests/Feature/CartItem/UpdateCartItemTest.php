<?php

namespace Tests\Feature\CartItem;

use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateCartItemTest extends TestCase
{
    /**
     * Test the updateCart method of CartItemController.
     */
    public function test_update_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $product = Product::factory()->create(['quantity' => 10]);
        $cartItem = CartItem::factory()->create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 2])
            ->load('product');
        $cartItem['quantity'] += 2;

        // Call the updateCart route
        $response = $this->postJson(route('cart.update'), [
            'product_id' => $product->id,
            'quantity_change' => 2,
        ]);

        // Assert the response is successful and the cart item is returned
        $response->assertStatus(201)->assertJson([
            'status' => 'success',
            'message' => 'item added to cart successfully',
            'data' => (new CartItemResource($cartItem))->response()->getData(true)['data'],
        ]);
    }
}
