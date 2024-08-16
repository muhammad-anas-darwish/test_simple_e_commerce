<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\CustomJsonException;
use App\Jobs\ClearCartItems;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class OrderService
{
    /**
     * Get a paginated list of orders.
     *
     * @param int $countPerPage Number of orders per page.
     * @return LengthAwarePaginator Paginated list of orders.
     */
    public function getPaginatedOrders($countPerPage = 10): LengthAwarePaginator
    {
        return Order::paginate($countPerPage);
    }

    /**
     * Get a paginated list of orders for a specific user.
     *
     * @param int $userId The ID of the user.
     * @param int $countPerPage Number of orders per page.
     * @return LengthAwarePaginator Paginated list of user orders.
     */
    public function getPaginatedUserOrders($userId, $countPerPage = 10): LengthAwarePaginator
    {
        return Order::with('orderItems.product')->where('user_id', $userId)->paginate($countPerPage);
    }

    /**
     * Get cart items for a specific user.
     *
     * @param int $userId The ID of the user.
     * @return Builder Query builder for the user's cart items.
     * @throws CustomJsonException if the cart is empty.
     */
    private function getCartItems($userId): Builder
    {
        $cartItems = CartItem::with('product')->where('user_id', $userId);
        if (!$cartItems->exists()) {
            throw new CustomJsonException('Cart is empty', 400);
        }
        return $cartItems;
    }

    /**
     * Update the total price of an order.
     *
     * @param Order $order The order to update.
     * @param float $total The new total price.
     * @return void
     */
    private function updateOrderTotal($order, $total): void
    {
        $order['total'] = $total;
        $order->save();
    }

    /**
     * Convert cart items to order items and calculate the total price.
     *
     * @param Order $order The order to associate the items with.
     * @param Builder $cartItems The cart items to convert.
     * @return float The total price of the order.
     */
    private function convertChatItemsToOrderItems($order, Builder $cartItems)
    {
        $total = 0;
        $cartItems->each(function ($cartItem) use ($order, &$total) {
            OrderItem::create([
                'product_id' => $cartItem->product_id,
                'order_id' => $order->id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem['product']['price'],
            ]);
            $total += $cartItem->quantity * $cartItem['product']['price'];
        });
        return $total;
    }

    /**
     * Create a new order for a user.
     *
     * @param int $userId The ID of the user.
     * @param string $address The shipping address.
     * @return Order The newly created order.
     */
    private function createOrder($userId, $address): Order
    {
        return Order::create([
            'user_id' => $userId,
            'total' => 0,
            'status' => OrderStatus::Pending->value,
            'address' => $address,
        ]);
    }

    /**
     * Handle the checkout process for a user.
     *
     * @param int $userId The ID of the user.
     * @param string $address The shipping address.
     * @return Order The completed order.
     * @throws \Exception if an error occurs during checkout.
     */
    public function checkout($userId, $address): Order
    {
        try {
            DB::beginTransaction(); // Start the transaction

            // Get the user's cart items
            $cartItems = $this->getCartItems($userId);

            // Create a new order
            $order = $this->createOrder($userId, $address);

            // Convert cart items to order items and calculate total
            $total = $this->convertChatItemsToOrderItems($order, $cartItems);
            
            // Update the order's total price
            $this->updateOrderTotal($order, $total);

            // Queue a job to clear the user's cart items
            Queue::push(new ClearCartItems($userId)); 

            DB::commit(); // Commit the transaction
            return $order;
        }
        catch (\Exception $e) {
            DB::rollBack(); // Roll back the transaction in case of an error
            throw $e;
        }
    }


    /**
     * Change the status of an order.
     *
     * @param Order $order The order to update.
     * @param string $status The new status.
     * @return Order The updated order.
     */
    public function changeStatus($order, $status): Order
    {
        $order->status = $status;
        $order->save();

        return $order->load('orderItems.product');
    }

    /**
     * Retrieve detailed information about an order.
     *
     * @param Order $order The order to retrieve.
     * @return Order The order with its items and products loaded.
     */
    public function showOrder($order)
    {
        return $order->load('orderItems.product');
    }
}
