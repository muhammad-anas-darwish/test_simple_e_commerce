<?php

namespace Tests\Feature\Order;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateOrderTest extends TestCase
{
    protected $orderService;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin and set it as authenticated admin
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        $this->user = $admin;

        // Mock the OrderService
        $this->orderService = $this->createMock(OrderService::class);
        $this->app->instance(OrderService::class, $this->orderService);
    }
    
    /**
     * Test that a user can update the status of their order.
     * 
     * This test creates an order for the authenticated user and mocks the order service's
     * changeStatus method to return the updated order. It then sends a PUT request to update
     * the order status and asserts that the response status is 200 and that the response JSON
     * contains the correct data, including the updated order details.
     */
    public function it_can_update_order_status()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $this->orderService->method('changeStatus')
            ->willReturn($order);

        $response = $this->putJson(route('orders.update', $order->id), [
            'status' => 'completed',
        ]);

        $order->refresh();

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Order status changed successfully',
                'data' => (new OrderResource($order))->response()->getData(true)['data'],
            ]);
    }
}
