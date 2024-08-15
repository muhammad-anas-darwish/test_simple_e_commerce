<?php

namespace App\Observers;

use App\Exceptions\CustomJsonException;
use App\Models\OrderItem;
use App\Models\Product;
use Exception;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "creating" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function creating(OrderItem $orderItem)
    {
        $product = Product::find($orderItem->product_id);

        if (!$product) {
            // abort(404, "Product not found");
            throw new CustomJsonException("Product not found", 404);

        }

        if ($orderItem->quantity > $product->quantity) {
            // abort(400, "Insufficient quantity available for product {$product->name}");
            throw new CustomJsonException("Insufficient quantity available for product {$product->name}", 400);
        }

        $product->decrement('quantity', $orderItem->quantity);
    }

    /**
     * Handle the OrderItem "created" event.
     */
    public function created(OrderItem $orderItem): void
    {
        //
    }

    /**
     * Handle the OrderItem "updated" event.
     */
    public function updated(OrderItem $orderItem): void
    {
        //
    }

    /**
     * Handle the OrderItem "deleted" event.
     */
    public function deleted(OrderItem $orderItem): void
    {
        //
    }

    /**
     * Handle the OrderItem "restored" event.
     */
    public function restored(OrderItem $orderItem): void
    {
        //
    }

    /**
     * Handle the OrderItem "force deleted" event.
     */
    public function forceDeleted(OrderItem $orderItem): void
    {
        //
    }
}
