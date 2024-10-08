<?php

namespace Tests\Feature\Product;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateProductTest extends TestCase
{
    /**
     * Test that an admin can create a product.
     * 
     * This test logs in as an admin and sends a POST request to create a new product. It then
     * asserts that the response status is 201 (Created) and that the response JSON contains the
     * correct product details. The test also verifies that the product was successfully inserted
     * into the database.
     */
    public function it_can_create_a_product()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $data = [
            'name' => 'Test Product',
            'description' => 'This is a test description.',
            'price' => 99.99,
            'quantity' => 10,
        ];

        $response = $this->actingAs($admin)->postJson('/api/products', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Resource created successfully',
                'data' => [
                    'name' => 'Test Product',
                    'description' => 'This is a test description.',
                    'price' => '99.99',
                    'quantity' => 10,
                ],
            ]);

        $this->assertDatabaseHas('products', $data);
    }
}
