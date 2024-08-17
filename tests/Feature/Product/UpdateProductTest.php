<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateProductTest extends TestCase
{
    /**
     * Test that an admin can update a product's details.
     * 
     * This test logs in as an admin and sends a PUT request to update an existing product's
     * details. It then asserts that the response status is 200 and that the response JSON
     * contains the updated product details. The test also verifies that the product was
     * successfully updated in the database.
     */
    public function it_can_update_a_product()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = Product::factory()->create();

        $data = [
            'name' => 'Updated Product',
            'description' => 'Updated description.',
            'price' => 49.99,
            'quantity' => 5,
        ];

        $response = $this->actingAs($admin)->putJson("/api/products/{$product->id}", $data);

        $response->assertStatus(200)->assertJson([
            'message' => 'Resource updated successfully',
            'data' => [
                'id' => $product->id,
                'name' => 'Updated Product',
                'description' => 'Updated description.',
                'price' => format_price(49.99),
                'quantity' => 5,
            ],
        ]);

        $this->assertDatabaseHas('products', $data);
    }
}
