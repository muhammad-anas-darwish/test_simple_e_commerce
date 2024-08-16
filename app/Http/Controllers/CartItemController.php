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

    /**
     * Inject the CartService dependency and require authentication.
     *
     * @param CartService $cartService The service handling cart operations.
     */
    public function __construct(protected CartService $cartService)
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a list of the authenticated user's cart items.
     *
     * @return JsonResponse The response containing the user's cart items.
     */
    public function index()
    {
        $cartItems = Auth::user()->cartItems()->with('product')->get();

        return $this->successResponse(CartItemResource::collection($cartItems), 'Items retrieved successfully');
    }

    /**
     * Update the cart by adding a new item or adjusting the quantity of an existing item.
     *
     * @param AddItemToCartRequest $request The incoming request with product and quantity data.
     * @return JsonResponse The response after updating the cart.
     */
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
