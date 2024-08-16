<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test retrieving a list of products.
     *
     * @return void
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
