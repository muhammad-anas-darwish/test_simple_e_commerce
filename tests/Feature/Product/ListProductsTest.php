<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListProductsTest extends TestCase
{
    /**
     * Test that a user can list their own orders.
     * 
     * This test attempts to retrieve the authenticated user's orders.
     * It sends a GET request to the appropriate endpoint and asserts that
     * the response status is 200. The response JSON structure is validated
     * to ensure it includes the expected fields such as `id`, `status`, 
     * `total`, `address`, `created_at`, and `updated_at` within the `data` 
     * and pagination fields.
     */
    public function testCanRetrieveProducts()
    {
        // Create some products
        Product::factory()->count(5)->create();

        // Send a GET request to the /products endpoint
        $response = $this->getJson('/api/products');

        // Assert that the response status is 200
        $response->assertStatus(200);

        // Assert that the response contains data
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'items' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'pagination' => [
                    'total',
                    'count',
                    'per_page',
                    'current_page',
                    'total_pages',
                    'next_page_url',
                    'previous_page_url',
                ],
            ],
        ]);
    }
}
