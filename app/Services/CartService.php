<?php

namespace App\Services;

use App\Exceptions\CustomJsonException;
use App\Models\CartItem;
use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CartService
{
    /**
     * Validate the quantity of a product before updating the cart.
     *
     * @param int $productQuantity The available quantity of the product.
     * @param int $newQuantity The new quantity requested by the user.
     * @throws CustomJsonException If the requested quantity exceeds the available product quantity.
     * @return void
     */
    protected function validateQuantity($productQuantity, $newQuantity)
    {
        if ($newQuantity > $productQuantity) {
            throw new CustomJsonException('Insufficient product quantity', 400);
        }
    }

    /**
     * Update the user's cart with a new product or change the quantity of an existing item.
     *
     * @param int $productId The ID of the product.
     * @param int $userId The ID of the user.
     * @param int $quantityChange The change in quantity (can be positive or negative).
     * @return CartItem The updated cart item.
     * @throws CustomJsonException If the quantity is invalid or product does not exist.
     */
    public function updateCart($productId, $userId, $quantityChange): CartItem
    {
        $product = Product::findOrFail($productId);
        $cartItem = CartItem::with('product')->where('user_id', $userId)
                            ->where('product_id', $productId)
                            ->first();

        $newQuantity = $quantityChange;

        // If the cart item already exists, update its quantity
        if ($cartItem) {
            $newQuantity += $cartItem->quantity;
            $this->validateQuantity($product->quantity, $newQuantity);

            // Update and save the cart item
            $cartItem->quantity = $newQuantity;
            $cartItem->save();
            return $cartItem;
        }

        // If trying to remove an item that isn't in the cart, throw an exception
        if ($quantityChange <= 0) {
            throw new CustomJsonException('Cannot remove item that does not exist in cart', 400);
        }

        // Validate that the quantity is available before adding a new cart item
        $this->validateQuantity($product->quantity, $quantityChange);

        return CartItem::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => $quantityChange,
        ])->with('product')->first();
    }
}
