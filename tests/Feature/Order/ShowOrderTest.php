<?php

namespace Tests\Feature\Order;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowOrderTest extends TestCase
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
     * Test that a user can retrieve their own order.
     * 
     * This test creates an order for the authenticated user and mocks the order service's
     * showOrder method to return the requested order. It then sends a GET request to retrieve
     * the order and asserts that the response status is 200 and that the response JSON
     * contains the correct order details.
     */
    public function it_can_show_an_order()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $this->orderService->method('showOrder')
            ->willReturn($order);

        $response = $this->getJson(route('orders.show', $order->id));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Order retrieved successfully',
                'data' => (new OrderResource($order))->response()->getData(true)['data'],
            ]);
    }

    /**
     * Test that a user cannot view another user's order.
     * 
     * This test creates an order for a different user and then attempts to retrieve it
     * as the authenticated user. It asserts that the response status is 403 (Forbidden)
     * and that the response JSON contains an error message indicating that the user is
     * not authorized to view the order.
     */
    public function it_fails_to_show_another_users_order()
    {
        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson(route('orders.show', $order->id));

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'You are not authorized to view this order.',
            ]);
    }
}
