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
    protected function validateQuantity($productQuantity, $newQuantity)
    {
        if ($newQuantity > $productQuantity) {
            throw new CustomJsonException('Insufficient product quantity', 400);
        }
    }

    public function updateCart($productId, $userId, $quantityChange): CartItem
    {
        $product = Product::findOrFail($productId);
        $cartItem = CartItem::with('product')->where('user_id', $userId)
                            ->where('product_id', $productId)
                            ->first();

        $newQuantity = $quantityChange;
        if ($cartItem) {
            $newQuantity += $cartItem->quantity;
            $this->validateQuantity($product->quantity, $newQuantity);

            $cartItem->quantity = $newQuantity;
            $cartItem->save();
            return $cartItem;
        }

        if ($quantityChange <= 0) {
            throw new CustomJsonException('Cannot remove item that does not exist in cart', 400);
        }

        $this->validateQuantity($product->quantity, $quantityChange);

        return CartItem::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => $quantityChange,
        ])->with('product')->first();
    }
}
