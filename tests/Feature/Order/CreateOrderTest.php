<?php

namespace Tests\Feature\Order;

use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    protected $orderService;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and set it as authenticated
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');

        // // Mock the OrderService
        $this->orderService = $this->createMock(OrderService::class);
        $this->app->instance(OrderService::class, $this->orderService);
    }

    /**
     * Test that a user can create an order.
     * 
     * This test mocks the order service's checkout method to return a newly created order
     * for the authenticated user. It then sends a POST request to create the order and asserts
     * that the response status is 200 and that the response JSON has the correct structure,
     * including the order details.
     */
    public function it_can_create_an_order()
    {
        $this->orderService->method('checkout')
            ->willReturn(Order::factory()->create([
                'user_id' => $this->user->id
            ]));

        $response = $this->postJson(route('orders.store'), [
            'address' => '123 Main St',
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'status',
                'total', 
                'address', 
                'created_at',
                'updated_at'
            ],
        ]);
    }
}
