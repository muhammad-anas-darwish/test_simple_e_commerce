<?php

namespace Tests\Feature\Order;

use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListUserOrdersTest extends TestCase
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
     * Test that a user can retrieve a paginated list of their orders.
     * 
     * This test creates multiple orders for the authenticated user and mocks the order service's
     * getPaginatedUserOrders method to return a paginated list of these orders. It then sends
     * a GET request to retrieve the list and asserts that the response status is 200 and that
     * the response JSON has the correct structure, including order items and pagination details.
     */
    public function it_can_get_user_orders()
    {
        Order::factory()
            ->count(3)
            ->create(['user_id' => $this->user->id]);
        $orders = Order::where('user_id', $this->user->id);

        $this->orderService->method('getPaginatedUserOrders')
            ->willReturn($orders->paginate(10));

        $response = $this->getJson(route('orders.getUserOrders'));

        $data = [
            'status',
            'message',
            'data' => [
                'items' => [[
                    'id',
                    'status',
                    'total', 
                    'address', 
                    'created_at',
                    'updated_at'
                ]],
                'pagination' => [
                    'total', 
                    'count', 
                    'per_page', 
                    'current_page', 
                    'total_pages', 
                    'next_page_url', 
                    'previous_page_url'
                ]
            ]
        ];

        $response->assertStatus(200)->assertJsonStructure($data);
    }
}
