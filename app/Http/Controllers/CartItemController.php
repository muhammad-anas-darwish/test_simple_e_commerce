<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddItemToCartRequest;
use App\Http\Resources\CartItemResource;
use App\Services\CartService;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CartItemController extends Controller
{
    use ApiResponses;

    public function __construct(protected CartService $cartService)
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $cartItems = Auth::user()->cartItems()->with('product')->get();

        return $this->successResponse(CartItemResource::collection($cartItems), 'Items retrieved successfully');
    }

    public function updateCart(AddItemToCartRequest $request): JsonResponse
    {
        $data = $request->validated();
        // Call the service to update the cart
        $cartItem = $this->cartService->updateCart(
            $data['product_id'],
            Auth::id(),
            $data['quantity_change']
        );

        if ($data['quantity_change'] > 0) {
            return $this->successResponse(new CartItemResource($cartItem), 'item added to cart successfully', 201);
        }

        if ($cartItem->quantity <= 0) {
            return $this->noContentResponse();
        }

        return $this->successResponse(new CartItemResource($cartItem), 'Item quantity in cart has been decreased');
    }
}
