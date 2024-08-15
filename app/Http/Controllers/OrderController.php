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

    public function __construct(protected OrderService $orderService) 
    { 
        $this->middleware('auth:api');
    }

    public function getUserOrders(): JsonResponse
    {
        $orders = $this->orderService->getPaginatedUserOrders(Auth::id(), 10);

        return $this->paginatedResponse($orders, OrderResource::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = $this->orderService->getPaginatedOrders(20);
        
        return $this->paginatedResponse($orders, OrderResource::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();

        $order = $this->orderService->checkout(Auth::id(), $data['address']);

        return $this->successResponse(new OrderResource($order), 'Checkout Successfully');
    }

    /**
     * Display the specified resource.
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
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $data = $request->validated();
        $order = $this->orderService->changeStatus($order, $data['status']);
        return $this->successResponse(new OrderResource($order), 'Order status changed successfully');
    }
}
