<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowProductTest extends TestCase
{
    /**
     * Test that a user can retrieve a product by its ID.
     * 
     * This test creates a product in the database and then sends a GET request to retrieve
     * the product by its ID. It asserts that the response status is 200 and that the response
     * JSON contains the correct product details.
     */
    public function it_can_show_a_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                "status" => "success",
                'message' => 'Resource retrieved successfully',
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => format_price($product->price),
                    'quantity' => $product->quantity,
                ],
            ]);
    }
}
