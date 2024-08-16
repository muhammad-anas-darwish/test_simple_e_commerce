<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    use ApiResponses;

    /**
     * Inject the OrderService dependency and require authentication.
     *
     * @param OrderService $orderService The service handling order operations.
     */
    public function __construct(protected OrderService $orderService) 
    { 
        $this->middleware('auth:api');
    }

    /**
     * Get a paginated list of the authenticated user's orders.
     *
     * @return JsonResponse Paginated list of the user's orders.
     */
    public function getUserOrders(): JsonResponse
    {
        $orders = $this->orderService->getPaginatedUserOrders(Auth::id(), 10);

        return $this->paginatedResponse($orders, OrderResource::class);
    }

    /**
     * Display a paginated list of all orders.
     *
     * @return JsonResponse Paginated list of all orders.
     */
    public function index()
    {
        $orders = $this->orderService->getPaginatedOrders(20);
        
        return $this->paginatedResponse($orders, OrderResource::class);
    }

    /**
     * Store a newly created order in storage (handle checkout).
     *
     * @param StoreOrderRequest $request The incoming request with order data.
     * @return JsonResponse The response containing the newly created order.
     */
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();

        $order = $this->orderService->checkout(Auth::id(), $data['address']);

        return $this->successResponse(new OrderResource($order), 'Checkout Successfully');
    }

    /**
     * Display the specified order.
     *
     * @param Order $order The order to display.
     * @return JsonResponse The response containing the order details.
     */
    public function show(Order $order)
    {
        if (Auth::id() !== $order->user_id && !Auth::user()->is_admin) {
            return $this->errorResponse('You are not authorized to view this order.', 403);
        }

        $order = $this->orderService->showOrder($order);
        return $this->successResponse(new OrderResource($order), 'Order retrieved successfully');
    }

    /**
     * Update the specified order's status in storage.
     *
     * @param UpdateOrderRequest $request The incoming request with the new status.
     * @param Order $order The order to update.
     * @return JsonResponse The response containing the updated order.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $data = $request->validated();
        $order = $this->orderService->changeStatus($order, $data['status']);
        return $this->successResponse(new OrderResource($order), 'Order status changed successfully');
    }
}
